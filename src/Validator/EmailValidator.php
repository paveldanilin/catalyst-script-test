<?php

namespace Pada\CatalystScriptTest\Validator;


final class EmailValidator implements ValidatorInterface
{
    private const NAME = 'email';
    private const PATTERN = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    public function getName(): string
    {
        return self::NAME;
    }

    public function validate($value, array $options): void
    {
        if (!\is_string($value)) {
            throw new InvalidValueException($this,'Email must be a string');
        }

        $value = (string)$value;
        $value = \trim($value);
        if (empty($value)) {
            throw new InvalidValueException($this,'Email must be non empty string');
        }

        if (!\preg_match(self::PATTERN, $value)) {
            throw new InvalidValueException($this,'Email is not valid');
        }
    }
}
