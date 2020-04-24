const connectionData = require('../config/ElasticSearch');
const elasticsearch = require('elasticsearch');

const client = new elasticsearch.Client(connectionData);

module.exports = client;
