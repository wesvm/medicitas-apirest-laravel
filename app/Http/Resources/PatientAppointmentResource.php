<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class PatientAppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patientId' => $this->patient_id,
            'doctorId' => $this->doctor_id,
            'reason' => $this->reason,
            'appointmentDate' => $this->appointment_date->toIso8601String(),
            'status' => $this->status
        ];
    }
}
