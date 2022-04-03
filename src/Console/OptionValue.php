<?php

namespace Pada\CatalystScriptTest\Console;

final class OptionValue
{
    private OptionDefinition $definition;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param OptionDefinition $definition
     * @param mixed $value
     */
    public function __construct(OptionDefinition $definition, $value)
    {
        $this->definition = $definition;
        $this->value = $value;
    }

    public function getDefinition(): OptionDefinition
    {
        return $this->definition;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
