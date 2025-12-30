<?php

namespace Nicolas\JsonParser;

class JsonParser
{
    private static Lexer $lexer;
    private static Parser $parser;

    public function __construct()
    {
        self::$lexer = new Lexer;
    }

    static function main()
    {
        var_dump("hello world");
        #$tokens = self::$lexer->tokenize('{"key":"value", "key2": {"key3": "value3"}, "key5": 102, "key6": [true], ["key-a":[1,2,3,4] }');
        #$tokens = self::$lexer->tokenize(file_get_contents(__DIR__ . '/tests/step2/valid.json'));
//        $tokens = self::$lexer->tokenize(file_get_contents(__DIR__ . '/tests/step3/valid.json'));
        $tokens = self::$lexer->tokenize(file_get_contents(__DIR__ . '/tests/step4/valid.json'));
        self::$parser = new Parser($tokens);

        $ast = self::$parser->parse();

        var_dump($tokens);
        var_dump($ast);
    }
}