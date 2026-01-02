<?php

namespace Nicolas\JsonParser;

class Parser
{
    private int $currentTokenIndex = 0;

    /**
     * @var array<Token> $tokens
     */
    public array $tokens = [] {
        set {
            $this->tokens = $value;
        }
    }

    private function advance(): void
    {
        $this->currentTokenIndex++;
    }

    private function current(): Token
    {
        return $this->tokens[$this->currentTokenIndex];
    }

    /**
     * @return bool
     */
    public function parse(): bool
    {
        if (empty($this->tokens)) {
            throw new \InvalidArgumentException('Invalid JSON object.');
        }

        $this->parseObject();
        $this->expect(TokenType::EOF);

        return true;
    }


    private function parseObject(): void
    {
        if ($this->current()->getType() === TokenType::OPEN_SQUARE_BRACKET) {
            $this->parseArray();
        }

        if ($this->current()->getType() === TokenType::EOF) {
            return;
        }

        $this->expect(TokenType::OPEN_CURLY_BRACE);

        if ($this->current()->getType() === TokenType::CLOSE_CURLY_BRACE) {
            $this->advance();
            return;
        }

        $this->parsePair();

        while ($this->current()->getType() === TokenType::COMMA) {
            $this->advance();
            $this->parsePair();
        }

        $this->expect(TokenType::CLOSE_CURLY_BRACE);
    }

    private function parsePair(): void
    {
        $this->expect(TokenType::STRING);
        $this->expect(TokenType::COLON);
        $this->parseValue();
    }

    private function parseArray(): void
    {
        $this->expect(TokenType::OPEN_SQUARE_BRACKET);

        if ($this->current()->getType() === TokenType::CLOSE_SQUARE_BRACKET) {
            $this->advance();
            return;
        }

        $this->parseValue();

        while ($this->current()->getType() === TokenType::COMMA) {
            $this->advance();
            $this->parseValue();
        }

        $this->expect(TokenType::CLOSE_SQUARE_BRACKET);
    }

    private function parseValue(): void
    {
        match ($this->current()->getType()) {
            TokenType::STRING,
            TokenType::NUMBER,
            TokenType::TRUE,
            TokenType::FALSE,
            TokenType::NULL => $this->advance(),

            TokenType::OPEN_CURLY_BRACE => $this->parseObject(),
            TokenType::OPEN_SQUARE_BRACKET => $this->parseArray(),

            default => throw new \InvalidArgumentException(
                "Unexpected value {$this->current()->getType()->name}",
            ),
        };
    }

    private function expect(TokenType $type): void
    {
        if ($this->current()->getType() !== $type) {
            throw new \InvalidArgumentException(
                "Expected {$type->name}, got {$this->current()->getType()->name} in token {$this->current()->getValue()} at position {$this->currentTokenIndex}",
            );
        }
        $this->advance();
    }
}
