<?php

namespace Pada\CatalystScriptTest\Transformer;

abstract class AbstractStringTransformer implements TransformerInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract protected function doTransform(string $value): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function transform($value)
    {
        if (!\is_string($value)) {
            throw new \InvalidArgumentException('Expected string');
        }
        return $this->doTransform((string)$value);
    }
}
