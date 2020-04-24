const express = require('express');
const jwt = require('jsonwebtoken');
const Secrets = require('../config/Secrets');
const Router = require('../config/Router');
const ES = require('../helpers/ESConnection');
const VerifyMiddleware = require('../helpers/VerifyMiddleware');

const router = express.Router(Router);

router.get('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const {type = 'post'} = req.query;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.get({
        index: indexName,
        type,
        id: docId
    }).then((document) => {
        if (document) {
            res.status(200).send(document);
        } else {
            res.sendStatus(404);
        }
    }).catch((err) => {
        try {
            res.status(err.statusCode).send(err);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.get('/:id/doc-id/:docId/_exists', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const {type = 'post'} = req.query;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.get({
        index: indexName,
        type,
        id: docId
    }).then((document) => {
        if (document) {
            res.sendStatus(200);
        } else {
            res.sendStatus(404);
        }
    }).catch((err) => {
        try {
            res.status(err.statusCode).send(err);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.put('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const {type = 'post'} = req.query;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.create({
        index: indexName,
        type,
        id: docId,
        body: req.body
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.status(err.statusCode).send(err);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.post('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const {type = 'post'} = req.query;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;


    ES.update({
        index: indexName,
        type,
        id: docId,
        body: {
            doc: req.body
        }
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.status(err.statusCode).send(err);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.delete('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const {type = 'post'} = req.query;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.delete({
        index: indexName,
        type,
        id: docId
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.status(err.statusCode).send(err);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});


module.exports = router;