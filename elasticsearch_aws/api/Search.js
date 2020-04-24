const express = require('express');
const Secrets = require('../config/Secrets');
const Router = require('../config/Router');
const ES = require('../helpers/ESConnection');
const _ = require('lodash');

const router = express.Router(Router);

function repairNumber(number) {
    let n = number.toString();

    return n.length === 1 ? `0${n}` : n;
}

function format(date) {
    return date.getFullYear().toString() + '-' + repairNumber(date.getMonth() + 1) + '-' + repairNumber(date.getDate());
}

function addMissingWords(words, missing) {
    const max = _.maxBy(words, 'length').length;

    for (let i = 0; i < words.length; i++) {
        let oneWords = words[i];

        for (let j = oneWords.length; j < max; j++) {
            words[i].push(words[i][0] || missing[i]);
        }
    }

    return words;
}

function allPossibleCases(arr) {
    if (arr.length === 1) {
        return arr[0];
    } else {
        let result = [];
        let allCasesOfRest = allPossibleCases(arr.slice(1));

        for (let i = 0; i < allCasesOfRest.length; i++) {
            for (let j = 0; j < arr[0].length; j++) {
                result.push(arr[0][j] + ' ' + allCasesOfRest[i]);
            }
        }

        return result;
    }

}

function buildSearchBody(cs, q, category, mode, d_from, d_to, no_attachments, withResult = true) {
    let body = {
        query: {
            bool: {
                must: []
            }

        }
    };

    if (withResult) {
        body._source = {
            excludes: ['data', 'content', 'attachment.content']
        };

        body.highlight = {
            pre_tags: ['<mark>'],
            post_tags: ['</mark>'],
            fields: {
                [`content.${cs ? 'case-sensitive' : 'search'}`]: {
                    fragment_size: 160, number_of_fragments: 3
                },
                content: {
                    fragment_size: 160, number_of_fragments: 3
                }				
            }
		
        };

        body.suggest = {
            posts: {
                text: q,
                term: {
                    field: 'content.suggest'
                }
            }
        };

        if (!no_attachments) {
            body.highlight.fields['attachment.content'] = {
                fragment_size: 80, number_of_fragments: 3
            };

            body.suggest.attachments = {
                text: q,
                term: {
                    field: 'attachment.content'
                }
            }
        }
    }

    const mode_and = [{
        match: {
            [`title.${cs ? 'case-sensitive' : 'search'}`]: {
                query: q,
                operator: 'and'
            }
        }
    }, {
        match: {
            [`content.${cs ? 'case-sensitive' : 'search'}`]: {
                query: q,
                operator: 'and'
            }
        }
    }];

    if (!no_attachments) {
        mode_and.push({
            match: {
                'attachment.content': {
                    query: q,
                    operator: 'and'
                }
            }
        });
    }

    const mode_phrase = [{
        match_phrase: {
            [`title${cs ? '.case-sensitive' : ''}`]: q
        }
    }, {
        match_phrase: {
            [`content${cs ? '.case-sensitive' : ''}`]: q
        }
    }];

    if (!no_attachments) {
        mode_phrase.push({
            match_phrase: {
                'attachment.content': q
            }
        });
    }

    const mode_or = {
        multi_match: {
            query: q,
            fields: [
                `content.${cs ? 'case-sensitive' : 'search'}`,
                `title.${cs ? 'case-sensitive' : 'search'}`,
            ]
        }
    };

    if (!no_attachments) {
        mode_or.multi_match.fields.push('attachment.content');
    }

    const cat = {
        term: {
            categories: category
        }
    };

    if (q) {
        switch (mode) {
            case 'and': {
                body.query.bool.must.push({
                    dis_max: {
                        queries: mode_and
                    }
                });

                break;
            }
            case 'phrase': {
                body.query.bool.must.push({
                    dis_max: {
                        queries: mode_phrase
                    }
                });

                break;
            }
            case 'or': {
                body.query.bool.must.push(mode_or);

                break;
            }
            default: {
                body.query.bool.must.push({
                    dis_max: {
                        queries: [mode_or].concat(mode_and).concat(mode_phrase)
                    }
                });

                break;
            }
        }
    }

    if (category) {
        body.query.bool.must.push(cat);
    }

    if (d_from || d_to) {
        const range = {
            range: {
                date: {}
            }
        };

        if (d_from) {
            range.range.date.gte = format(new Date(d_from));
        }

        if (d_to) {
            range.range.date.lte = format(new Date(d_to));
        }

        body.query.bool.must.push(range);
    }

    return body;
}

function findFirstResult(indexName, cs, category, mode, d_from, d_to, response, res, queries, no_attachments, index = 0) {
    const q = queries[index];

    if (q) {
        const body = buildSearchBody(cs, q, category, mode, d_from, d_to, no_attachments, false);

        ES.count({
            index: indexName,
            body
        }).then((result) => {
            if (result.count > 0) {
                response.suggest = q;

                return res.status(200).send(response);
            }

            findFirstResult(indexName, cs, category, mode, d_from, d_to, response, res, queries, no_attachments, index + 1);
        }).catch((err) => {
            console.log('tutaj: 1');
            res.status(err.statusCode || 500).send(err)
        });
    } else {
        return res.status(200).send(response);
    }
}

router.get('/:user/:id', (req, res) => {
    const {id, user} = req.params;
    const {size = 10, from = 0, q = '', mode = '', category = '', d_from = '', d_to = '', sort = 'score'} = req.query;
    const indexName = `${user}${id ? `_${id}` : ''}`;
    let cs = req.query.cs === 'true';

    const no_attachments = req.query.hasOwnProperty('no_attachments');

    const body = buildSearchBody(cs, q, category, mode, d_from, d_to, no_attachments);

    ES.search({
        index: indexName,
        size,
        from,
        sort: sort === 'date_new' ? 'date:desc' : sort === 'date_old' ? 'date' : '_score',
        body
    }).then((result) => {
        const response = {
            count: result.hits.total
        };

        if (result.hits.total > 0) {
            response.hits = result.hits.hits;

            return res.status(200).send(response)
        }

        const splitedQuery = q.trim().split(' ');

        const attachSugg = result.suggest.attachments;
        const postsSugg = result.suggest.posts;
        const suggestsWords = [];

        for (let i = 0; i < splitedQuery.length; i++) {
            if (attachSugg || postsSugg) {
                const attach = attachSugg && attachSugg[i] ? attachSugg[i].options : [];
                const posts = postsSugg && postsSugg[i] ? postsSugg[i].options : [];
                let words = _.concat(attach, posts);

                words = _.map(words, word => word.text);
                words = _.uniq(words);
                words = _.map(words, word => word.replace('.', '').replace(',', '').replace('-', '').replace(':', '').replace(';', ''));

                suggestsWords.push(words);
            }
        }

        let suggests = allPossibleCases(addMissingWords(suggestsWords, splitedQuery));
        suggests = _.uniq(suggests);

        response.suggest = findFirstResult(indexName, cs, category, mode, d_from, d_to, response, res, suggests, no_attachments);
    }).catch((err) => {
        console.log(err);
        res.status(err.statusCode || 500).send(err);
    });
});

module.exports = router;
