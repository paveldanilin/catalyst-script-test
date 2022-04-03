<?php

use Pada\CatalystScriptTest\Transformer\StringLowerTransformer;
use Pada\CatalystScriptTest\Transformer\StringUcfirstTransformer;
use Pada\CatalystScriptTest\Transformer\TransformerManager;
use Pada\CatalystScriptTest\Transformer\TransformerManagerInterface;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    private static TransformerManagerInterface $transformerManager;

    public static function setUpBeforeClass(): void
    {
        self::$transformerManager = new TransformerManager();
        self::$transformerManager->addTransformer(new StringLowerTransformer());
        self::$transformerManager->addTransformer(new StringUcfirstTransformer());
    }

    public function testLower(): void
    {
        $str = 'ABC';
        $transformed = self::$transformerManager->transform($str, ['lower']);
        self::assertEquals('abc', $transformed);
    }

    public function testUcfirst(): void
    {
        $str = 'abcd';
        $transformed = self::$transformerManager->transform($str, ['ucfirst']);
        self::assertEquals('Abcd', $transformed);
    }
}
