<?php

namespace Pada\CatalystScriptTest;

use Pada\CatalystScriptTest\Database\DatabaseInterface;

final class UserUploadService implements UserUploadServiceInterface
{
    private DatabaseInterface $database;
    private string $usersTable;

    public function __construct(DatabaseInterface $database, string $usersTable)
    {
        $this->database = $database;
        $this->usersTable = $usersTable;
    }

    public function upload(string $csvFilename, array $dbOptions): void
    {
        // TODO: read CSV
        $this->dbConnect($dbOptions);

        if (!$this->database->tableExists($this->usersTable)) {
            throw new \RuntimeException('Table "'.$this->usersTable.'" not exists');
        }

        $this->database->beginTransaction();

        // TODO: batch upload

        $this->database->commit();
    }

    public function createTable(array $dbOptions): void
    {
        $this->dbConnect($dbOptions);
        if ($this->database->tableExists($this->usersTable)) {
            throw new \RuntimeException('The table "'.$this->usersTable.'" already exists');
        }
        $this->database->createTable($this->usersTable, [
            'name' => ['type' => 'string', 'nullable' => false],
            'surname' => ['type' => 'string', 'nullable' => false],
            'email' => ['type' => 'string', 'unique' => true, 'nullable' => false]
        ]);
    }

    private function dbConnect(array $dbOptions): void
    {
        if (!$this->database->open($dbOptions)) {
            throw new \RuntimeException('Could not connect to the database, please check connection options. ' .
                $this->database->getLastError());
        }
    }
}
