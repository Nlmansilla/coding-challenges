<?php

namespace Wc;

class Analyzer
{
    public static function analyze(string $content, Options $mode): false|int
    {
        return match ($mode) {
            Options::MODE_BYTES => strlen($content),
            Options::MODE_LINES => substr_count($content, "\n"),
            Options::MODE_WORDS => preg_match_all('/[\s]+/', $content),
            Options::MODE_CHARACTERS => mb_strlen($content),
            default => false
        };
    }
}