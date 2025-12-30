<?php

namespace Nicolas\JsonParser;

readonly class Token
{
    public function __construct(
        private TokenType $type,
        private mixed     $value
    ){
    }

    public function getType(): TokenType {
        return $this->type;
    }
    public function getValue(): mixed {
        return $this->value;
    }
}