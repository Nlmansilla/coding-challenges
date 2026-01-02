<?php

namespace Nicolas\JsonParser\tests;

use Nicolas\JsonParser\Lexer;
use Nicolas\JsonParser\Token;
use Nicolas\JsonParser\TokenType;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @param array<Token> $tokens
     * @return array<TokenType>
     */
    private function tokenTypes(array $tokens): array
    {
        return array_map(
            fn(Token $token) => $token->getType(),
            $tokens,
        );
    }

    public function testEmptyObject(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('{}');

        $this->assertSame(
            [
                TokenType::OPEN_CURLY_BRACE,
                TokenType::CLOSE_CURLY_BRACE,
                TokenType::EOF,
            ],
            $this->tokenTypes($tokens),
        );
    }

    public function testObjectWithStringAndNumber(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('{"a":123}');

        $this->assertSame(
            [
                TokenType::OPEN_CURLY_BRACE,
                TokenType::STRING,
                TokenType::COLON,
                TokenType::NUMBER,
                TokenType::CLOSE_CURLY_BRACE,
                TokenType::EOF,
            ],
            $this->tokenTypes($tokens),
        );

        $this->assertSame('a', $tokens[1]->getValue());
        $this->assertSame('123', $tokens[3]->getValue());
    }

    public function testBooleanAndNull(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('{"a":true,"b":false,"c":null}');

        $this->assertSame(
            [
                TokenType::OPEN_CURLY_BRACE,
                TokenType::STRING,
                TokenType::COLON,
                TokenType::TRUE,
                TokenType::COMMA,
                TokenType::STRING,
                TokenType::COLON,
                TokenType::FALSE,
                TokenType::COMMA,
                TokenType::STRING,
                TokenType::COLON,
                TokenType::NULL,
                TokenType::CLOSE_CURLY_BRACE,
                TokenType::EOF,
            ],
            $this->tokenTypes($tokens),
        );
    }

    public function testArray(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->tokenize('[1,2,3]');

        $this->assertSame(
            [
                TokenType::OPEN_SQUARE_BRACKET,
                TokenType::NUMBER,
                TokenType::COMMA,
                TokenType::NUMBER,
                TokenType::COMMA,
                TokenType::NUMBER,
                TokenType::CLOSE_SQUARE_BRACKET,
                TokenType::EOF,
            ],
            $this->tokenTypes($tokens),
        );
    }

    public function testInvalidCharacterThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);

        $lexer = new Lexer();
        $lexer->tokenize('{"a": @}');
    }

    public function testUnterminatedStringThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);

        $lexer = new Lexer();
        $lexer->tokenize('{"a":"unterminated}');
    }

}
