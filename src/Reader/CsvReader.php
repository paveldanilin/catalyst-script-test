<?php

namespace Pada\CatalystScriptTest\Reader;

class CsvReader implements ReaderInterface
{
    private const MAX_LINE_LENGTH = 10000;
    private const DEFAULT_SEPARATOR = ',';

    private string $separator;

    public function __construct(string $separator = self::DEFAULT_SEPARATOR)
    {
        $this->separator = $separator;
    }

    // Going to use a Generator feature since we don't want to read all file in memory.
    public function nextRow(array $options): \Generator
    {
        // A path to the file
        $filename = $this->requireOption('filename', $options);
        if (!\file_exists($filename)) {
            throw new \RuntimeException('File not found [' . $filename . ']');
        }

        // Consider the first line as a headers
        $withHeaders = $this->extractOption('with_headers', $options, false);
        $rowNum = 0;
        $headers = [];

        $file = \fopen($filename, 'rb');
        if (false === $file) {
            throw new \RuntimeException('Could not open file');
        }

        while (($row = \fgetcsv($file, self::MAX_LINE_LENGTH, $this->separator)) !== false) {
            // Normalize values
            $row = \array_map(static function ($headerName) {
                return \trim($headerName);
            }, $row);

            // Headers definition
            if ($withHeaders && 0 === $rowNum) {
                $headers = $row;
                $rowNum++;
                continue;
            }

            if ($withHeaders) {
                yield \array_combine($headers, $row);
            } else {
                yield $row;
            }
            $rowNum++;
        }

        \fclose($file);
    }

    /**
     * @param string $optionName
     * @param array $options
     * @param mixed $def
     * @return mixed
     */
    private function requireOption(string $optionName, array $options, $def = null)
    {
        if (!\array_key_exists($optionName, $options)) {
            throw new \InvalidArgumentException('Not found "'.$optionName.'" option');
        }
        return $this->extractOption($optionName, $options, $def);
    }

    /**
     * @param string $optionName
     * @param array $options
     * @param mixed $def
     * @return mixed|null
     */
    private function extractOption(string $optionName, array $options, $def = null)
    {
        return $options[$optionName] ?? $def;
    }
}
