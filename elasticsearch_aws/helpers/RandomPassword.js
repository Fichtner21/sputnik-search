const crypto = require('crypto');
const randomstring = require('randomstring');

function hashString(text) {
    return crypto.createHash('sha512').update(text).digest('hex');
}

function generate() {
    const currentTime = new Date();
    const randomString = randomstring.generate(32) + currentTime.getTime().toString();

    return hashString(randomString);
}

module.exports = {
    generate,
    hashString
};