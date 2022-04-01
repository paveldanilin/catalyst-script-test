<?php

namespace Pada\CatalystScriptTest\Console;

interface InputInterface
{
    /**
     * @return array<OptionValue>
     */
    public function getOptions(): array;
    public function hasOption(string $optionName): bool;
    public function getOption(string $optionName): ?OptionValue;
}
