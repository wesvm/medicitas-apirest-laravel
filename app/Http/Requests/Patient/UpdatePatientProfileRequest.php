<?php

namespace App\Http\Requests\Patient;

use App\Enums\Insurance;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientProfileRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = auth()->id();
        return [
            'email' => 'required|string|email|max:100|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'insuranceType' => 'required|string|in:' .  implode(',', Insurance::getValues()),
            'emergencyContactPhone' => 'nullable|string|max:255',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
