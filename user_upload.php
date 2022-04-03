<?php

use Pada\CatalystScriptTest\App;
use Pada\CatalystScriptTest\ArrayConfig;
use Pada\CatalystScriptTest\Database\Database;
use Pada\CatalystScriptTest\Reader\CsvReader;
use Pada\CatalystScriptTest\Transformer\StringLowerTransformer;
use Pada\CatalystScriptTest\Transformer\StringUcfirstTransformer;
use Pada\CatalystScriptTest\Transformer\TransformerManager;
use Pada\CatalystScriptTest\UserUploadService;
use Pada\CatalystScriptTest\Validator\EmailValidator;
use Pada\CatalystScriptTest\Validator\ValidatorManager;

require "./vendor/autoload.php";

$validatorManager = (new ValidatorManager())
    ->addValidator(new EmailValidator());

$transformerManager = (new TransformerManager())
    ->addTransformer(new StringLowerTransformer())
    ->addTransformer(new StringUcfirstTransformer());

// Can be read from the external config file
// Keep it in a script for simplicity
$config = new ArrayConfig([
    // A table name that is meant to hold the imported data
    'importTableName' => 'users',
    // CSV separator sign
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
]);

$userUploadService = new UserUploadService(
    $config,
    new Database(),
    new CsvReader($config->getCsvSeparator()),
    $validatorManager,
    $transformerManager
);

(new App($userUploadService))->run($argv);
