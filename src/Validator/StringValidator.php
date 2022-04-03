<?php

namespace Pada\CatalystScriptTest\Validator;

final class StringValidator implements ValidatorInterface
{
    private const NAME = 'string';

    public function getName(): string
    {
        return self::NAME;
    }

    public function validate($value, array $options): void
    {
        if (!\is_string($value)) {
            throw new InvalidValueException($this,'Expected string');
        }

        $minLength = $options['min_length'] ?? 0;
        $maxLength = $options['max_length'] ?? PHP_INT_MAX;

        $len = \strlen(\trim($value));

        if ($len < $minLength) {
            throw new InvalidValueException($this, 'A string length must be > ' . $minLength);
        }

        if ($len > $maxLength) {
            throw new InvalidValueException($this, 'A string length must be < ' . $maxLength);
        }
    }
}
