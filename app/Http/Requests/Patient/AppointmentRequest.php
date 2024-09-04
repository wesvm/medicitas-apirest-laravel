<?php

namespace App\Http\Requests\Patient;

use App\Rules\DoctorScheduleRule;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'doctorId' => 'required',
            'appointmentDate' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:now',
                new DoctorScheduleRule
            ],
            'reason' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
