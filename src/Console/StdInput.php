<?php

namespace Pada\CatalystScriptTest\Console;

class StdInput implements InputInterface
{
    /** @var array<OptionValue> */
    private array $options;

    /**
     * @param array<OptionValue> $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function hasOption(string $optionName): bool
    {
        return $this->getOption($optionName) !== null;
    }

    public function getOption(string $optionName): ?OptionValue
    {
        foreach ($this->options as $option) {
            if ($option->getDefinition()->getLongName() === $optionName || $option->getDefinition()->getShortName() === $optionName) {
                return $option;
            }
        }
        return null;
    }
}
