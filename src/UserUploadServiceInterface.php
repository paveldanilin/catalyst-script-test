<?php

namespace Pada\CatalystScriptTest;

interface UserUploadServiceInterface
{
    public function upload(string $csvFilename, array $dbOptions): void;
    public function createTable(array $dbOptions): void;
}
