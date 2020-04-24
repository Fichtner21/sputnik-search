module.exports = {
    host: '127.0.0.1:9200',
    requestTimeout: 99999999,
    log: [{
        type: 'stdio',
        levels: ['error'] // change these options
    }]
};