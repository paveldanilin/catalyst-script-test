<?php

use Pada\CatalystScriptTest\App;
use Pada\CatalystScriptTest\Database\Database;
use Pada\CatalystScriptTest\Reader\CsvReader;
use Pada\CatalystScriptTest\UserUploadService;

require "./vendor/autoload.php";

$userUploadService = new UserUploadService(
    new Database(),
    'users',
    new CsvReader()
);

(new App($userUploadService))->run($argv);
