<?php

namespace Pada\CatalystScriptTest\Transformer;

final class StringUcfirstTransformer extends AbstractStringTransformer
{
    private const NAME = 'ucfirst';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function doTransform(string $value): string
    {
        return \ucfirst($value);
    }
}
