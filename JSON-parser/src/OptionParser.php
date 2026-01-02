<?php

namespace Nicolas\JsonParser;

use Exception;

class OptionParser
{
    /**
     * @throws Exception
     */
    public static function parse(): string
    {
        $file = array_last($_SERVER['argv']);

        if (!is_readable($file)) {
            throw new Exception('Unable to read: ' . $file);
        }

        return $file;
    }
}
