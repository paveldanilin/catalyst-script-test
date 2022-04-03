<?php

namespace Pada\CatalystScriptTest\Reader;

interface ReaderInterface
{
    /**
     * @param array $options
     * @return \Generator<array<int, array<string, string>|int|false>>
     */
    public function next(array $options): \Generator;
}
