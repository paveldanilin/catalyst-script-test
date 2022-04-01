<?php

namespace Pada\CatalystScriptTest\Console;

class ColoredOutput implements OutputInterface
{
    public const OUTPUT_COLOR = 'color';

    public const COLOR_RED = "\033[31m";
    public const COLOR_GREEN = "\033[32m";
    public const COLOR_YELLOW = "\033[33m";

    public function writeln(string $text, array $options = []): void
    {
        $colorBegin = $options[self::OUTPUT_COLOR] ?? '';
        $colorEnd = $colorBegin === '' ? '' : "\033[0m";

        echo $this->getColorCode($colorBegin) . $text . $colorEnd . "\n";
    }

    private function getColorCode(string $colorName): string
    {
        switch (\strtolower($colorName)) {
            case 'red':
                return self::COLOR_RED;
            case 'yellow':
                return self::COLOR_YELLOW;
            case 'green':
                return self::COLOR_GREEN;
        }
        return '';
    }
}
