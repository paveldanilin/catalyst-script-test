<?php

function cpdir(string $src, string $dst): void
{
    $dir = opendir($src);
    if (!mkdir($dst) && !is_dir($dst)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dst));
    }

    foreach (scandir($src) as $file) {
        if (($file !== '.') && ($file !== '..')) {
            if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                cpdir($src. DIRECTORY_SEPARATOR . $file , $dst . DIRECTORY_SEPARATOR . $file);
            } else {
                copy($src. DIRECTORY_SEPARATOR . $file , $dst . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    closedir($dir);
}

$src_dir = $argv[1] ?? null;
$dest_dir = $argv[2] ?? null;

if (empty($src_dir)) {
    throw new Exception('Source directory is not defined');
}

if (empty($dest_dir)) {
    throw new Exception('Destination directory is not defined');
}

cpdir($src_dir, $dest_dir);
