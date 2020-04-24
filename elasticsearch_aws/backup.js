const ES = require('./helpers/ESConnection');
const _ = require('lodash');
const moment = require('moment');
const request = require('superagent');
const prefix = require('superagent-prefix');

const currentDate = moment().format('DD-MM-YYYY') || '09-08-2017';

const args = process.argv;
const length = process.argv.length;

function errorLog(err) {
    console.log(err);
}

for (let i = 0; i < length; i++) {
    if (args[i] === '--list') {
        if (args[i + 1]) {
            const repoName = args[i + 1];

            listSnapshots(repoName);
        } else {
            console.error('No repository name');
        }

        break;
    } else if (args[i] === '--del-all') {
        if (args[i + 1]) {
            const repoName = args[i + 1];

            deleteAllSnapshots(repoName);
        } else {
            console.error('No repository name');
        }

        break;
    } else if (args[i] === '--create-repo') {
        if (args[i + 1]) {
            const repoName = args[i + 1];

            createRepository(repoName);
        } else {
            console.error('No repository name');
        }

        break;
    } else if (args[i] === '--del-repo') {
        if (args[i + 1]) {
            const repoName = args[i + 1];

            delRepository(repoName);
        } else {
            console.error('No repository name');
        }

        break;
    } else if (args[i] === '--create-snapshots') {
        if (args[i + 1]) {
            const repoName = args[i + 1];

            createSnapshots(repoName);
        } else {
            console.error('No repository name');
        }

        break;
    } else if (args[i] === '--create-index') {
        if (args[i + 1]) {
            const indexName = args[i + 1];

            createIndex(indexName);
        } else {
            console.error('No index name');
        }

        break;
    } else if (args[i] === '--restore-snapshot') {
        const repoName = args[i + 1];
        const index = args[i + 2];
        const snapshot = args[i + 3];

        if (repoName && index && snapshot) {
            restoreSnapshot(repoName, index, snapshot);
        } else {
            console.error('This method required 3 args(repoName, indexName, snapshotName).');
        }

        break;
    }
}


function deleteSnapshot(name, snapshots, current = 0) {
    const snapshot = snapshots[current];

    if (snapshot) {
        ES.snapshot.delete({
            repository: name,
            snapshot: snapshot.id
        }).then(() => {
            deleteSnapshot(name, snapshots, current + 1);
            console.log(`Snapshot ${snapshot.id} was deleted.`);
        }).catch(errorLog);
    }
}

function deleteAllSnapshots(name) {
    ES.cat.snapshots({
        repository: name,
        format: 'json'
    }).then((snapshots) => {
        deleteSnapshot(name, snapshots)
    }).catch(errorLog);
}

function createRepository(name) {
    ES.snapshot.createRepository({
        repository: name,
        body: {
            type: 'fs',
            settings: {
                compress: true,
                location: '/home/es-service/backup'
            }
        }
    }).then((response) => {
        console.log(response);
    }).catch(errorLog);
}

function delRepository(name) {
    ES.snapshot.deleteRepository({
        repository: name
    }).then((response) => {
        console.log(response);
    }).catch(errorLog);
}

const checkSnapshot = {
    interval: '',
    current: 0
};

function checkIfSnapshotSuccess(name, indices, current, snapshot) {
    if(checkSnapshot.current === current) {
        ES.snapshot.status({
            repository: name,
            snapshot: snapshot,
        }).then((res) => {
            const {snapshots} = res;

            if (snapshots[0]) {
                if (snapshots[0].state === 'SUCCESS') {
                    clearInterval(checkSnapshot.interval);
                    checkSnapshot.current++;
                    console.log(`Snapshot ${snapshot} was created.`);
                    createSnapshot(name, indices, current + 1);
                } else {
                    console.log(`Snapshot ${snapshot} status ${snapshots[0].state}.`);
                }
            }
        }).catch((err) => {
            console.log(err);
        });
    }
}

function createSnapshot(name, indices, current = 0) {
    const index = indices[current];

    if (index) {
        const snapshotName = `${index.index}_${currentDate}`;

        request.put(`/_snapshot/${name}/${snapshotName}`)
            .use(prefix('http://127.0.0.1:9200'))
            .send({
                indices: index.index
            })
            .end((err, res) => {
                if (err) {
                    console.log(err);
                } else {
                    checkSnapshot.interval = setInterval(checkIfSnapshotSuccess.bind(this, name, indices, current, snapshotName), 10000);
                }
            });
    }
}

function createSnapshots(name) {
    ES.cat.indices({
        format: 'json'
    }).then((indices) => {
        createSnapshot(name, indices);
    }).catch(errorLog);
}

function listSnapshots(name) {
    ES.cat.snapshots({
        format: 'json',
        repository: name
    }).then((snapshots) => {
        console.log(snapshots);
    }).catch(errorLog);
}

function openIndex(index) {
    ES.indices.open({
        index
    }).then(() => {
        console.log(`Open index ${index}.`);
    }).catch(errorLog);
}

function restoreSnapshot(name, index, snapshot) {
    ES.indices.close({
        index
    }).then(() => {
        console.log(`Close index ${index}.`);
        ES.snapshot.restore({
            format: 'json',
            repository: name,
            snapshot,
            body: {
                indices: index
            }
        }).then(() => {
            console.log(`Snapshot for index ${index} with name '${snapshot}' was restored.`);
            openIndex(index);
        }).catch((err) => {
            openIndex(index);
            console.log(err);
        });
    }).catch(errorLog);

}

function createIndex(name) {
    const body = {
        settings: {
            analysis: {
                analyzer: {
                    suggest: {
                        tokenizer: 'whitespace'
                    },
                    'simple-analyzer': {
                        tokenizer: 'whitespace',
                        filter: ['lowercase']
                    },
                    'case-sensitive': {
                        tokenizer: 'whitespace'
                    }
                }
            }
        },
        mappings: {
            post: {
                properties: {
                    title: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'simple-analyzer'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    },
                    thumbnail: {
                        type: 'text'
                    },
                    url: {
                        type: 'text'
                    },
                    date: {
                        type: 'date'
                    },
                    categories: {
                        type: 'integer'
                    },
                    content: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'polish'
                            },
                            suggest: {
                                type: 'text',
                                analyzer: 'suggest'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    }
                }
            },
            attachments: {
                properties: {
                    title: {
                        type: 'text',
                        fields: {
                            search: {
                                type: 'text',
                                analyzer: 'simple-analyzer'
                            },
                            'case-sensitive': {
                                type: 'text',
                                analyzer: 'case-sensitive'
                            }
                        }
                    },
                    thumbnail: {
                        type: 'text'
                    },
                    url: {
                        type: 'text'
                    },
                    date: {
                        type: 'date'
                    },
                    categories: {
                        type: 'integer'
                    },
                    data: {
                        type: 'text',
                        analyzer: 'polish'
                    }
                }
            }
        }
    };

    ES.indices.exists({
        index: name
    }).then((exists) => {
        if (exists) {
            console.log(`Index ${name} already exists.`);
        } else {
            ES.indices.create({
                index: name,
                masterTimeout: '5m',
                body: body
            }).then(() => {
                console.log(`Index ${name} created.`);
                ES.ingest.deletePipeline({
                    id: 'attachment',
                }).then(() => {
                    ES.ingest.putPipeline({
                        id: 'attachment',
                        body: {
                            description: 'Extract attachment information',
                            processors: [
                                {
                                    attachment: {
                                        field: 'data',
                                        indexed_chars: -1
                                    }
                                }
                            ]
                        }
                    }).catch(errorLog);
                }).catch(errorLog);
            }).catch(errorLog);
        }
    }).catch(errorLog);
}
