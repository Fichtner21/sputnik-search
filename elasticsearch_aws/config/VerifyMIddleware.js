module.exports = function (req, res, next) {
    const {authorization} = req.headers;

    try {
        jwt.verify(authorization, Secrets.secret);
    } catch (err) {
        return res.status(401).send(err.message);
    }

    next();
};