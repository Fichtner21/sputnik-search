const Auth = require('./Auth');
const Attachments = require('./Attachments');
const Documents = require('./Documents');
const Indices = require('./Indices');
const Search = require('./Search');

module.exports = (app) => {
    app.use('/api/auth', Auth);
    app.use('/api/attachments', Attachments);
    app.use('/api/documents', Documents);
    app.use('/api/indices', Indices);
    app.use('/api/search', Search);
};