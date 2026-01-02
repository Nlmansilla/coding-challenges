<?php

namespace Nicolas\JsonParser\tests;

use Nicolas\JsonParser\JsonParser;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class JsonParserTest extends TestCase
{
    public const string COMMAND = './jsonparser';
    public const string OFFICIAL_SUITE = __DIR__ . '/official_suite';

    protected function setUp(): void
    {
        parent::setUp();
        unset($_SERVER['argv']);
    }

    public function testStep1Valid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step1/valid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep1Invalid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step1/invalid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(1, $result);
    }

    public function testStep2Valid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step2/valid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep2Valid2(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step2/valid2.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep2Invalid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step2/invalid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(1, $result);
    }

    public function testStep2Invalid2(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step2/invalid2.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(1, $result);
    }

    public function testStep3Invalid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step3/invalid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(1, $result);
    }

    public function testStep3Valid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step3/valid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep4Valid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step4/valid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep4Valid2(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step4/valid2.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(0, $result);
    }

    public function testStep4Invalid(): void
    {
        $_SERVER['argv'] = [self::COMMAND, __DIR__ . '/step4/invalid.json'];
        $parser = new JSONParser();
        $result = $parser->main();
        assertEquals(1, $result);
    }

    public function testOfficialSuite(): void
    {
        // It fails with the structure from fail18.json file.
        // The reference said that the parser should return 0, since it is a valid JSON, but in the suite it is marked as invalid.
        $files = scandir(self::OFFICIAL_SUITE);
        $files = array_slice($files, 2);

        foreach ($files as $file) {
            unset($_SERVER['argv']);
            $_SERVER['argv'] = [self::COMMAND, self::OFFICIAL_SUITE . "/$file"];
            $parser = new JSONParser();
            $res = $parser->main();
            if (str_starts_with($file, 'pass')) {
                assertEquals(0, $res, $file);
            } else {
                assertEquals(1, $res, $file);
            }

        }
    }
}
