<?php

namespace Pada\CatalystScriptTest;

interface UserUploadServiceInterface
{
    /**
     * Uploads user's data from the specified file to a database.
     * Returns array of errors.
     * @param string $csvFilename
     * @param array $dbOptions
     * @return array<string>
     */
    public function upload(string $csvFilename, array $dbOptions): array;
    public function createTable(array $dbOptions): void;
}
