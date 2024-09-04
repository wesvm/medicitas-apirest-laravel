<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $userId = $this->route('user');
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'dni' => [
                'required',
                'string',
                'min:8',
                'max:8',
                Rule::unique('users', 'dni')->ignore($userId)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'active' => 'sometimes|boolean',
        ];
    }
}
