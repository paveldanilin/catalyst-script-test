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

    public function addValidator(ValidatorInterface $validator): self
    {
        $this->validators[$validator->getName()] = $validator;
        return $this;
    }

    /**
     * @param mixed $value
     * @param array<string> $validatorStack
     * @return void
     */
    public function validate($value, array $validatorStack): void
    {
        foreach ($validatorStack as $validatorName) {
            $this->getValidator($validatorName)->validate($value);
        }
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
