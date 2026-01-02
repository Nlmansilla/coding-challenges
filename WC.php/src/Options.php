<?php

namespace Wc;

enum Options: string
{
    case MODE_CHARACTERS = 'm';
    case MODE_BYTES = 'c';
    case MODE_WORDS = 'w';
    case MODE_LINES = 'l';

    case MODE_CHARACTERS_LONG = 'chars';
    case MODE_BYTES_LONG = 'bytes';
    case MODE_WORDS_LONG = 'words';
    case MODE_LINES_LONG = 'lines';

    case STDIN_MODE = 'stdin';
    case FILES_MODE = 'files';

    public static function getShortOptions(): array
    {
        return [
            self::MODE_CHARACTERS->value,
            self::MODE_BYTES->value,
            self::MODE_WORDS->value,
            self::MODE_LINES->value,
        ];
    }

    public static function getLongOptions(): array
    {
        return [
            self::MODE_CHARACTERS_LONG->value,
            self::MODE_BYTES_LONG->value,
            self::MODE_WORDS_LONG->value,
            self::MODE_LINES_LONG->value,
        ];
    }

    public static function getDefaultOptions(): array
    {
        return [
                self::MODE_BYTES->value,
                self::MODE_WORDS->value,
                self::MODE_LINES->value
        ];
    }
}
