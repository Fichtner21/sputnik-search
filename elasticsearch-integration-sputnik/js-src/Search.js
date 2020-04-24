import React, {Component} from 'react';
import request from 'superagent';
import prefix from 'superagent-prefix';
import _ from 'lodash';
import ReactPaginate from 'react-paginate';
import {Img} from './Img';

export class Search extends Component {
    constructor(props) {
        super(props);

        this.state = {
            q: props.q,
            count: 0,
            size: props.size,
            mode: props.mode,
            from: props.from,
            d_from: props.d_from,
            d_to: props.d_to,
            category: props.category,
            sort: props.sort,
            cs: props.cs === 'true',
            results: [],
            suggests: '',
            loading: true
        };

        this.count = 0;

        this.onChangeInput = (e) => {
            this.state.q = e.target.value;
        };

        this.popState = () => {
            let s = document.getElementById('s');
            let searchMode = document.getElementById('search-mode');
            let categorySelect = document.getElementById('category-select');
            let dateFrom = document.getElementById('datepicker-from');
            let dateTo = document.getElementById('datepicker-to');

            const url = new URL(location.href);
            const q = url.searchParams.get('s');
            const from = url.searchParams.get('from');
            const size = url.searchParams.get('size');
            const mode = url.searchParams.get('search-mode');
            const category = url.searchParams.get('category');
            const d_from = url.searchParams.get('d_from');
            const d_to = url.searchParams.get('d_to');
            const sort = url.searchParams.get('sort');
            const cs = url.searchParams.get('cs') === 'true';

            s.value = q;
            searchMode.value = mode;
            categorySelect.value = category;
            dateFrom.value = d_from;
            dateTo.value = d_to;
            searchMode.checked = cs;

            this.state.q = q;
            this.state.from = from;
            this.state.size = size;
            this.state.mode = mode;
            this.state.d_from = d_from;
            this.state.d_to = d_to;
            this.state.cs = cs;
            this.state.sort = sort;
            this.state.category = category;

            this.search();
        };

        this.onChangeMode = (e) => {
            this.state.mode = e.target.value;
        };

        this.onChangeCS = (e) => {
            this.state.cs = e.target.checked;
        };

        this.onChangeCategory = (e) => {
            this.state.category = e.target.value;
        };

        this.onChangeDateFrom = (e) => {
            this.state.d_from = e.target.value;
        };

        this.onChangeDateTo = (e) => {
            this.state.d_to = e.target.value;
        };

        this.search = (e) => {
            let {q, from, size, mode, cs, category, d_from, d_to, sort} = this.state;
            const {apiURL, user, blogID} = window.configES;

            this.setState({
                loading: true
            });

            if (e) {
                e.preventDefault();

                from = 0;

                this.pushState(q, mode, category, '', '', 0, size, cs);
            }

            const path = `/api/search/${user}/${blogID}?q=${q}&from=${from * size}&size=${size}&mode=${mode}&cs=${cs}&category=${category}&d_from=${d_from}&d_to=${d_to}&sort=${sort}`;

            request.get(path)
                .use(prefix(apiURL))
                .end((err, res) => {
                    if (err) {
                        console.log(err);
                    } else {
                        const {count, hits, suggest} = res.body;

                        if (count > 0) {
                            this.setState({
                                results: _.map(hits, hit => {
                                    let highlight = [];

                                    if (hit.highlight) {
                                        if (hit._type === 'post') {
                                            highlight = hit.highlight['content.search'] || hit.highlight['content'] || hit.highlight['content.case-sensitive'] || [];
                                        } else if (hit._type === 'attachments') {
                                            highlight = hit.highlight['attachment.content'] || [];
                                        }
                                    }

                                    return {
                                        fields: hit._source,
                                        type: hit._type,
                                        highlight: highlight
                                    }
                                }),
                                from,
                                loading: false,
                                count,
                                suggest: ''
                            }, this.goToTopOfResults.bind(this));
                        } else {
                            this.setState({
                                results: [],
                                from,
                                loading: false,
                                count,
                                suggest: suggest || ''
                            }, this.goToTopOfResults.bind(this));
                        }
                    }

                    window.configES.onSearch(q);
                });
        }
    }

    componentDidMount() {
        let s = document.getElementById('s');
        let searchForm = document.getElementById('searchform');
        let searchMode = document.getElementById('search-mode');
        let cs = document.getElementById('case_sensitive');
        let categorySelect = document.getElementById('category-select');
        let dateFrom = document.getElementById('datepicker-from');
        let dateTo = document.getElementById('datepicker-to');

        s.addEventListener('change', this.onChangeInput);
        searchForm.addEventListener('submit', this.search);
        searchMode.addEventListener('change', this.onChangeMode);
        cs.addEventListener('change', this.onChangeCS);
        categorySelect.addEventListener('change', this.onChangeCategory);
        dateFrom.onchange = this.onChangeDateFrom;
        dateTo.onchange = this.onChangeDateTo;

        window.addEventListener('popstate', this.popState);

        this.search();
    }

    componentWillUnmount() {
        let s = document.getElementById('s');
        let searchForm = document.getElementById('searchform');
        let searchMode = document.getElementById('search-mode');
        let cs = document.getElementById('case_sensitive');
        let categorySelect = document.getElementById('category-select');
        let dateFrom = document.getElementById('datepicker-from');
        let dateTo = document.getElementById('datepicker-to');

        s.removeEventListener('change', this.onChangeInput);
        searchForm.removeEventListener('submit', this.search);
        searchMode.removeEventListener('change', this.onChangeMode);
        cs.removeEventListener('change', this.onChangeCS);
        categorySelect.removeEventListener('change', this.onChangeCategory);
        dateFrom.onchange = Function.prototype;
        dateTo.onchange = Function.prototype;

        window.removeEventListener('popstate', this.popState);
    }

    goToTopOfResults() {
        const searchTitle = document.getElementById('search-title');
        const boundingClientRect = searchTitle.getBoundingClientRect();

        window.scrollBy(0, boundingClientRect.top);
    }

    onPageChange(from) {
        if (this.state.from !== from.selected) {
            this.state.from = from.selected;

            const {q, size, mode, cs, category, d_from, d_to, sort} = this.state;

            this.pushState(q, mode, category, d_from, d_to, this.state.from, size, cs, sort);

            this.search();
        }
    }

    onChangeSize(e) {
        const {q, mode, cs, category, d_from, d_to, sort} = this.state;

        this.state.from = 0;
        this.state.size = e.target.value;

        this.pushState(q, mode, category, d_from, d_to, 0, this.state.size, cs, sort);

        this.search();
    }

    approveSuggest(q) {
        const {cs, category, d_from, d_to, sort} = this.state;

        this.state.q = q;
        this.state.size = 10;
        this.state.from = 0;

        document.getElementById('s').value = q;

        this.pushState(this.state.q, this.state.mode, category, d_from, d_to, 0, 10, cs, sort);

        this.search();
    }

    onChangeSort(e) {
        const {cs, category, d_from, d_to, size, q, mode} = this.state;

        this.state.sort = e.target.value;

        this.pushState(q, mode, category, d_from, d_to, 0, size, cs, this.state.sort);

        this.search();
    }

    pushState(q, mode, category, d_from, d_to, from, size, cs, sort) {
        history.pushState({}, 'Szukanie', `?s=${q}&search-mode=${mode}&category=${category}&d_from=${d_from}&d_to=${d_to}&from=${from}&size=${size}&cs=${cs}&sort=${sort}`);
    }

    getFileType(mime) {
        switch (mime) {
            case 'application/pdf': {
                return 'PDF';
            }
            case 'application/vnd.oasis.opendocument.text': {
                return 'ODT';
            }
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': {
                return 'PPTX';
            }
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': {
                return 'XLSX';
            }
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': {
                return 'DOCX';
            }
            default: {
                return 'PLIK';
            }
        }
    }

    socialClick(e) {
        e.preventDefault();

        window.open(e.target.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    }

    render() {
        const {results, count, from, size, q, loading, sort, suggest} = this.state;
        const {thumbnail, facebook} = window.configES;

        this.count++;

        if (loading) {
            return <div className="loader">
                <div className="spinner">
                    <div className="bounce1"/>
                    <div className="bounce2"/>
                    <div className="bounce3"/>
                </div>
            </div>
        }

        return <div>
            {count === 0 && q && this.count > 1 && <div className="suggests">
                Nie znaleziono wyników dla zapytania.
                {suggest !== q && suggest && <span> Czy chodziło Tobie o <span className="suggest-text"
                                                                               onClick={this.approveSuggest.bind(this, suggest)}>{suggest}</span>?</span>}
                {(suggest === q || !suggest) && <span> Spróbuj wyszukać inne.</span>}
            </div>}
            {count > 0 && <div className="search-result-bar">
                <div>Liczba znalezionych artykułów: {count}.</div>
                <div>
                    <div>
                        <lable htmlFor="#number-of-results">Liczba wyników na stronie</lable>
                        <select id="number-of-results"
                                title="Liczba wyników na stronie"
                                value={size}
                                onChange={this.onChangeSize.bind(this)}>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div>
                        <lable htmlFor="#number-of-results">Sortuj według</lable>
                        <select id="sort"
                                title="Sortuj według"
                                value={sort}
                                onChange={this.onChangeSort.bind(this)}>
                            <option value="score">Trafność</option>
                            <option value="date_new">Data najnowsza</option>
                            <option value="date_old">Data najstarsza</option>
                        </select>
                    </div>
                </div>
            </div>}
            {
                _.map(results, (result, i) => {
                    const {fields, type, highlight} = result;
                    const {attachment} = fields;

                    return <article className="article" key={i}>
                        <div>
                            {type !== 'attachments' && <figure style={{overflow: "hidden"}}>
                                {fields.thumbnail && <Img width={thumbnail.width}
                                                          height={thumbnail.height}
                                                          src={fields.thumbnail}
                                                          className="attachment-post-thumbnail size-post-thumbnail wp-post-image"
                                                          alt={fields.title}/>}
                            </figure>}
                            <div>
                                <header>
                                    <h4>
                                        {type === 'attachments' ? <span>
                                    <sup>[{this.getFileType(attachment.content_type)}]</sup> {fields.title}</span> : fields.title}
                                    </h4>
                                </header>
                                <div className="info">
                                    <div>
                                        <span>{fields.date}</span>
                                    </div>
                                    <div className="social-buttons">
                                        <a href={`https://www.facebook.com/sharer/sharer.php?u=${fields.url}title=${fields.title}”`}
                                           onClick={this.socialClick.bind(this)}
                                           className="fb">
                                            <em className="fa fa-facebook" aria-hidden="true"></em>
                                        </a>
                                        <a href={`https://plus.google.com/share?url=${fields.url}`}
                                           onClick={this.socialClick.bind(this)} class="g">
                                            <em className="fa fa-google-plus" aria-hidden="true"></em>
                                        </a>
                                    </div>
                                </div>
                                <div className="content-post-on-list">
                                    {
                                        _.map(highlight, (mark, index) => {
                                            const threeDots = ' <span style="padding=0 5px;">...</span> ';
                                            const text = `...${mark}${index === highlight.length - 1 ? threeDots : ''}`;

                                            return <span key={index}
                                                         dangerouslySetInnerHTML={{__html: text}}/>
                                        })
                                    }
                                </div>
                                <footer>
                                    <a href={fields.url} className="more">Czytaj więcej</a>
                                </footer>
                            </div>
                        </div>
                    </article>
                })
            }
            {count > size && <ReactPaginate pageCount={Math.ceil(count / size)}
                                            containerClassName="custom-pagination"
                                            onPageChange={this.onPageChange.bind(this)}
                                            initialPage={from}
                                            pageClassName="page-numbers"
                                            previousClassName="page-numbers"
                                            nextClassName="page-numbers"
                                            nextLabel="»"
                                            previousLabel="«"
                                            forcePage={from}/>}
        </div>;
    }
}
