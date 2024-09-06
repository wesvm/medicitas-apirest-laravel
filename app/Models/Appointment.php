<?php

namespace App\Models;

use App\Traits\HasSort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasSort;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'reason',
        'status',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    protected function casts(): array
    {
        return [
            'appointment_date' => 'datetime',
        ];
    }
}
