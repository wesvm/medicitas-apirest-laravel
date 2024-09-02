<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Elon',
            'last_name' => 'Musk',
            'phone' => '987654321',
            'dni' => '76543210',
            'password' => Hash::make('elon'),
            'email' => 'elon@mail.com',
            'role' => 'admin'
        ]);

        Admin::create([
            'user_id' => $admin->id,
        ]);
    }
}
