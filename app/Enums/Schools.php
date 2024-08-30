<?php

namespace App\Enums;

enum Schools: string
{
    case MINING_ENGINEERING = 'Ingeniería de Minas';
    case SYSTEMS_AND_INFORMATICS_ENGINEERING = 'Ingeniería de Sistemas e Informatica';
    case ENVIRONMENTAL_ENGINEERING = 'Ingeniería Ambiental';
    case AGROINDUSTRIAL_ENGINEERING = 'Ingeniería Agroindustrial';
    case FISHERY_ENGINEERING = 'Ingeniería Pesquera';
    case CIVIL_ENGINEERING = 'Ingeniería Civil';
    case LAW = 'Derecho';
    case MEDICINE = 'Medicina';
    case BUSINESS_ADMINISTRATION = 'Administración';
    case ACCOUNTING = 'Contabilidad';
    case PUBLIC_MANAGEMENT_AND_SOCIAL_DEVELOPMENT = 'Gestión Publica y Desarrollo Social';

    public static function getValues(): array {
        return array_map(fn($enum) => $enum->value, self::cases());
    }
}
