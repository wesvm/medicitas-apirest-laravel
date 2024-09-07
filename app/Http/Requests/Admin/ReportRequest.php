<?php

namespace App\Http\Requests\Admin;

use App\Enums\Schools;
use App\Enums\Specialty;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        $schools = implode(',', Schools::values());
        $specialties = implode(',', Specialty::values());
        return [
            'school' => 'required|in:' . $schools . ',All,Other',
            'startDate' => 'required|date|date_format:Y-m-d',
            'endDate' => 'required|date|after_or_equal:startDate|date_format:Y-m-d',
            'specialty' => 'nullable|in:' . $specialties,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
