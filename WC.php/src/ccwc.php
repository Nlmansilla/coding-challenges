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

$counts = [];

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
$restIndex = 0;
$args = getopt($shortOpts, $longOpts, $restIndex);
$filepath = array_slice($_SERVER['argv'], $restIndex);

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
 * @param array $userOpts
 * @param int|null $lines
 * @param int|null $bytes
 * @param int|null $words
 * @param int|null $chars
 */
function analyzeFile(?string $filepath, array $userOpts, ?int &$lines, ?int &$bytes, ?int &$words, ?int &$chars): array
{
    $readContentFrom = hasStdinData() ? MODE_STDIN : MODE_FILES;
    $localCount = getEmptyArray();
    $f = $readContentFrom === MODE_FILES ? fopen($filepath, 'r') : STDIN;
    $buffer = '';

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
                        $bytes += strlen($buffer);
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

    $localCount[MODE_BYTES] = $bytes ?? 0;
    $localCount[MODE_LINES] = $lines ?? 0;
    $localCount[MODE_WORDS] = $words ?? 0;
    $localCount[MODE_CHARACTERS] = $chars ?? 0;
    return $localCount;
}

function hasStdinData(): bool {
    $read = [STDIN];
    $write = null;
    $except = null;

    $result = stream_select($read, $write, $except, 0);

    return $result > 0;
}

function formatOutput(array $counts): void
{
    $content = '';
    foreach ($counts as $file => $count) {
        $content .= implode(" ", $count) . " {$file}" . PHP_EOL;
    }
    echo $content;
}

foreach ($filepath as $file) {
    $counts[$file] = analyzeFile($file, $userOpts, $lines, $bytes, $words, $chars);
}

formatOutput($counts);

function getEmptyArray(): array
{
    return [
        MODE_BYTES => 0,
        MODE_WORDS => 0,
        MODE_CHARACTERS => 0,
        MODE_LINES => 0
    ];
}