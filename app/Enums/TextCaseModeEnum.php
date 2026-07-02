<?php

namespace App\Enums;

enum TextCaseModeEnum: string
{
    case NONE = 'none';
    case UPPER = 'upper';
    case LOWER = 'lower';
    case CAPITALIZE = 'capitalize';
    case SENTENCE = 'sentence';
}
