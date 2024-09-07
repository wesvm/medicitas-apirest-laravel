<?php

namespace App\Enums;

enum Schools: string
{
    case MINING_ENGINEERING = 'Mining Engineering';
    case SYSTEMS_AND_INFORMATICS_ENGINEERING = 'Systems and Information Engineering';
    case ENVIRONMENTAL_ENGINEERING = 'Environmental Engineering';
    case AGROINDUSTRIAL_ENGINEERING = 'Agroindustrial Engineering';
    case FISHERY_ENGINEERING = 'Fisher Engineering';
    case CIVIL_ENGINEERING = 'Civil Engineering';
    case LAW = 'Law';
    case MEDICINE = 'Medicine';
    case BUSINESS_ADMINISTRATION = 'Business Administration';
    case ACCOUNTING = 'Accounting';
    case PUBLIC_MANAGEMENT_AND_SOCIAL_DEVELOPMENT = 'Public Management and Social Development';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
