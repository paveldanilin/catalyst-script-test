<?php

namespace Pada\CatalystScriptTest;

use Pada\CatalystScriptTest\Database\DatabaseInterface;
use Pada\CatalystScriptTest\Reader\ReaderInterface;
use Pada\CatalystScriptTest\Transformer\TransformerManagerInterface;
use Pada\CatalystScriptTest\Validator\InvalidValueException;
use Pada\CatalystScriptTest\Validator\ValidatorManagerInterface;

final class UserUploadService implements UserUploadServiceInterface
{
    private DatabaseInterface $database;
    private ReaderInterface $reader;
    private ValidatorManagerInterface $validatorManager;
    private TransformerManagerInterface $transformerManager;
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config,
                                DatabaseInterface $database,
                                ReaderInterface $reader,
                                ValidatorManagerInterface $validatorManager,
                                TransformerManagerInterface $transformerManager)
    {
        $this->config = $config;
        $this->database = $database;
        $this->reader = $reader;
        $this->validatorManager = $validatorManager;
        $this->transformerManager = $transformerManager;
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
            'with_headers' => true, // Treats the first line as a header line
        ];
        $errors = [];

        foreach ($this->reader->next($csvOpts) as $row) {
            [$rowNum, $rowData] = $row;
            $isDataValid = true;
            $dataToInsert = [];

            foreach ($rowData as $columnName => $columnValue) {
                // Validate value
                $validators= $columnMapping[$columnName]['validator'] ?? [];
                try {
                    $this->validatorManager->validate($columnValue, $validators);
                } catch (InvalidValueException $invalidValueException) {
                    $errors[] = $invalidValueException->getMessage() . ' [' . $columnValue . '] at line ' . ($rowNum + 1);
                    $isDataValid = false;
                    break; // The value is invalid, skip the row
                }

                // Transform value
                $transformers = $columnMapping[$columnName]['transformer'] ?? [];
                $dataToInsert[$columnName] = $this->transformerManager->transformer($columnValue, $transformers);
            }

            if ($isDataValid) {
                print "INSERT ->" . print_r($dataToInsert, true) . "\n";
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
