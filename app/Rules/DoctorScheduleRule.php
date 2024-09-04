<?php

namespace App\Rules;

use App\Models\Doctor;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DoctorScheduleRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doctor = Doctor::findOrFail(request()->doctorId);

        $appointmentTime = Carbon::parse($value)->format('H:i:s');

        $startTime = $doctor->schedule->start_time;
        $endTime = $doctor->schedule->end_time;

        if ($appointmentTime < $startTime || $appointmentTime > $endTime) {
            $fail("The appointment time must be within the doctor's schedule from $startTime to $endTime.");
        }

    }
}
