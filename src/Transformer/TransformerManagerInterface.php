<?php

namespace Pada\CatalystScriptTest\Transformer;

interface TransformerManagerInterface
{
    /**
     * @param mixed $value
     * @param array<string> $transformerStack
     * @param bool $throws
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function transform($value, array $transformerStack, bool $throws = false);

    /**
     * @param string $name
     * @return TransformerInterface
     * @throws \OutOfRangeException
     */
    public function getTransformer(string $name): TransformerInterface;
}
