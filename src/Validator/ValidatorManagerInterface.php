<?php

namespace Pada\CatalystScriptTest\Validator;

interface ValidatorManagerInterface
{
    public function getValidator(string $name): ValidatorInterface;
}
