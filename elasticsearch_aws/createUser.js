require('./helpers/CreateFolder');
const Datastore = require('nedb');
const RandomPassword = require('./helpers/RandomPassword');

const db = new Datastore({filename: `${__dirname}/db/users.db`});

db.loadDatabase();

const args = process.argv;
const length = process.argv.length;

let userName = '';
let aesKey = '';

for (let i = 0; i < length; i++) {
    if (args[i] === '-u') {
        if (args[i + 1]) {
            userName = args[i + 1];
        }
    } else if (args[i] === '-k') {
        if (args[i + 1]) {
            aesKey = args[i + 1];
        }
    }
}

if (userName && aesKey && aesKey.length === 16) {
    db.findOne({userName}, (error, usr) => {
        if(error) {
            console.log(error);
        } else {
            if(usr) {
                console.log('User exists');
            } else {
                const password = RandomPassword.generate();
                const hashedPassword = RandomPassword.hashString(password);

                let user = {
                    userName,
                    aesKey,
                    password: hashedPassword
                };

                db.insert(user, (err) => {
                    if (err) {
                        console.log(err);
                    } else {
                        console.log(password);
                    }
                });
            }
        }
    });
} else {
    console.log('Error');
}
