<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'email' => $this->email,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phone' => $this->phone,
            'specialty' => $this->doctor->specialty,
            'schedule' => [
                'id' => $this->doctor->schedule->id,
                'startTime' => $this->doctor->schedule->start_time,
                'endTime' => $this->doctor->schedule->end_time,
            ],
        ];
    }
}
