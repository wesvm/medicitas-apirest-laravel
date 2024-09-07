<?php

namespace App\Models;

use App\Traits\HasSort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasSort;

    protected $fillable = [
        'appointment_id',
        'consultation_reason',
        'consultation_date',
        'diagnosis',
        'treatment',
        'observations',
        'patient_id',
        'doctor_id',
        'next_appointment_id',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function nextAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'next_appointment_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    protected function casts(): array
    {
        return [
            'consultation_date' => 'datetime',
        ];
    }
}
