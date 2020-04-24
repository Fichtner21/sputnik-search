const RandomPassword = require('../helpers/RandomPassword');

module.exports = {
    secret: RandomPassword.generate()
};