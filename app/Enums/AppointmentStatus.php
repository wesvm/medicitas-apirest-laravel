<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case SCHEDULED = 'Scheduled';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';
}
