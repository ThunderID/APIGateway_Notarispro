FORMAT: 1A

# BPM

# ComponentRule [/companies]
ComponentRule  resource representation.

## Show all companies [GET /companies]


+ Request (application/json)
    + Body

            {
                "search": {
                    "_id": "string",
                    "name": "string",
                    "code": "string"
                },
                "sort": {
                    "newest": "asc|desc"
                },
                "take": "integer",
                "skip": "integer"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "data": {
                        "_id": {
                            "value": "1234567890",
                            "type": "string",
                            "max": "255"
                        },
                        "client_identifier": {
                            "value": "123456789",
                            "type": "string",
                            "max": "255"
                        },
                        "component": {
                            "value": "referre.point",
                            "type": "string",
                            "max": "255"
                        },
                        "rule": {
                            "value": "50000",
                            "type": "string",
                            "max": "255"
                        },
                        "description": {
                            "value": "Bonus yang diterima user ketika memasukkan referral",
                            "type": "text"
                        },
                        "created_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "updated_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "deleted_at": {
                            "value": "null",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        }
                    },
                    "count": "integer"
                }
            }

## Store ComponentRule [POST /companies]


+ Request (application/json)
    + Body

            {
                "_id": "string",
                "name": "string",
                "code": "string"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "component": {
                        "value": "referre.point",
                        "type": "string",
                        "max": "255"
                    },
                    "rule": {
                        "value": "50000",
                        "type": "string",
                        "max": "255"
                    },
                    "description": {
                        "value": "Bonus yang diterima user ketika memasukkan referral",
                        "type": "text"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

## Delete ComponentRule [DELETE /companies]


+ Request (application/json)
    + Body

            {
                "_id": null
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "component": {
                        "value": "referre.point",
                        "type": "string",
                        "max": "255"
                    },
                    "rule": {
                        "value": "50000",
                        "type": "string",
                        "max": "255"
                    },
                    "description": {
                        "value": "Bonus yang diterima user ketika memasukkan referral",
                        "type": "text"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

# WorkflowProtocol [/companies]
WorkflowProtocol  resource representation.

## Show all companies [GET /companies]


+ Request (application/json)
    + Body

            {
                "search": {
                    "_id": "string",
                    "client": "string",
                    "trigger": "string"
                },
                "sort": {
                    "newest": "asc|desc"
                },
                "take": "integer",
                "skip": "integer"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "data": {
                        "_id": {
                            "value": "1234567890",
                            "type": "string",
                            "max": "255"
                        },
                        "client_identifier": {
                            "value": "123456789",
                            "type": "string",
                            "max": "255"
                        },
                        "trigger": {
                            "value": "store.referral",
                            "type": "string",
                            "max": "255"
                        },
                        "processes": {
                            "value": {
                                "rules": {
                                    "value": [
                                        "referee.point"
                                    ],
                                    "type": "array",
                                    "array": "string"
                                },
                                "parameters": {
                                    "value": [
                                        "referee.point",
                                        "referre.name"
                                    ],
                                    "type": "array",
                                    "array": "string"
                                },
                                "command": {
                                    "value": "store.point",
                                    "type": "string",
                                    "max": "255"
                                }
                            },
                            "type": "array"
                        },
                        "created_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "updated_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "deleted_at": {
                            "value": "null",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        }
                    },
                    "count": "integer"
                }
            }

## Store WorkflowProtocol [POST /companies]


+ Request (application/json)
    + Body

            {
                "_id": "string",
                "name": "string",
                "code": "string"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "trigger": {
                        "value": "store.referral",
                        "type": "string",
                        "max": "255"
                    },
                    "processes": {
                        "value": {
                            "rules": {
                                "value": [
                                    "referee.point"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "parameters": {
                                "value": [
                                    "referee.point",
                                    "referre.name"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "command": {
                                "value": "store.point",
                                "type": "string",
                                "max": "255"
                            }
                        },
                        "type": "array"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

## Delete WorkflowProtocol [DELETE /companies]


+ Request (application/json)
    + Body

            {
                "_id": null
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "trigger": {
                        "value": "store.referral",
                        "type": "string",
                        "max": "255"
                    },
                    "processes": {
                        "value": {
                            "rules": {
                                "value": [
                                    "referee.point"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "parameters": {
                                "value": [
                                    "referee.point",
                                    "referre.name"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "command": {
                                "value": "store.point",
                                "type": "string",
                                "max": "255"
                            }
                        },
                        "type": "array"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

# WorkflowProcess [/companies]
WorkflowProcess  resource representation.

## Show all companies [GET /companies]


+ Request (application/json)
    + Body

            {
                "search": {
                    "_id": "string",
                    "client": "string",
                    "trigger": "string"
                },
                "sort": {
                    "newest": "asc|desc"
                },
                "take": "integer",
                "skip": "integer"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "data": {
                        "_id": {
                            "value": "1234567890",
                            "type": "string",
                            "max": "255"
                        },
                        "client_identifier": {
                            "value": "123456789",
                            "type": "string",
                            "max": "255"
                        },
                        "trigger": {
                            "value": "store.referral",
                            "type": "string",
                            "max": "255"
                        },
                        "ticket": {
                            "value": "1270001",
                            "type": "string",
                            "max": "255"
                        },
                        "method": {
                            "value": "post",
                            "type": "string",
                            "max": "255"
                        },
                        "status": {
                            "value": "waiting",
                            "type": "enum",
                            "option": "waiting,failed,succeed"
                        },
                        "processes": {
                            "value": {
                                "parameters": {
                                    "value": [
                                        "referee.point",
                                        "referre.name"
                                    ],
                                    "type": "array",
                                    "array": "string"
                                },
                                "command": {
                                    "value": "store.point",
                                    "type": "string",
                                    "max": "255"
                                },
                                "status": {
                                    "value": "waiting",
                                    "type": "enum",
                                    "option": "waiting,failed,succeed"
                                },
                                "current_data_version": {
                                    "value": "0",
                                    "type": "integer"
                                },
                                "prev_data_version": {
                                    "value": "0",
                                    "type": "integer"
                                }
                            },
                            "type": "array"
                        },
                        "created_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "updated_at": {
                            "value": "2016-11-08 00:00:00",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        },
                        "deleted_at": {
                            "value": "null",
                            "type": "datetime",
                            "zone": "Asia/Jakarta",
                            "format": "Y-m-d H:i:s"
                        }
                    },
                    "count": "integer"
                }
            }

## Store WorkflowProcess [POST /companies]


+ Request (application/json)
    + Body

            {
                "_id": "string",
                "name": "string",
                "code": "string"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "trigger": {
                        "value": "store.referral",
                        "type": "string",
                        "max": "255"
                    },
                    "ticket": {
                        "value": "1270001",
                        "type": "string",
                        "max": "255"
                    },
                    "method": {
                        "value": "post",
                        "type": "string",
                        "max": "255"
                    },
                    "status": {
                        "value": "waiting",
                        "type": "enum",
                        "option": "waiting,failed,succeed"
                    },
                    "processes": {
                        "value": {
                            "parameters": {
                                "value": [
                                    "referee.point",
                                    "referre.name"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "command": {
                                "value": "store.point",
                                "type": "string",
                                "max": "255"
                            },
                            "status": {
                                "value": "waiting",
                                "type": "enum",
                                "option": "waiting,failed,succeed"
                            },
                            "current_data_version": {
                                "value": "0",
                                "type": "integer"
                            },
                            "prev_data_version": {
                                "value": "0",
                                "type": "integer"
                            }
                        },
                        "type": "array"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

## Delete WorkflowProcess [DELETE /companies]


+ Request (application/json)
    + Body

            {
                "_id": null
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": {
                        "value": "1234567890",
                        "type": "string",
                        "max": "255"
                    },
                    "client_identifier": {
                        "value": "123456789",
                        "type": "string",
                        "max": "255"
                    },
                    "trigger": {
                        "value": "store.referral",
                        "type": "string",
                        "max": "255"
                    },
                    "ticket": {
                        "value": "1270001",
                        "type": "string",
                        "max": "255"
                    },
                    "method": {
                        "value": "post",
                        "type": "string",
                        "max": "255"
                    },
                    "status": {
                        "value": "waiting",
                        "type": "enum",
                        "option": "waiting,failed,succeed"
                    },
                    "processes": {
                        "value": {
                            "parameters": {
                                "value": [
                                    "referee.point",
                                    "referre.name"
                                ],
                                "type": "array",
                                "array": "string"
                            },
                            "command": {
                                "value": "store.point",
                                "type": "string",
                                "max": "255"
                            },
                            "status": {
                                "value": "waiting",
                                "type": "enum",
                                "option": "waiting,failed,succeed"
                            },
                            "current_data_version": {
                                "value": "0",
                                "type": "integer"
                            },
                            "prev_data_version": {
                                "value": "0",
                                "type": "integer"
                            }
                        },
                        "type": "array"
                    },
                    "created_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "updated_at": {
                        "value": "2016-11-08 00:00:00",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    },
                    "deleted_at": {
                        "value": "null",
                        "type": "datetime",
                        "zone": "Asia/Jakarta",
                        "format": "Y-m-d H:i:s"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }