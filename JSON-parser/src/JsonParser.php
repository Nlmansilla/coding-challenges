<?php

namespace Nicolas\JsonParser;

use Exception;

class JsonParser
{
    private readonly Lexer $lexer;
    private Parser $parser;

    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->parser = new Parser();
    }

    public function main(): int
    {
        try {
            $file = OptionParser::parse();
            $content = file_get_contents($file);

            if (empty($content)) {
                throw new Exception('File is empty');
            }

            $tokens = $this->lexer->tokenize($content);
            $this->parser->tokens = $tokens;
            $this->parser->parse();
            echo sprintf("File %s contains a valid JSON\n", $file);
            return 0;
        } catch (Exception $e) {
            echo sprintf("Error: %s\n", $e->getMessage());
            return 1;
        }
    }
}
