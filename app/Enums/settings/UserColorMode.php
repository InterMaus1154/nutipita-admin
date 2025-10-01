<?php

namespace App\Enums\settings;

enum UserColorMode: int
{
    case ORANGE = 0;
    case PINK = 1;
    case LIME = 2;
    case INDIGO = 3;
    case YELLOW = 4;

    case WHITE = 5;
    case RED = 6;
    case AMBER = 7;
    CASE GREEN = 8;
    CASE EMERALD = 9;
    CASE TEAL = 10;
    CASE CYAN = 11;
    CASE SKY = 12;
    CASE BLUE = 13;
    CASE VIOLET = 14;
    CASE PURPLE = 15;
    CASE FUCHSIA = 16;
    CASE ROSE = 17;

    public static function colorFromValue(int $value): string
    {
        return match ($value) {
            0 => strtolower(self::ORANGE->name),
            1 => strtolower(self::PINK->name),
            2 => strtolower(self::LIME->name),
            3 => strtolower(self::INDIGO->name),
            4 => strtolower(self::YELLOW->name),
            5 => strtolower(self::WHITE->name),
            6 => strtolower(self::RED->name),
            7 => strtolower(self::AMBER->name),
            8 => strtolower(self::GREEN->name),
            9 => strtolower(self::EMERALD->name),
            10 => strtolower(self::TEAL->name),
            11 => strtolower(self::CYAN->name),
            12 => strtolower(self::SKY->name),
            13 => strtolower(self::BLUE->name),
            14 => strtolower(self::VIOLET->name),
            15 => strtolower(self::PURPLE->name),
            16 => strtolower(self::FUCHSIA->name),
            17 => strtolower(self::ROSE->name)
        };
    }

}
