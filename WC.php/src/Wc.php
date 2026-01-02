<?php

namespace Wc;

class Wc
{
    static UserOptions $userOptions;
    static Displayer $display;

    static function main(): int
    {
        self::$display = new Displayer;
        self::$userOptions = OptionParser::getUSerOptions();
        self::readFiles(self::$userOptions);
        self::$display->display();
        return 0;
    }

    static function readFiles(UserOptions $userOptions): void
    {
        $files = $userOptions->getFiles();

        if ($userOptions->getReadFrom() === Options::FILES_MODE) {
            array_walk($files, [static::class, 'analyzeFiles']);
        } else {
            self::analyzeFiles('');
        }
    }

    static function analyzeFiles(string $file): void
    {
        if (!is_readable($file)) {
            self::$display->addExceptionLine($file, 'No such file or directory' );
            return;
        }

        $counts[$file] = self::makeNewEmptyArray();
        foreach (FileReader::readFile($file, self::$userOptions->getReadFrom()) as $content) {

            /** @var Options $mode */
            foreach (self::$userOptions->getModes() as $mode) {
                $counts[$file][$mode->name] += Analyzer::analyze($content, $mode);
            }
        }
        foreach ($counts as $file => $count) {
            self::$display->addLine($file, $count);
        }
    }

    private static function makeNewEmptyArray(): array
    {
        $array = [];
        foreach (self::$userOptions->getModes() as $mode) {
            $array[$mode->name] = 0;
        }
        return $array;
    }
}