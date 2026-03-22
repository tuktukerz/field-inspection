<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SUPER ADMIN
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // ADMIN 1
        User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ADMIN 2
        User::create([
            'name' => 'Admin 2',
            'email' => 'admin2@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
