<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use App\Rules\DoctorScheduleRule;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'doctorId' => 'required|integer|exists:doctors,user_id',
            'patientId' => 'sometimes|required|integer|exists:patients,user_id',
            'appointmentDate' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now',
                new DoctorScheduleRule
            ],
            'reason' => 'required|string|max:255',
            'status' => 'sometimes|required|in:' . implode(',', AppointmentStatus::values()),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
