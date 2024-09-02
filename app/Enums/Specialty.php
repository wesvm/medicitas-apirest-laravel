<?php

namespace App\Enums;

enum Specialty: string
{
    case PSYCHOLOGY = 'Psychology';
    case ODONTOLOGY = 'Odontology';

    public static function getValues(): array {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
