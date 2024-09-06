<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class DoctorAppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient' => [
                'id' => $this->patient->user->id,
                'firstName' => $this->patient->user->first_name,
                'lastName' => $this->patient->user->last_name,
                'email' => $this->patient->user->email,
                'phone' => $this->patient->user->phone,
                'dni' => $this->patient->user->dni,
                'dateOfBirth' => $this->patient->date_of_birth,
                'placeOfBirth' => $this->patient->place_of_birth,
                'address' => $this->patient->address,
                'professionalSchool' => $this->patient->professional_school,
                'occupation' => $this->patient->occupation,
                'insuranceType' => $this->patient->insurance_type,
                'emergencyContactPhone' => $this->patient->emergency_contact_phone,
            ],
            'reason' => $this->reason,
            'appointmentDate' => $this->appointment_date->toIso8601String(),
            'status' => $this->status
        ];
    }
}
