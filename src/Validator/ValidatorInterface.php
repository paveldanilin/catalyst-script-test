<?php

namespace Pada\CatalystScriptTest\Validator;

interface ValidatorInterface
{
    public function getName(): string;

    /**
     * @param mixed $value
     * @return void
     * @throws InvalidValueException
     */
    public function validate($value): void;
}
