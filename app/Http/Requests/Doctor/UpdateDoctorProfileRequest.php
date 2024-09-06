<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = auth()->id();
        return [
            'email' => 'required|string|email|max:100|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:255',
            'scheduleId' => 'required|string|exists:doctor_schedules,id'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
