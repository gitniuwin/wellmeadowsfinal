<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Maria',
                'last_name'  => 'Santos',
                'email'      => 'director@wellmeadows.com',
                'password'   => Hash::make('password123'),
                'role'       => 'Medical Director',
                'department' => 'Cardiology',
                'status'     => 'Active',
            ],
            [
                'first_name' => 'Clara',
                'last_name'  => 'Reyes',
                'email'      => 'nurse@wellmeadows.com',
                'password'   => Hash::make('password123'),
                'role'       => 'Charge Nurse',
                'department' => 'General Medicine',
                'status'     => 'Active',
            ],
            [
                'first_name' => 'Mark',
                'last_name'  => 'Villanueva',
                'email'      => 'hr@wellmeadows.com',
                'password'   => Hash::make('password123'),
                'role'       => 'Personnel/HR Staff',
                'department' => 'Administration',
                'status'     => 'Active',
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }
    }
}