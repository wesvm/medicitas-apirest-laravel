<?php

namespace App\Http\Resources;

use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DoctorSchedule */
class DoctorScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
        ];
    }
}
