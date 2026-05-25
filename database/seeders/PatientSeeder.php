<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['first_name' => 'Juan',    'last_name' => 'dela Cruz',  'gender' => 'Male',   'date_of_birth' => '1990-03-15', 'contact_number' => '09171234567'],
            ['first_name' => 'Maria',   'last_name' => 'Garcia',     'gender' => 'Female', 'date_of_birth' => '1985-07-22', 'contact_number' => '09281234567'],
            ['first_name' => 'Roberto', 'last_name' => 'Santos',     'gender' => 'Male',   'date_of_birth' => '1972-11-08', 'contact_number' => '09391234567'],
            ['first_name' => 'Ana',     'last_name' => 'Reyes',      'gender' => 'Female', 'date_of_birth' => '1995-01-30', 'contact_number' => '09171112222'],
            ['first_name' => 'Carlos',  'last_name' => 'Mendoza',    'gender' => 'Male',   'date_of_birth' => '1968-06-14', 'contact_number' => '09283334444'],
            ['first_name' => 'Liza',    'last_name' => 'Villanueva', 'gender' => 'Female', 'date_of_birth' => '2000-09-05', 'contact_number' => '09175556666'],
            ['first_name' => 'Pedro',   'last_name' => 'Bautista',   'gender' => 'Male',   'date_of_birth' => '1955-12-20', 'contact_number' => '09287778888'],
            ['first_name' => 'Nena',    'last_name' => 'Castillo',   'gender' => 'Female', 'date_of_birth' => '1978-04-18', 'contact_number' => '09199990000'],
        ];

        foreach ($patients as $i => $data) {
            Patient::create(array_merge($data, [
                'patient_number' => 'P' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'is_admitted'    => true,
            ]));
        }
    }
}