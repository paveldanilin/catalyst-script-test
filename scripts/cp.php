<?php

$src_file = $argv[1] ?? null;
$dst_file = $argv[2] ?? null;

if (empty($src_file)) {
    throw new Exception('Source file not defined');
}

if (empty($dst_file)) {
    throw new Exception('Destination file not defined');
}

if (!is_file($src_file)) {
    throw new Exception("'$src_file' is not file");
}

copy($src_file, $dst_file);

