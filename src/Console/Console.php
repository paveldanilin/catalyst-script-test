<?php

namespace Pada\CatalystScriptTest\Console;

final class Console implements ConsoleInterface
{
    private string $name;
    private OutputInterface $output;
    /** @var array<OptionDefinition> */
    private array $definitions;

    public function __construct(string $name, ?OutputInterface $output = null)
    {
        $this->name = $name;
        $this->output = $output ?? new ColoredOutput();
        $this->definitions = [
            new OptionDefinition(null,
                'help',
                OptionDefinition::VALUE_NONE,
                'Help',
                fn(OutputInterface $output, InputInterface $input) => $this->helpCommand($output, $input)
            )
        ];
    }

    public function addDefinition(OptionDefinition $definition): self
    {
        $this->definitions[] = $definition;
        return $this;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return array<OptionValue>
     */
    public function parse(array $argv): array
    {
        $shortOptions = '';
        $longOptions = [];
        foreach ($this->definitions as $definition) {
            if ($definition->hasShortName()) {
                $shortOptions .=  $this->getOpt($definition->getShortName(), $definition->getValueMode());
            }
            if ($definition->hasLongName()) {
                $longOptions[] = $this->getOpt($definition->getLongName(), $definition->getValueMode());
            }
        }

        $optsKV = \getopt($shortOptions, $longOptions);
        if (false === $optsKV) {
            throw new OptParsingException();
        }
        foreach ($argv as $value) {
            if (\strpos($value, '--') === 0 && !\array_key_exists(\substr($value, 2), $optsKV)) {
                $optsKV[\substr($value, 2)] = null;
            } elseif (\strpos($value, '-') === 0 && !\array_key_exists(\substr($value, 1), $optsKV)) {
                $optsKV[\substr($value, 1)] = null;
            }
        }

        $opts = [];
        foreach ($this->definitions as $definition) {
            $k = $definition->getShortName() ?? $definition->getLongName();
            if (\array_key_exists($k, $optsKV)) {
                if ($optsKV[$k] === null && $definition->getValueMode() === OptionDefinition::VALUE_REQUIRED) {
                    throw new OptParsingException('Option [' . $k . '] value is required');
                }
                $opts[] = new OptionValue($definition, $optsKV[$k]);
            }
        }

        return $opts;
    }

    public function run(array $argv): void
    {
        try {
            $optValues = $this->parse($argv);

            /** @var array<OptionValue> $withHandler */
            $withHandler = \array_filter($optValues, static function (OptionValue $optVal) {
                return $optVal->getDefinition()->getCommandHandler() !== null;
            });

            if (empty($withHandler)) {
                $this->output->writeln('Nothing to do, use [--help] directive to see usage', ['color' => 'yellow']);
                return;
            }

            $cmd = $withHandler[0]->getDefinition()->getCommandHandler();
            $cmd($this->output, new StdInput($optValues));
        } catch (\Exception $exception) {
            $this->output->writeln('[ERROR] ' . $exception->getMessage(), ['color' => 'red']);
        }
    }

    private function getOpt(string $name, int $valueMode): string
    {
        $opt = $name;
        switch ($valueMode) {
            case OptionDefinition::VALUE_REQUIRED:
                $opt .= ':';
                break;
            case OptionDefinition::VALUE_OPTIONAL:
                $opt .= '::';
                break;
            case OptionDefinition::VALUE_NONE:
                // Nothing to do
                break;
        }
        return $opt;
    }

    private function helpCommand(OutputInterface $output, InputInterface $input): void
    {
        $buffer = ['usage: ' . $this->name . ' ' . $this->getUsage()];

        $shortDefs = \array_filter($this->definitions, static function (OptionDefinition $definition) {
            return $definition->getShortName() !== null;
        });
        $longDefs = \array_filter($this->definitions, static function (OptionDefinition $definition) {
            return $definition->getLongName() !== null;
        });

        foreach ($shortDefs as $shortDef) {
            $buffer[] = "\t[-" . $shortDef->getShortName() . $this->stringifyOptionValueMode($shortDef->getValueMode()) . "] " . $shortDef->getDescription();
        }
        foreach ($longDefs as $longDef) {
            if ($longDef->getLongName() === 'help') {
                continue;
            }
            $buffer[] = "\t[--" . $longDef->getLongName() . $this->stringifyOptionValueMode($longDef->getValueMode()) . "] " . $longDef->getDescription();
        }

        foreach ($buffer as $line) {
            $output->writeln($line);
        }
    }

    private function getUsage(): string
    {
        $str = '';
        $shortDefs = \array_filter($this->definitions, static function (OptionDefinition $definition) {
            return $definition->getShortName() !== null;
        });
        $longDefs = \array_filter($this->definitions, static function (OptionDefinition $definition) {
            return $definition->getLongName() !== null;
        });

        foreach ($shortDefs as $shortDef) {
            $str .= ' [-' . $shortDef->getShortName() . $this->stringifyOptionValueMode($shortDef->getValueMode()) . ']';
        }
        foreach ($longDefs as $longDef) {
            $str .= ' [--' . $longDef->getLongName() . $this->stringifyOptionValueMode($longDef->getValueMode()) . ']';
        }

        return $str;
    }

    private function stringifyOptionValueMode(int $valueMode): string
    {
        $v = '';
        if ($valueMode === OptionDefinition::VALUE_REQUIRED) {
            $v = '=value';
        } elseif($valueMode === OptionDefinition::VALUE_OPTIONAL) {
            $v = '[=value]';
        }
        return $v;
    }
}
