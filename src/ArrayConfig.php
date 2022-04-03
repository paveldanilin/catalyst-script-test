<?php

namespace Pada\CatalystScriptTest;

final class ArrayConfig implements ConfigInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getColumnMapping(): array
    {
        return $this->config['columnMapping'] ?? [];
    }

    public function getTableName(): string
    {
        return $this->config['importTableName'] ?? '';
    }

    public function getCsvSeparator(): string
    {
        return $this->config['csvSeparator'] ?? ',';
    }

    public function getLogDir(): string
    {
        return $this->config['log']['dir'] ?? '.' . DIRECTORY_SEPARATOR;
    }

    public function getLogFilename(): string
    {
        return $this->config['log']['filename'] ?? 'application.log';
    }
}
