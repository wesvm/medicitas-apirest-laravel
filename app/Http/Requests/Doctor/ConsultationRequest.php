<?php

namespace App\Http\Requests\Doctor;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ConsultationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'appointmentId' => 'sometimes|required',
            'consultationDate' => 'required|date|date_format:Y-m-d H:i:s|after:now',
            'consultationReason' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'observations' => 'nullable|string',
            'patientId' => 'required|exists:patients,user_id',
            'nextAppointment' => 'sometimes|nullable',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
