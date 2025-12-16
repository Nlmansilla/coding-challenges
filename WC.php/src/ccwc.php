<?php

const MODE_CHARACTERS = 'm';
const MODE_BYTES = 'c';
const MODE_WORDS = 'w';
const MODE_LINES = 'l';

const LONG_MODE_CHARACTERS = 'chars';
const LONG_MODE_BYTES = 'bytes';
const LONG_MODE_WORDS = 'words';
const LONG_MODE_LINES = 'lines';

const MODE_STDIN = 'stdin';
const MODE_FILES = 'files';

$shortOpts = implode([
    MODE_CHARACTERS,
    MODE_BYTES,
    MODE_WORDS,
    MODE_LINES
    ]);

$longOpts  = [
    LONG_MODE_CHARACTERS,
    LONG_MODE_BYTES,
    LONG_MODE_WORDS,
    LONG_MODE_LINES
];

$defaultOpts = [
    MODE_BYTES,
    MODE_WORDS,
    MODE_LINES
];

$lines = $bytes = $words = $chars = null;

$args = getopt($shortOpts, $longOpts);
$filepath = array_last($argv);

$userOpts = [];

foreach ($args as $key => $value) {
    $userOpts[] = match($key) {
        MODE_BYTES, LONG_MODE_BYTES => MODE_BYTES,
        MODE_LINES, LONG_MODE_LINES => MODE_LINES,
        MODE_WORDS, LONG_MODE_WORDS => MODE_WORDS,
        MODE_CHARACTERS, LONG_MODE_CHARACTERS => MODE_CHARACTERS,
        default => null,
    };
}

if (empty($userOpts)) {
    $userOpts = $defaultOpts;
}

/**
 * @param string|null $filepath
 * @param int|null $lines
 * @param array <int,string> $userOpts
 * @param int|null $bytes
 * @param int|null $words
 * @param int|null $chars
 */
function analyzeFile(?string $filepath, array $userOpts, ?int &$lines, ?int &$bytes, ?int &$words, ?int &$chars): void {
    $readContentFrom = hasStdinData() ? MODE_STDIN : MODE_FILES;

    $f = $readContentFrom === MODE_FILES ? fopen($filepath, 'r') : STDIN;
    $buffer = '';
    $justCountBytes = false;
    if(count($userOpts) === 1  && $userOpts[0] === 'c') {
        $justCountBytes = true;
    }

    if (in_array('c', $userOpts)) {
        if ($readContentFrom === MODE_FILES) {
            $filesize = filesize($filepath);
            $bytes = $filesize !== false ? $filesize : 0;
        } else {
            $bytes = 0;
        }
    }

    if ($justCountBytes && $readContentFrom === MODE_FILES) {
        return;
    }
    while (!feof($f)) {
        $buffer = fread($f, 8192);
        foreach ($userOpts as $opt) {
            switch ($opt) {
                case 'l':
                    $lines += substr_count($buffer, "\n");
                    break;
                case 'w':
                    $words += preg_match_all('/[\s]+/', $buffer);
                    break;
                case 'm':
                    $chars += mb_strlen($buffer);
                    break;
                case 'c':
                    if ($readContentFrom === MODE_STDIN) {
                        $bytes += strlen($buffer);
                    }
                    break;
            }
        }
    }
    fclose($f);
    if (strlen($buffer) > 0 && $buffer[-1] != "\n") {
        $lines++;
        $words += preg_match_all('/[\s]+/', $buffer);
        $chars += mb_strlen($buffer);
    }
}

function hasStdinData(): bool {
    $read = [STDIN];
    $write = null;
    $except = null;

    $result = stream_select($read, $write, $except, 0);

    return $result > 0;
}

function formatOutput(?int $bytes, ?int $words, ?int $chars, ?int $lines): void
{
    echo implode(" ", array_filter([$bytes ?? null , $words ?? null, $chars ?? null, $lines ?? null])) . PHP_EOL;
}

analyzeFile($filepath, $userOpts, $lines, $bytes, $words, $chars);
formatOutput($bytes, $words, $chars, $lines);