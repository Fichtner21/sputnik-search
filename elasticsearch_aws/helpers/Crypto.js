const crypto = require('crypto'),
    algorithm = 'aes-128-cbc',
    iv = '1234567890123456';

module.exports = {
    encrypt: function (text, password) {
        let cipher = crypto.createCipheriv(algorithm, password, iv);
        let encrypted = cipher.update(text, 'utf8', 'binary');

        encrypted += cipher.final('binary');

        let hexVal = new Buffer(encrypted, 'binary');
        let newEncrypted = hexVal.toString('hex');

        return newEncrypted;
    },
    decrypt: function (text, password) {
        let decipher = crypto.createDecipheriv(algorithm, password, iv);
        let decrypted = decipher.update(text, 'hex', 'binary');

        decrypted += decipher.final('binary');

        return decrypted;
    }
}