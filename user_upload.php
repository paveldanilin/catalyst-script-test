<?php

use Pada\CatalystScriptTest\App;
use Pada\CatalystScriptTest\Database\Database;
use Pada\CatalystScriptTest\UserUploadService;

require "./vendor/autoload.php";

$userUploadService = new UserUploadService(
    new Database(),
    'users'
);

(new App($userUploadService))->run($argv);
