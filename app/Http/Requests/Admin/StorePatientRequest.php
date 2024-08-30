<?php

namespace App\Http\Requests\Admin;

use App\Enums\Insurance;
use App\Enums\Schools;
use App\Http\Requests\UserRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userRequestRules = (new UserRequest())->rules();
        $insuranceTypes = implode(',', Insurance::getValues());
        $schools = implode(',', Schools::getValues());
        return array_merge($userRequestRules, [
            'dateOfBirth' => 'required|date|before_or_equal:' . now()->format('Y-m-d') . '|after_or_equal:1900-01-01',
            'placeOfBirth' => 'nullable|string',
            'address' => 'required|string',
            'professionalSchool' => 'nullable|string|in:' . $schools,
            'occupation' => 'nullable|string',
            'insuranceType' => 'required|string|in:'.$insuranceTypes,
            'emergencyContactPhone' => 'nullable|string',
        ]);
    }
}
