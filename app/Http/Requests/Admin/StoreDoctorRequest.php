<?php

namespace App\Http\Requests\Admin;

use App\Enums\Specialty;
use App\Http\Requests\UserRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function rules(): array
    {
        $userRequestRules = (new UserRequest())->rules();
        $specialties = implode(',', Specialty::getValues());
        return array_merge($userRequestRules, [
            'scheduleId' => 'required|string|exists:doctor_schedules,id',
            'specialty' => 'required|in:' . $specialties,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }
}
