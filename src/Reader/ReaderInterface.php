<?php

namespace Pada\CatalystScriptTest\Reader;

interface ReaderInterface
{
    public function nextRow(array $options): \Generator;
}
