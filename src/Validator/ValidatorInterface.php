<?php

namespace Pada\CatalystScriptTest\Validator;

interface ValidatorInterface
{
    /**
     * @param mixed $value
     * @return void
     * @throws InvalidValueException
     */
    public function validate($value): void;
}
