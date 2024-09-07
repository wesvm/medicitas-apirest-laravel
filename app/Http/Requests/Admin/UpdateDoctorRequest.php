<?php

namespace App\Http\Requests\Admin;

use App\Enums\Specialty;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function rules(): array
    {
        $specialties = implode(',', Specialty::values());
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'dni' => 'required|string|min:8|max:8|unique:users,dni',
            'email' => 'required|string|email|max:100|unique:users,email',
            'scheduleId' => 'required|string|exists:doctor_schedules,id',
            'specialty' => 'required|string|in:' . $specialties,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
