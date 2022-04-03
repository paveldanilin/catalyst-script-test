<?php

namespace Pada\CatalystScriptTest;

use Psr\Log\LoggerAwareInterface;

interface UserUploadServiceInterface extends LoggerAwareInterface
{
    /**
     * Uploads user's data from the specified file to a database.
     * Returns array of errors.
     * @param string $csvFilename
     * @param array $dbOptions
     * @param bool $dryRun
     * @return UploadResult
     */
    public function upload(string $csvFilename, array $dbOptions, bool $dryRun): UploadResult;
    public function createTable(array $dbOptions): void;
}
