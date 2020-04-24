const express = require('express');
const jwt = require('jsonwebtoken');
const Secrets = require('../config/Secrets');
const Router = require('../config/Router');
const ES = require('../helpers/ESConnection');
const VerifyMiddleware = require('../helpers/VerifyMiddleware');

const router = express.Router(Router);

router.get('/:id', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);

    ES.indices.exists({
        index: `${userData.userName}${id ? `_${id}` : ''}`
    }).then((exists) => {
        if (exists) {
            res.sendStatus(200);
        } else {
            res.sendStatus(404);
        }
    }).catch((err) => {
        res.status(err.statusCode).send(err);
    });
});

router.put('/:id/mapping', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.indices.putMapping({
        index: indexName,
        masterTimeout: '5m',
        body: req.body
    }).catch(Function.prototype);

    res.sendStatus(202);
});

router.put('/:id', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    const body = {
        settings: {
            analysis: {
                analyzer: {
                    suggest: {
                        tokenizer: 'whitespace'
                    },
                    'simple-analyzer': {
                        tokenizer: 'whitespace',
                        filter: ['lowercase']
                    },
                    'case-sensitive': {
                        tokenizer: 'whitespace'
                    }
                }
            }
        },
        mappings: {
            post: {
                properties: {
                    title: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'simple-analyzer'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    },
                    thumbnail: {
                        type: 'text'
                    },
                    url: {
                        type: 'text'
                    },
                    date: {
                        type: 'date'
                    },
                    categories: {
                        type: 'integer'
                    },
                    content: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'polish'
                            },
                            suggest: {
                                type: 'text',
                                analyzer: 'suggest'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    }
                }
            },
            attachments: {
                properties: {
                    title: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'simple-analyzer'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    },
                    thumbnail: {
                        type: 'text'
                    },
                    url: {
                        type: 'text'
                    },
                    date: {
                        type: 'date'
                    },
                    categories: {
                        type: 'integer'
                    },
                    data: {
                        type: 'text',
                        analyzer: 'polish'
                    }
                }
            }
        }
    };

    ES.indices.exists({
        index: indexName
    }).then((exists) => {
        if (exists) {
            res.sendStatus(409);
        } else {
            ES.indices.create({
                index: indexName,
                masterTimeout: '5m',
                body: body
            }).then(() => {
                ES.ingest.deletePipeline({
                    id: 'attachment',
                }).then(() => {
                    ES.ingest.putPipeline({
                        id: 'attachment',
                        body: {
                            description: 'Extract attachment information',
                            processors: [
                                {
                                    attachment: {
                                        field: 'data',
                                        indexed_chars: -1
                                    }
                                }
                            ]
                        }
                    }).catch(Function.prototype);
                }).catch(Function.prototype);
            }).catch(Function.prototype);

            res.sendStatus(202);
        }
    }).catch((err) => {
        res.status(err.statusCode).send(err);
    });
})
;

router.put('/custom/:id', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.indices.exists({
        index: indexName
    }).then((exists) => {
        if (exists) {
            res.sendStatus(409);
        } else {
            ES.indices.create({
                index: indexName,
                masterTimeout: '5m',
                body: req.body
            }).catch(Function.prototype);

            res.sendStatus(202);
        }
    }).catch((err) => {
        res.status(err.statusCode).send(err);
    });
});

router.delete('/:id', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);

    ES.indices.delete({
        index: `${userData.userName}${id ? `_${id}` : ''}`
    }).then(() => {
        res.sendStatus(200);
    }).catch((err) => {
        res.status(err.statusCode).send(err);
    });
});


module.exports = router;