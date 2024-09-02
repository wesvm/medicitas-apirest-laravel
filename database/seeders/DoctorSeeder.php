<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Enums\Specialty;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $dni = $faker->unique()->numerify('########');
        $doctor = User::create([
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'phone' => $faker->phoneNumber,
            'dni' => $dni,
            'password' => bcrypt($dni),
            'email' => $faker->unique()->safeEmail,
            'role' => Roles::DOCTOR->value,
        ]);

        Doctor::create([
            'user_id' => $doctor->id,
            'schedule_id' => 1,
            'specialty' =>  Specialty::ODONTOLOGY->value,
        ]);

        //--------------

        $dni = $faker->unique()->numerify('########');
        $doctor = User::create([
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'phone' => $faker->phoneNumber,
            'dni' => $dni,
            'password' => bcrypt($dni),
            'email' => $faker->unique()->safeEmail,
            'role' => Roles::DOCTOR->value,
        ]);

        Doctor::create([
            'user_id' => $doctor->id,
            'schedule_id' => 2,
            'specialty' =>  Specialty::PSYCHOLOGY->value,
        ]);

    }
}
