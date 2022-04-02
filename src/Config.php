<?php

namespace Pada\CatalystScriptTest;

final class Config implements ConfigInterface
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
}
