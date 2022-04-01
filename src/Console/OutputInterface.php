<?php

namespace Pada\CatalystScriptTest\Console;

interface OutputInterface
{
    public function writeln(string $text, array $options = []): void;
}
