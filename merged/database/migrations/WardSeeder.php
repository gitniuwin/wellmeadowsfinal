<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;

class WardSeeder extends Seeder
{
    public function run(): void
    {
        // Sample patients
        $patients = [];
        $names = [
            ['Juan', 'dela Cruz'], ['Maria', 'Santos'], ['Pedro', 'Reyes'],
            ['Ana', 'Gonzales'], ['Jose', 'Bautista'], ['Luisa', 'Ramos'],
            ['Carlos', 'Torres'], ['Rosa', 'Villanueva'],
        ];
        foreach ($names as $i => [$first, $last]) {
            $patients[] = Patient::create([
                'patient_number' => 'P-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'first_name'     => $first,
                'last_name'      => $last,
                'date_of_birth'  => now()->subYears(rand(20, 70)),
                'gender'         => ['Male', 'Female'][rand(0, 1)],
                'is_admitted'    => true,
            ]);
        }

        // Ward data
        $wardsData = [
            ['name' => 'General Ward A', 'type' => 'General',   'capacity' => 20, 'floor' => '1st'],
            ['name' => 'ICU',            'type' => 'ICU',        'capacity' => 10, 'floor' => '2nd'],
            ['name' => 'Pediatric Ward', 'type' => 'Pediatric',  'capacity' => 15, 'floor' => '2nd'],
            ['name' => 'Maternity Ward', 'type' => 'Maternity',  'capacity' => 20, 'floor' => '3rd'],
            ['name' => 'Surgical Ward',  'type' => 'Surgical',   'capacity' => 12, 'floor' => '3rd'],
            ['name' => 'Cardiac Ward',   'type' => 'Cardiac',    'capacity' => 8,  'floor' => '4th'],
        ];

        $patientIdx = 0;

        foreach ($wardsData as $data) {
            $ward = Ward::create(array_merge($data, ['is_active' => true]));

            for ($i = 1; $i <= $ward->capacity; $i++) {
                $bedNumber = strtoupper($ward->name[0]) . str_pad($i, 2, '0', STR_PAD_LEFT);

                // Assign some beds randomly
                $status = 'vacant';
                $patientId = null;
                $assignedAt = null;

                $roll = rand(1, 10);
                if ($roll <= 6 && $patientIdx < count($patients)) {
                    $status = 'occupied';
                    $patientId = $patients[$patientIdx]->id;
                    $assignedAt = now()->subHours(rand(1, 72));
                    $patientIdx++;
                } elseif ($roll === 7) {
                    $status = 'reserved';
                } elseif ($roll === 8) {
                    $status = 'maintenance';
                }

                Bed::create([
                    'ward_id'    => $ward->id,
                    'bed_number' => $bedNumber,
                    'status'     => $status,
                    'patient_id' => $patientId,
                    'assigned_at'=> $assignedAt,
                ]);
            }
        }
    }
}
