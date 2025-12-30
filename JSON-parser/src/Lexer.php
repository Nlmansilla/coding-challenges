<?php

namespace Nicolas\JsonParser;

class Lexer
{
    private int $pos = 0;

    private int $line = 1;

    private string $input;
    private(set) array $tokens = [];

    private function next(): void
    {
        $this->pos++;
    }

    public function tokenize(string $content): array
    {
        $this->input = $content;
        unset($content);

        while($this->pos < strlen($this->input)) {
            if (ctype_space($this->input[$this->pos])) {

                if (preg_match('/\R/', $this->input[$this->pos])) {
                    $this->line++;
                }

                $this->next();
                continue;
            }

            if (ctype_digit($this->input[$this->pos])) {
                $number = $this->readNumber();
                $this->addToken(TokenType::NUMBER, $number);
                continue;
            }

            switch ($this->input[$this->pos]) {
                case "{":
                    $this->addToken(type: TokenType::OPEN_CURLY_BRACE);
                    $this->next();
                    break;

                case "}":
                    $this->addToken(type: TokenType::CLOSE_CURLY_BRACE);
                    $this->next();
                    break;

                case ':':
                    $this->addToken(TokenType::COLON);
                    $this->next();
                    break;

                case ',':
                    $this->addToken(TokenType::COMMA);
                    $this->next();
                    break;

                case '"':
                    $this->addToken(
                        type:TokenType::STRING,
                        value: $this->readString()
                    );
                    break;

                case 't':
                    $this->readKeyword('true');
                    $this->addToken(TokenType::TRUE);
                    break;

                case 'f':
                    $this->readKeyword('false');
                    $this->addToken(TokenType::FALSE);
                    break;

                case 'n':
                    $this->readKeyword('null');
                    $this->addToken(TokenType::NULL);
                    break;

                case '[':
                    $this->addToken(TokenType::OPEN_SQUARE_BRACKET);
                    $this->next();
                    break;

                case ']':
                    $this->addToken(TokenType::CLOSE_SQUARE_BRACKET);
                    $this->next();
                    break;

                default:
                    $this->raiseError();
            }
        }

        $this->addToken(TokenType::EOF);
        return $this->tokens;
    }

    private function raiseError(): never
    {
        throw new \RuntimeException(
            "Unexpected character '{$this->input[$this->pos]}' at line {$this->line} position {$this->pos}"
        );
    }

    private function addToken(TokenType $type, mixed $value = null): void
    {
        $this->tokens[] = new Token(
            type: $type,
            value: $value
        );
    }

    private function readString(): string
    {
        $this->next();
        $start = $this->pos;
        $length = strlen($this->input);
        while ($this->pos < $length) {
            if ($this->input[$this->pos] === '"') {
                $value = substr($this->input, $start, $this->pos - $start);
                $this->next();
                return $value;
            }

            $this->next();
        }

        throw new \RuntimeException("Unterminated string literal at line {$this->line} position {$this->pos}");
    }

    private function readKeyword(string $expected): void
    {
        $length = strlen($expected);
        $value = substr($this->input, $this->pos, $length);

        if ($value !== $expected) {
            throw new \RuntimeException("Unexpected token {$value}, expected {$expected} at line {$this->line} position {$this->pos}");
        }

        $this->pos += $length;
    }

    private function readNumber(): string
    {
        $start = $this->pos;
        $length = strlen($this->input);
        while ($this->pos < $length) {
            if ( !ctype_digit($this->input[$this->pos]) ) {
                return substr($this->input, $start, $this->pos - $start);
            }

            $this->next();
        }

        throw new \RuntimeException("TODO: improve error message");
    }
}