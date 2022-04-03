<?php

namespace Pada\CatalystScriptTest\Transformer;

interface TransformerInterface
{
    public function getName(): string;

    /**
     * @param mixed $value
     * @return mixed
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function transform($value);
}
