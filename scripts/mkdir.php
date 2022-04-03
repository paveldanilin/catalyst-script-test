<?php

$cwd = getcwd();
$dir = $argv[1] ?? null;

if (null === $dir) {
    throw new Exception('Directory is not defined');
}

$mkdir_path = $cwd . DIRECTORY_SEPARATOR . $dir;

if (!mkdir($mkdir_path, 0777, true) && !is_dir($mkdir_path)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $mkdir_path));
}

