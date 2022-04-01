<?php

namespace Pada\CatalystScriptTest\Console;

final class OptionDefinition
{
    public const VALUE_OPTIONAL = 0;
    public const VALUE_REQUIRED = 1;
    public const VALUE_NONE = 2;

    private ?string $shortName;
    private ?string $longName;
    private int $valueMode;
    /** @var callable|null */
    private $commandHandler;
    private ?string $description;

    public function __construct(?string $shortName, ?string $longName, int $valueMode, ?string $description, ?callable $commandHandler = null)
    {
        $this->setShortName($shortName);
        $this->setLongName($longName);
        if (!$this->hasShortName() && ! $this->hasLongName()) {
            throw new \InvalidArgumentException('At least Short or Long name must be defined');
        }
        $this->valueMode = $valueMode;
        $this->commandHandler = $commandHandler;
        $this->description = $description;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function hasShortName(): bool
    {
        return null !== $this->shortName;
    }

    private function setShortName(?string $shortName): void
    {
        if (null !== $shortName) {
            $shortName = \trim($shortName);
            if (empty($shortName)) {
                throw new \InvalidArgumentException('Short option name must be null or a non empty string');
            }
            if (\strlen($shortName) > 1) {
                throw new \InvalidArgumentException('Expected one letter');
            }
        }
        $this->shortName = $shortName;
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function hasLongName(): bool
    {
        return null !== $this->longName;
    }

    private function setLongName(?string $longName): void
    {
        if (null !== $longName) {
            $longName = \trim($longName);
            if (empty($longName)) {
                throw new \InvalidArgumentException('Long option name must be null or a non empty string');
            }
            if (\strlen($longName) === 1) {
                throw new \InvalidArgumentException('Expected an option length > 1');
            }
        }
        $this->longName = $longName;
    }

    public function getValueMode(): int
    {
        return $this->valueMode;
    }

    public function getCommandHandler(): ?callable
    {
        return $this->commandHandler;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
