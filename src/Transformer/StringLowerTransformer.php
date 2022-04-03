<?php

namespace Pada\CatalystScriptTest\Transformer;

final class StringLowerTransformer extends AbstractStringTransformer
{
    private const NAME = 'lower';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function doTransform(string $value): string
    {
        return \strtolower($value);
    }
}
