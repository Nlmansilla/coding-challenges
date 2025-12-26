<?php

namespace Nicolas\JsonParser;

enum EscapedValues: string
{
    case SPACE = ' ';
    case TAB = '\t';
    case NEWLINE = '\n';
    case CARRIAGE_RETURN = '\r';
}
