<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DoctorSchedule::create([
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        DoctorSchedule::create([
            'start_time' => '14:00:00',
            'end_time' => '18:00:00',
        ]);
    }
}
