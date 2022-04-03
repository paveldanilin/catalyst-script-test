<?php

namespace Pada\CatalystScriptTest\Validator;

class InvalidValueException extends \RuntimeException
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator, string $message)
    {
        parent::__construct($message);
        $this->validator = $validator;
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
