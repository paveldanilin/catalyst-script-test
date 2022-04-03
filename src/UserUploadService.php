<?php

namespace Pada\CatalystScriptTest;

use Pada\CatalystScriptTest\Database\DatabaseInterface;
use Pada\CatalystScriptTest\Reader\ReaderInterface;
use Pada\CatalystScriptTest\Transformer\TransformerManagerInterface;
use Pada\CatalystScriptTest\Validator\InvalidValueException;
use Pada\CatalystScriptTest\Validator\ValidatorManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class UserUploadService implements UserUploadServiceInterface
{
    private const INSERT_CODE_OK = 0;
    private const INSERT_CODE_DUPLICATE = 100;

    private DatabaseInterface $database;
    private ReaderInterface $reader;
    private ValidatorManagerInterface $validatorManager;
    private TransformerManagerInterface $transformerManager;
    private ConfigInterface $config;
    private LoggerInterface $logger;

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
        $this->logger = new NullLogger();
    }

    // TODO: a method too long, should be refactored
    public function upload(string $csvFilename, array $dbOptions, bool $dryRun): UploadResult
    {
        $this->logger->debug('Before upload', [
            'csv_filename' => $csvFilename,
            'db_options' => $dbOptions,
            'dry_run' => $dryRun,
        ]);

        $this->dbConnect($dbOptions);

        if (!$this->database->tableExists($this->config->getTableName())) {
            $this->logger->error('Table "'.$this->config->getTableName().'" not exists');
            throw new \RuntimeException('Table "'.$this->config->getTableName().'" not exists');
        }

        $columnMapping = $this->config->getColumnMapping();
        $csvOpts = [
            'filename' => $csvFilename,
            'with_headers' => true, // Treats the first line as a header line
        ];
        $errors = [];
        $batchSize = 1; // TODO: should be moved to the config
        $batch = [];
        $rowNum = 0;
        $inserted = 0;
        $skipped = 0;

        foreach ($this->reader->next($csvOpts) as $row) {
            [$rowNum, $rowData] = $row;
            $isDataValid = true;
            $dataToInsert = [];

            foreach ($rowData as $columnName => $columnValue) {
                // 1. Validate value
                $validators= $columnMapping[$columnName]['validator'] ?? [];
                try {
                    $this->validatorManager->validate($columnValue, $validators);
                } catch (InvalidValueException $invalidValueException) {
                    $errors[] = $invalidValueException->getMessage() . ' ' . $columnName . '=\'' . $columnValue . '\' at line ' . ($rowNum + 1);
                    $isDataValid = false;
                    $this->logger->warning('Invalid value at the "' . $columnName . '" column value=[' . $columnValue . '] a row will be skipped', [
                        'validator' => $invalidValueException->getValidator()->getName(),
                        'row' => $rowData,
                        'row_num' => $rowNum,
                        'column_value' => $columnValue,
                        'column_name' => $columnName,
                        'error' => $invalidValueException->getMessage(),
                    ]);
                    $skipped++;
                    break; // The value is invalid, skip the row
                }

                // 2. Transform value
                // Perhaps would be better to move a transformation block above a validation block.
                // It will give a chance to fix invalid values.
                $transformers = $columnMapping[$columnName]['transformer'] ?? [];
                $dataToInsert[$columnName] = $this->transformerManager->transformer($columnValue, $transformers);
            }

            if ($isDataValid && !$dryRun) {
                $batch[] = $dataToInsert;
                if (\count($batch) === $batchSize) {
                    [$opCode, $opMessage] = $this->insertBatch($batch);
                    if (self::INSERT_CODE_OK === $opCode) {
                        $inserted += \count($batch);
                    } else {
                        $errors[] = $opMessage . ' at line ' . ($rowNum + 1);
                        $this->logger->error('Could not insert a batch: ' . $opMessage . ' at line ' . ($rowNum + 1));
                    }
                    $batch = [];
                }
            }
        }

        if (!$dryRun) {
            [$opCode, $opMessage] = $this->insertBatch($batch);
            if (self::INSERT_CODE_OK === $opCode) {
                $inserted += \count($batch);
            } else {
                $errors[] = $opMessage . ' at line ' . ($rowNum + 1);
                $this->logger->error('Could not insert a batch: ' . $opMessage . ' at line ' . ($rowNum + 1));
            }
        }

        $this->logger->debug('After upload', [
            'inserted_rows' => $inserted,
            'processed_rows' => $rowNum,
            'skipped_rows' => $skipped,
        ]);

        return new UploadResult($inserted, $rowNum, $skipped, $errors);
    }

    /**
     * @param array $batch
     * @return array<int, string|null> <operationCode, message>
     */
    private function insertBatch(array $batch): array
    {
        try {
            $this->database->insertBatch($this->config->getTableName(), $batch);
            return [self::INSERT_CODE_OK, null];
        } catch (\PDOException $exception) {
            $errCode = $exception->errorInfo[1] ?? null;
            if ($errCode === 1062) {
                return [self::INSERT_CODE_DUPLICATE, $exception->errorInfo[2] ?? $exception->getMessage()];
            }
            throw $exception;
        }
    }

    public function createTable(array $dbOptions): void
    {
        $this->dbConnect($dbOptions);
        if ($this->database->tableExists($this->config->getTableName())) {
            $this->logger->error('Could not create table: ' .
                'the table "'.$this->config->getTableName().'" already exists');
            throw new \RuntimeException('The table "'.$this->config->getTableName().'" already exists');
        }
        $this->database->createTable($this->config->getTableName(), $this->config->getColumnMapping());
        $this->logger->debug('The table "' . $this->config->getTableName() . '" has been created');
    }

    private function dbConnect(array $dbOptions): void
    {
        if (!$this->database->open($dbOptions)) {
            $this->logger->error('Could not connect to database: ' . $this->database->getLastError());
            throw new \RuntimeException('Could not connect to the database, please check connection options. ' .
                $this->database->getLastError());
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
