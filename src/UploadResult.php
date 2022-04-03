<?php

namespace Pada\CatalystScriptTest;

final class UploadResult
{
    private array $errors;
    private int $inserted;
    private int $processed;
    private int $skipped;

    public function __construct(int $inserted, int $processed, int $skipped, array $errors)
    {
        $this->inserted = $inserted;
        $this->processed = $processed;
        $this->skipped = $skipped;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getInserted(): int
    {
        return $this->inserted;
    }

    public function getProcessed(): int
    {
        return $this->processed;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }
}
