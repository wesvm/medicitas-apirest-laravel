<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class PatientResource extends JsonResource
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
            'dateOfBirth' => $this->patient->date_of_birth,
            'placeOfBirth' => $this->patient->place_of_birth,
            'address' => $this->patient->address,
            'professionalSchool' => $this->patient->professional_school,
            'occupation' => $this->patient->occupation,
            'insuranceType' => $this->patient->insurance_type,
            'emergencyContactPhone' => $this->patient->emergency_contact_phone,
        ];
    }
}
