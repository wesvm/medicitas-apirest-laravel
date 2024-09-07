<?php

namespace App\Http\Resources;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Consultation */
class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointmentId' => $this->appointment_id,
            'consultationDate' => $this->consultation_date->toIso8601String(),
            'consultationReason' => $this->consultation_reason,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'observations' => $this->observations,
            'patientId' => $this->patient_id,
            'doctorId' => $this->doctor_id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'nextAppointmentId' => $this->next_appointment_id
        ];
    }
}
