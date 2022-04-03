<?php
return [
    // A table name that is meant to hold the imported data
    'importTableName' => 'users',

    // A CSV separator sign
    'csvSeparator' => ',',

    // Metadata (DB,Transformer,Validator)
    'columnMapping' => [
        'name' => [
            'type' => 'string',
            'nullable' => false,
            'transformer' => ['lower', 'ucfirst']
        ],
        'surname' => [
            'type' => 'string',
            'nullable' => false,
            'transformer' => ['lower', 'ucfirst']
        ],
        'email' => [
            'type' => 'string',
            'unique' => true,
            'nullable' => false,
            'validator' => ['email'],
            'transformer' => ['lower']
        ]
    ],
];
