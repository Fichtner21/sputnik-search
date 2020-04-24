const express = require('express');
const http = require('http');
const fs = require('fs');
const bodyParser = require('body-parser');
const api = require('./api');

const app = express();

app.use(bodyParser.json({limit: '50mb'}));
app.use(bodyParser.urlencoded({extended: true}));

app.use((req, res, next) => {
    res.header("Access-Control-Allow-Origin", "*");
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, HEAD');

    return next()
});

api(app);

app.set('port', 9005);

const server = http.createServer(app);

server.on('listening', onListening);

server.listen(9005);

function onListening() {
    let addr = server.address(),
        bind = typeof addr === 'string'
            ? `pipe ${addr}`
            : `port ${addr.port}`;

    console.log(`Server listening on ${bind}`);
}