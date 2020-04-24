const express = require('express');
const crypto = require('../helpers/Crypto');
const Datastore = require('nedb');
const jwt = require('jsonwebtoken');
const RandomPassword = require('../helpers/RandomPassword');
const Secrets = require('../config/Secrets');
const Router = require('../config/Router');

const db = new Datastore({filename: `${__dirname}/../db/users.db`});

const router = express.Router(Router);

router.post('/', (req, res) => {
    db.loadDatabase();

    const {userName, password} = req.body;
    const hashedPassword = RandomPassword.hashString(password);

    db.findOne({
        userName,
        password: hashedPassword
    }, (error, usr) => {
        if (error) {
            return res.status(500).send(error);
        } else {
            if (usr && usr.userName === userName && usr.password === hashedPassword) {
                const token = jwt.sign({userName}, Secrets.secret/*, {expiresIn: '1m'}*/);

               // const encrypted = crypto.encrypt(token, usr.aesKey); 
               // przed 23.04 token + key (trzeba to zdecryptować w crypto.php po stronie wtyczki). (większe zabezpieczenia)

                return res.status(200).send(token); 
                // po 23.04 token zwracany bez dekryptacji, nie ma potrzeby wykorzystywania pliku crypto.php (mniejsze zabezpieczenia)
            } else {
                return res.sendStatus(401);
            }
        }
    });
});


module.exports = router;
