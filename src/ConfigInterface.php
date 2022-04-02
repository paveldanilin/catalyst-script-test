<?php

namespace Pada\CatalystScriptTest;

interface ConfigInterface
{
    public function getColumnMapping(): array;
    public function getTableName(): string;
}
