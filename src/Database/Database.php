<?php

namespace Pada\CatalystScriptTest\Database;

/**
 * Wraps PDO object
 */
class Database implements DatabaseInterface
{
    public const DRIVER_MYSQL = 'mysql';
    public const DRIVER_SQLITE = 'sqlite';

    private string $driver;
    private ?\PDO $db;
    private ?string $lastError;

    public function __construct()
    {
        $this->db = null;
        $this->lastError = null;
    }

    public function open(array $options): bool
    {
        if (null === $this->db) {
            return $this->doOpen($options);
        }
        return true;
    }

    public function createTable(string $name, array $columns): bool
    {
        if (null === $this->db) {
            $this->lastError = 'No Database connection';
            return false;
        }
        $this->db->exec($this->getCreateTableSQL($name, $columns));
        return true;
    }

    public function tableExists(string $name): bool
    {
        try {
            $result = $this->db->query("SELECT 1 FROM {$name} LIMIT 1");
        } catch (\Exception $e) {
            return false;
        }
        // false|PDOStatement
        return $result !== false;
    }

    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db->commit();
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    private function doOpen(array $options): bool
    {
        try {
            $this->db = $this->createConnection($options);
            return true;
        } catch (\PDOException $PDOException) {
            $this->lastError = $PDOException->getMessage();
            return false;
        }
    }

    private function createConnection(array $options): \PDO
    {
        $this->driver = \strtolower(\trim($options['driver'] ?? ''));
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                return $this->mysqlConnection($options);
            case self::DRIVER_SQLITE:
                return $this->sqliteConnection($options);
        }
        throw new \OutOfRangeException('Unknown database driver [' . $this->driver . ']');
    }

    private function mysqlConnection(array $options): \PDO
    {
        $host = $this->requireOption('host', $options);
        $user = $this->requireOption('user', $options);
        $pass = $this->requireOption('password', $options);
        $dbname = $this->requireOption('dbname', $options);
        $extraOptions = $options['options'] ?? [];
        return new \PDO("mysql:host=$host;dbname=$dbname", $user, $pass, $extraOptions);
    }

    private function sqliteConnection(array $options): \PDO
    {
        $path = $this->requireOption('path', $options);
        $extraOptions = $options['options'] ?? [];
        return new \PDO('sqlite:' . $path, null, null, $extraOptions);
    }

    private function getCreateTableSQL(string $name, array $columns): string
    {
        $ddlColumns = [];
        foreach ($columns as $column => $options) {
            $ddlColumns[] = $this->ddlColumn($column, $options);
        }
        return "CREATE TABLE IF NOT EXISTS $name (" . \implode(',', $ddlColumns) . ")";
    }

    private function ddlColumn(string $columnName, array $columnOptions): string
    {
        $type = $this->getDriverStringType($this->driver, $columnOptions['type'] ?? 'string');
        $nullable = ($columnOptions['nullable'] ?? true) === true ? '' : ' NOT NULL';
        $unique = ($columnOptions['unique'] ?? false) === true ? ' UNIQUE' : '';
        return $columnName . ' ' . $type . $nullable . $unique;
    }

    private function getDriverStringType(string $driver, int $len = 255): string
    {
        switch ($driver) {
            case self::DRIVER_SQLITE:
                return 'TEXT';
            case self::DRIVER_MYSQL:
                return "VARCHAR($len)";
        }
    }

    private function requireOption(string $optName, array $options): string
    {
        $val = \trim($options[$optName] ?? '');
        if (empty($val)) {
            throw new \InvalidArgumentException("[{$this->driver}] option '$optName' must be non empty string");
        }
        return $val;
    }
}
