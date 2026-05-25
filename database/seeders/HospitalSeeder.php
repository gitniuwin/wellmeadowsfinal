<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\Bill;
use App\Models\Staff;
use Carbon\Carbon;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors  = Staff::where('role', 'Doctor')->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->warn('⚠ No patients or doctors found. Run PatientSeeder and StaffSeeder first.');
            return;
        }

        $apptTypes    = ['Consultation', 'Follow-up', 'Emergency', 'Routine Check'];
        $apptStatuses = ['scheduled', 'completed', 'cancelled'];
        $diagnoses    = ['Hypertension', 'Diabetes Type 2', 'Pneumonia', 'Bone Fracture', 'Anemia'];
        $procedures   = ['Blood Test', 'X-Ray', 'IV Therapy', 'Surgery Prep', 'ECG Monitoring'];
        $services     = ['room', 'treatment', 'services'];
        $billStatuses = ['paid', 'pending', 'overdue'];

        foreach ($patients as $i => $patient) {
            $doctor = $doctors[$i % $doctors->count()];

            // Appointment
            $appointment = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'appointment_date' => Carbon::now()->subDays(rand(1, 30)),
                'type'             => $apptTypes[array_rand($apptTypes)],
                'status'           => $apptStatuses[array_rand($apptStatuses)],
                'notes'            => 'Scheduled during routine hospital admission.',
            ]);

            // Treatment linked to appointment
            Treatment::create([
                'patient_id'     => $patient->id,
                'doctor_id'      => $doctor->id,
                'appointment_id' => $appointment->id,
                'diagnosis'      => $diagnoses[array_rand($diagnoses)],
                'procedure'      => $procedures[array_rand($procedures)],
                'treatment_date' => Carbon::now()->subDays(rand(1, 20)),
                'notes'          => 'Patient responded well to treatment.',
            ]);

            // Bill linked to patient
            Bill::create([
                'patient_id'   => $patient->id,
                'patient_name' => $patient->first_name . ' ' . $patient->last_name,
                'service_type' => $services[array_rand($services)],
                'total_amount' => rand(2500, 35000),
                'due_date'     => Carbon::now()->addDays(rand(-10, 30))->toDateString(),
                'status'       => $billStatuses[array_rand($billStatuses)],
            ]);
        }

        $this->command->info('✅ Appointments, Treatments, and Bills seeded successfully!');
    }
}