<?php

namespace App\Enums\settings;

enum UserColorMode: int
{
    CASE ORANGE = 0;
    CASE PINK = 1;
    CASE LIME = 2;
    CASE INDIGO = 3;

    public static function colorFromValue(int $value): string
    {
        return match($value){
            0 => strtolower(self::ORANGE->name),
            1 => strtolower(self::PINK->name),
            2 => strtolower(self::LIME->name),
            3 => strtolower(self::INDIGO->name)
        };
    }

}
