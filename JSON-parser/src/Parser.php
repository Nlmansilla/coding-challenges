<?php

namespace Nicolas\JsonParser;

class Parser
{
    private int $currentTokenIndex = 0;

    /**
     * @var array<Token> $tokens
     */
    private array $tokens = [];

    public function __construct(array $tokens){
        $this->tokens = $tokens;
    }

    private function advance(): void
    {
        $this->currentTokenIndex ++;
    }

    private function current(): Token
    {
        return $this->tokens[$this->currentTokenIndex];
    }

    /**
     * @return bool
     */
    public  function parse(): bool
    {
        if (empty($this->tokens)) {
            throw new \InvalidArgumentException('Invalid JSON object.');
        }

        if ($this->tokens[0]->getType() !== TokenType::OPEN_CURLY_BRACE) {
            throw new \InvalidArgumentException('Invalid JSON object. Expected { at position 0.');
        } else {
            $this->parseObject();
            $this->expect(TokenType::EOF);
        }

        return true;
    }


    private function parseObject(): void
    {
        $this->expect(TokenType::OPEN_CURLY_BRACE);

        while (count($this->tokens) > $this->currentTokenIndex) {
            if ($this->current()->getType() === TokenType::CLOSE_CURLY_BRACE) {
                $this->advance();
                return;
            }

//            match ($this->current()->getType()) {
//                TokenType::STRING => $this->parsePair(),
//                default => throw new \InvalidArgumentException('Only string keys supported for now')
//            };
            $this->parsePair();
        }
    }

    private function parsePair(): void
    {
        $this->expect(TokenType::STRING);
        $this->expect(TokenType::COLON);
        $this->parseValue();

        switch ($this->current()->getType() ):
            case TokenType::CLOSE_CURLY_BRACE:
                return;
            case TokenType::COMMA:
                $this->advance();
                $this->parsePair();
                break;
            default:
                throw new \InvalidArgumentException('Invalid JSON object.');
        endswitch;
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
            TokenType::OPEN_SQUARE_BRACKET => throw new \InvalidArgumentException('Arrays are not supported yet'),

            default => throw new \InvalidArgumentException(
                "Unexpected value {$this->current()->getType()->name}"
            )
        };
    }

    private function expect(TokenType $type): void
    {
        if ($this->current()->getType() !== $type) {
            var_dump($this->current());
            throw new \InvalidArgumentException(
                "Expected {$type->name}, got {$this->current()->getType()->name}"
            );
        }
        $this->advance();
    }
}