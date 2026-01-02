<?php

namespace Nicolas\JsonParser;

class Lexer
{
    private int $pos = 0;

    private int $line = 1;

    private string $input;

    /**
     * @var array<Token> $tokens
     */
    private(set) array $tokens = [];

    private function next(): void
    {
        $this->pos++;
    }

    /**
     * @param string $content
     * @return Token[]
     */
    public function tokenize(string $content): array
    {
        $this->input = $content;
        unset($content);

        while ($this->pos < strlen($this->input)) {
            if (ctype_space($this->input[$this->pos])) {

                if (preg_match('/\R/', $this->input[$this->pos])) {
                    $this->line++;
                }

                $this->next();
                continue;
            }

            if (ctype_digit($this->input[$this->pos]) || $this->input[$this->pos] === '-') {
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
                        type: TokenType::STRING,
                        value: $this->readString(),
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
            "Unexpected character '{$this->input[$this->pos]}' at line {$this->line} position {$this->pos}",
        );
    }

    private function addToken(TokenType $type, mixed $value = null): void
    {
        $this->tokens[] = new Token(
            type: $type,
            value: $value,
        );
    }

    private function readString(): string
    {
        $this->next();

        $result = '';
        $length = strlen($this->input);

        while ($this->pos < $length) {
            $char = $this->input[$this->pos];

            if ($char === '"') {
                $this->next();
                return $result;
            }

            if (ord($char) < 0x20) {
                throw new \RuntimeException(
                    "Invalid control character in string at line {$this->line} position {$this->pos}",
                );
            }

            if ($char === '\\') {
                $this->next();

                if ($this->pos >= $length) {
                    throw new \RuntimeException("Unterminated escape sequence");
                }

                $escape = $this->input[$this->pos];

                $result .= match ($escape) {
                    '"', '\\', '/' => $escape,
                    'b' => "\b",
                    'f' => "\f",
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'u' => $this->readUnicodeEscape(),
                    default => throw new \RuntimeException(
                        "Invalid escape sequence \\{$escape} at line {$this->line} position {$this->pos}",
                    ),
                };

                $this->next();
                continue;
            }

            $result .= $char;
            $this->next();
        }

        throw new \RuntimeException("Unterminated string literal at line {$this->line}");
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

        if ($this->input[$this->pos] === '-') {
            $this->next();
        }

        if ($this->input[$this->pos] === '0') {
            $this->next();
        } elseif (ctype_digit($this->input[$this->pos])) {
            while ($this->pos < $length && ctype_digit($this->input[$this->pos])) {
                $this->next();
            }
        } else {
            throw new \RuntimeException("Invalid number at position {$this->pos}");
        }

        if ($this->pos < $length && $this->input[$this->pos] === '.') {
            $this->next();

            if ($this->pos >= $length || !ctype_digit($this->input[$this->pos])) {
                throw new \RuntimeException("Invalid fractional part at position {$this->pos}");
            }

            while ($this->pos < $length && ctype_digit($this->input[$this->pos])) {
                $this->next();
            }
        }

        if ($this->pos < $length && ($this->input[$this->pos] === 'e' || $this->input[$this->pos] === 'E')) {
            $this->next();

            if ($this->pos < $length && ($this->input[$this->pos] === '+' || $this->input[$this->pos] === '-')) {
                $this->next();
            }

            if ($this->pos >= $length || !ctype_digit($this->input[$this->pos])) {
                throw new \RuntimeException("Invalid exponent at position {$this->pos}");
            }

            while ($this->pos < $length && ctype_digit($this->input[$this->pos])) {
                $this->next();
            }
        }

        return substr($this->input, $start, $this->pos - $start);
    }


    private function readUnicodeEscape(): string
    {
        $hex = substr($this->input, $this->pos + 1, 4);

        if (!preg_match('/^[0-9a-fA-F]{4}$/', $hex)) {
            throw new \RuntimeException(
                "Invalid unicode escape \\u{$hex} at line {$this->line} position {$this->pos}",
            );
        }

        $this->pos += 4;

        $codepoint = hexdec($hex);

        return mb_convert_encoding(pack('n', $codepoint), 'UTF-8', 'UTF-16BE');
    }

}
