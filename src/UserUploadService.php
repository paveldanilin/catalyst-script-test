<?php

namespace Pada\CatalystScriptTest;

use Pada\CatalystScriptTest\Database\DatabaseInterface;
use Pada\CatalystScriptTest\Reader\ReaderInterface;
use Pada\CatalystScriptTest\Validator\InvalidValueException;
use Pada\CatalystScriptTest\Validator\ValidatorManagerInterface;

final class UserUploadService implements UserUploadServiceInterface
{
    private DatabaseInterface $database;
    private ReaderInterface $reader;
    private ValidatorManagerInterface $validatorManager;
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config, DatabaseInterface $database, ReaderInterface $reader, ValidatorManagerInterface $validatorManager)
    {
        $this->config = $config;
        $this->database = $database;
        $this->reader = $reader;
        $this->validatorManager = $validatorManager;
    }

    public function upload(string $csvFilename, array $dbOptions): array
    {
        // TODO: read CSV
        $this->dbConnect($dbOptions);

        if (!$this->database->tableExists($this->config->getTableName())) {
            throw new \RuntimeException('Table "'.$this->config->getTableName().'" not exists');
        }

        //$this->database->beginTransaction();

        $columnMapping = $this->config->getColumnMapping();
        $csvOpts = [
            'filename' => $csvFilename,
            'with_headers' => true,
        ];
        $errors = [];

        foreach ($this->reader->next($csvOpts) as $row) {
            [$rowNum, $rowData] = $row;

            foreach ($rowData as $columnName => $columnValue) {
                $validatorName = $columnMapping[$columnName]['validator'] ?? null;
                if (null !== $validatorName) {
                    try {
                        $this->validatorManager->getValidator($validatorName)->validate($columnValue);
                    } catch (InvalidValueException $invalidValueException) {
                        $errors[] = $invalidValueException->getMessage() . ' [' . $columnValue . '] at line ' . ($rowNum + 1);
                    }
                }
            }
        }

        //$this->database->commit();

        return $errors;
    }

    public function createTable(array $dbOptions): void
    {
        $this->dbConnect($dbOptions);
        if ($this->database->tableExists($this->config->getTableName())) {
            throw new \RuntimeException('The table "'.$this->config->getTableName().'" already exists');
        }
        $this->database->createTable($this->config->getTableName(), $this->config->getColumnMapping());
    }

    private function dbConnect(array $dbOptions): void
    {
        if (!$this->database->open($dbOptions)) {
            throw new \RuntimeException('Could not connect to the database, please check connection options. ' .
                $this->database->getLastError());
        }
    }
}
