const fs = require('fs');
const dir = `${__dirname}/../db`;

if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir);
}