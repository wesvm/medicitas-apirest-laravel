<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'dni' => '76543210',
            'password' => Hash::make('admin'),
            'email' => 'admin@mail.com',
            'role' => 'admin',
            'email_verified' => true
        ]);

        Admin::create([
            'user_id' => $admin->id,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone' => '987654321'
        ]);
    }
}
