<?php

namespace App\Enums;

enum Insurance: string
{
    case SIS = 'SIS';
    case ESSALUD = 'ESSALUD';

    public static function getValues(): array {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
