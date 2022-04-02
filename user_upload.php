<?php

use Pada\CatalystScriptTest\App;
use Pada\CatalystScriptTest\Database\Database;
use Pada\CatalystScriptTest\Reader\CsvReader;
use Pada\CatalystScriptTest\UserUploadService;
use Pada\CatalystScriptTest\Validator\EmailValidator;
use Pada\CatalystScriptTest\Validator\ValidatorManager;

require "./vendor/autoload.php";

$validatorManager = new ValidatorManager();
$validatorManager->addValidator('email', new EmailValidator());

// Can be read from the external config file
$config = new \Pada\CatalystScriptTest\Config([
    'importTableName' => 'users',
    'columnMapping' => [
        'name' => [
            'type' => 'string',
            'nullable' => false
        ],
        'surname' => [
            'type' => 'string',
            'nullable' => false
        ],
        'email' => [
            'type' => 'string',
            'unique' => true,
            'nullable' => false,
            'validator' => 'email'
        ]
    ],
]);

$userUploadService = new UserUploadService(
    $config,
    new Database(),
    new CsvReader(),
    $validatorManager
);

(new App($userUploadService))->run($argv);
