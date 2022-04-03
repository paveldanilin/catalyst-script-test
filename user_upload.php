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
$configData = include './config.php';
$config = new ArrayConfig($configData);

$userUploadService = new UserUploadService(
    $config,
    new Database(),
    new CsvReader($config->getCsvSeparator()),
    $validatorManager,
    $transformerManager
);

(new App($userUploadService))->run($argv);
