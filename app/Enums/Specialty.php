<?php

namespace App\Enums;

enum Specialty: string
{
    case PSYCHOLOGY = 'psychology';
    case ODONTOLOGY = 'odontology';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
