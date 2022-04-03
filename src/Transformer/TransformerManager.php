<?php

namespace Pada\CatalystScriptTest\Transformer;

final class TransformerManager implements TransformerManagerInterface
{
    private array $transformers;

    public function __construct()
    {
        $this->transformers = [];
    }

    public function addTransformer(TransformerInterface $transformer): self
    {
        $this->transformers[$transformer->getName()] = $transformer;
        return $this;
    }

    public function getTransformer(string $name): TransformerInterface
    {
        $t = $this->transformers[$name] ?? null;
        if (null === $t) {
            throw new \OutOfRangeException('Transformer not found');
        }
        return $t;
    }

    public function transformer($value, array $transformerStack, bool $throws = false)
    {
        $out = $value;
        foreach ($transformerStack as $transformerName) {
            try {
                $out = $this->getTransformer($transformerName)->transform($out);
            } catch (\Exception $exception) {
                if ($throws) {
                    throw $exception;
                }
            }
        }
        return $out;
    }
}
