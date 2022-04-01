<?php

namespace Pada\CatalystScriptTest\Database;

interface DatabaseInterface
{
    public function open(array $options): bool;
    public function createTable(string $name, array $columns): bool;
    public function tableExists(string $name): bool;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function getLastError(): ?string;
}
