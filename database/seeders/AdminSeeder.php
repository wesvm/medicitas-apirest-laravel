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
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone' => '987654321',
            'dni' => '76543210',
            'password' => Hash::make('admin'),
            'email' => 'admin@mail.com',
            'role' => 'admin'
        ]);

        Admin::create([
            'user_id' => $admin->id,
        ]);
    }
}
