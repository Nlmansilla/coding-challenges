<?php

namespace Wc;

class OptionParser
{
    private static array $userOptions = [];
    private static int $restIndex = 0;

    public static function getUSerOptions(): UserOptions
    {
        return new UserOptions(...self::parse());
    }

    public static function parse(): array
    {
        $args = getopt(
            implode('',Options::getShortOptions())
            , Options::getLongOptions(),
            self::$restIndex
        );
        foreach ($args as $key => $value) {
            self::$userOptions[] = match($key) {
                Options::MODE_LINES->value, Options::MODE_LINES_LONG->value => Options::MODE_LINES,
                Options::MODE_BYTES->value, Options::MODE_BYTES_LONG->value => Options::MODE_BYTES,
                Options::MODE_WORDS->value, Options::MODE_WORDS_LONG->value => Options::MODE_WORDS,
                Options::MODE_CHARACTERS->value, Options::MODE_CHARACTERS_LONG->value => Options::MODE_CHARACTERS,
                default => null,
            };
        }

        $files = array_slice($_SERVER['argv'], self::$restIndex);

        return [
            'modes' => array_unique(self::$userOptions, SORT_REGULAR),
            'files' => empty($files) ? [STDIN] : $files,
            'readFrom' => empty($files) ? Options::STDIN_MODE : Options::FILES_MODE
        ];
    }
}