<?php

namespace App\Enums\settings;

enum UserThemeMode: int
{
    case DARK = 0;
    case WHITE = 1;

    public static function themeFromValue(int $value): string
    {
        return match ($value) {
            0 => strtolower(self::DARK->name),
            1 => strtolower(self::WHITE->name)
        };
    }

}
