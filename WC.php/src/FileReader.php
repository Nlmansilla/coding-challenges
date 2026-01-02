<?php

namespace Wc;


class FileReader
{
    static function readFile(string $path, Options $readMode = Options::FILES_MODE, int $bufferSize = 16 * 1024 * 1024): iterable
    {
        $f = ($readMode === Options::FILES_MODE) ? fopen($path, 'r') : STDIN;
        while (!feof($f)) {
            $buffer = fread($f, $bufferSize);
            if ($buffer === false) {
                continue;
            }
            yield $buffer;
        }
        fclose($f);
    }
}