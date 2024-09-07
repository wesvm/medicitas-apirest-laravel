<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable();
            $table->dateTime('consultation_date');
            $table->string('consultation_reason');
            $table->text('diagnosis');
            $table->text('treatment');
            $table->text('observations')->nullable();
            $table->foreignId('patient_id');
            $table->foreignId('doctor_id');
            $table->foreignId('next_appointment_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
