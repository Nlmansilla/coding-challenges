<?php

namespace Nicolas\JsonParser;

enum TokenType
{
    case OPEN_CURLY_BRACE;
    case CLOSE_CURLY_BRACE;

    case COLON;
    case COMMA;
    case STRING;
    case NUMBER;
    case TRUE;
    case FALSE;
    case NULL;
    case OPEN_SQUARE_BRACKET;
    case CLOSE_SQUARE_BRACKET;
    case EOF;
}
