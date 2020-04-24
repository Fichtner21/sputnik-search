const express = require('express');
const jwt = require('jsonwebtoken');
const Secrets = require('../config/Secrets');
const Router = require('../config/Router');
const ES = require('../helpers/ESConnection');
const VerifyMiddleware = require('../helpers/VerifyMiddleware');
const request = require('request');

const router = express.Router(Router);

router.get('/:id/doc-id/:docId', /*VerifyMiddleware,*/ (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    // const userData = jwt.decode(authorization, Secrets.secret);
    // const indexName = `${userData.userName}${id ? `_${id}` : ''}`;
    const indexName = `krosno${id ? `_${id}` : ''}`;

    ES.get({
        index: indexName,
        type: 'attachments',
        id: docId
    }).then((document) => {
        if (document) {
            res.status(200).send(document);
        } else {
            res.sendStatus(404);
        }
    }).catch((err) => {
        try {
            res.sendStatus(err.statusCode);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.put('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    console.log(req.body);


    ES.create({
        index: indexName,
        type: 'attachments',
        pipeline: 'attachment',
        id: docId,
        body: req.body
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.sendStatus(err.statusCode);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.post('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.update({
        index: indexName,
        type: 'attachments',
        id: docId,
        body: {
            doc: req.body
        }
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.sendStatus(err.statusCode);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});

router.delete('/:id/doc-id/:docId', VerifyMiddleware, (req, res) => {
    const {authorization} = req.headers;
    const {id, docId} = req.params;
    const userData = jwt.decode(authorization, Secrets.secret);
    const indexName = `${userData.userName}${id ? `_${id}` : ''}`;

    ES.delete({
        index: indexName,
        type: 'attachments',
        id: docId
    }).then((response) => {
        res.status(200).send(response);
    }).catch((err) => {
        try {
            res.sendStatus(err.statusCode);
        } catch (e) {
            res.sendStatus(500);
        }
    });
});


module.exports = router;