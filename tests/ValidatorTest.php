<?php

use Pada\CatalystScriptTest\Validator\EmailValidator;
use Pada\CatalystScriptTest\Validator\InvalidValueException;
use Pada\CatalystScriptTest\Validator\StringValidator;
use Pada\CatalystScriptTest\Validator\ValidatorManager;
use Pada\CatalystScriptTest\Validator\ValidatorManagerInterface;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private static ValidatorManagerInterface $validatorManager;

    public static function setUpBeforeClass(): void
    {
        self::$validatorManager = new ValidatorManager();
        self::$validatorManager->addValidator(new EmailValidator());
        self::$validatorManager->addValidator(new StringValidator());
    }

    public function testEmailValid(): void
    {
        self::$validatorManager->validate('pasha@mail.com', ['email']);
        self::assertTrue(true);
    }

    public function testEmailInvalid(): void
    {
        $this->expectException(InvalidValueException::class);
        self::$validatorManager->validate('pasha@pasha@mail.com', ['email']);
    }

    public function testStringTooShort(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A string length must be > 100');
        self::$validatorManager->validate('too-short-string', ['string' => ['min_length' => 100]]);
    }

    public function testStringTooLong(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A string length must be < 10');
        self::$validatorManager->validate('zzzzbbbbbccccccaaaaassss', ['string' => ['max_length' => 10]]);
    }
}
