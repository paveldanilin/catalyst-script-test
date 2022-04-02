<?php

namespace Pada\CatalystScriptTest\Validator;

final class ValidatorManager implements ValidatorManagerInterface
{
    /** @var array<string, ValidatorInterface> */
    private array $validators;

    public function __construct()
    {
        $this->validators = [];
    }

    public function addValidator(string $name, ValidatorInterface $validator): self
    {
        $this->validators[$name] = $validator;
        return $this;
    }

    public function getValidator(string $name): ValidatorInterface
    {
        $validator = $this->validators[$name] ?? null;
        if (null === $validator) {
            throw new \OutOfRangeException('Validator not found');
        }
        return $validator;
    }
}
