<?php

namespace Nicolas\JsonParser\tests;

use Nicolas\JsonParser\Parser;
use Nicolas\JsonParser\Token;
use Nicolas\JsonParser\TokenType;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private function newToken(TokenType $type, mixed $value = null): Token
    {
        return new Token($type, $value);
    }

    /**
     * @param array<Token> $tokens
     * @return bool
     */
    private function parse(array $tokens): bool
    {
        $parser = new Parser();
        $parser->tokens = $tokens;
        return $parser->parse();
    }

    public function testEmptyObject(): void
    {
        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->assertTrue($this->parse($tokens));
    }

    public function testSinglePair(): void
    {
        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING, 'a'),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::STRING, 'b'),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->assertTrue($this->parse($tokens));
    }

    public function testMultiplePairs(): void
    {
        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING, 'a'),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::NUMBER, '1'),
            $this->newToken(TokenType::COMMA),
            $this->newToken(TokenType::STRING, 'b'),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::TRUE),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->assertTrue($this->parse($tokens));
    }

    public function testNestedObject(): void
    {
        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING, 'a'),
            $this->newToken(TokenType::COLON),

            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING, 'b'),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::NULL),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),

            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->assertTrue($this->parse($tokens));
    }

    public function testArray(): void
    {
        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING, 'a'),
            $this->newToken(TokenType::COLON),

            $this->newToken(TokenType::OPEN_SQUARE_BRACKET),
            $this->newToken(TokenType::NUMBER, '1'),
            $this->newToken(TokenType::COMMA),
            $this->newToken(TokenType::NUMBER, '2'),
            $this->newToken(TokenType::COMMA),
            $this->newToken(TokenType::NUMBER, '3'),
            $this->newToken(TokenType::CLOSE_SQUARE_BRACKET),

            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->assertTrue($this->parse($tokens));
    }

    public function testInvalidStartToken(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = [
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::EOF),
        ];

        $this->parse($tokens);
    }

    public function testMissingColon(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->parse($tokens);
    }

    public function testTrailingComma(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::COMMA),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->parse($tokens);
    }

    public function testUnclosedArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tokens = [
            $this->newToken(TokenType::OPEN_CURLY_BRACE),
            $this->newToken(TokenType::STRING),
            $this->newToken(TokenType::COLON),
            $this->newToken(TokenType::OPEN_SQUARE_BRACKET),
            $this->newToken(TokenType::NUMBER),
            $this->newToken(TokenType::CLOSE_CURLY_BRACE),
            $this->newToken(TokenType::EOF),
        ];

        $this->parse($tokens);
    }


}
