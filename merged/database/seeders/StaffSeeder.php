<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Schedule;
use App\Models\Responsibility;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffData = [
            [
                'first_name' => 'Maria', 'last_name' => 'Santos',
                'role' => 'Doctor', 'department' => 'Cardiology',
                'ward' => 'Ward A', 'shift' => 'AM',
                'email' => 'maria.santos@wellmeadows.com', 'status' => 'Active',
                'schedule' => ['mon'=>true,'tue'=>true,'wed'=>true,'thu'=>true,'fri'=>true,'sat'=>false,'sun'=>false],
                'responsibilities' => [
                    'Monitor cardiac patients in Ward A daily rounds',
                    'Review and update patient treatment plans',
                    'Coordinate with nurses on medication schedules',
                    'Report critical cases to department head',
                ],
            ],
            [
                'first_name' => 'Clara', 'last_name' => 'Reyes',
                'role' => 'Nurse', 'department' => 'Pediatrics',
                'ward' => 'Ward B', 'shift' => 'PM',
                'email' => 'clara.reyes@wellmeadows.com', 'status' => 'Active',
                'schedule' => ['mon'=>true,'tue'=>false,'wed'=>true,'thu'=>false,'fri'=>true,'sat'=>true,'sun'=>false],
                'responsibilities' => [
                    'Administer prescribed medications to assigned patients',
                    'Record vital signs and patient observations every 4 hours',
                    'Assist doctors during examinations and procedures',
                    'Maintain patient ward hygiene and safety standards',
                ],
            ],
            [
                'first_name' => 'James', 'last_name' => 'Tan',
                'role' => 'Doctor', 'department' => 'Neurology',
                'ward' => 'Ward C', 'shift' => 'AM',
                'email' => 'james.tan@wellmeadows.com', 'status' => 'Active',
                'schedule' => ['mon'=>false,'tue'=>true,'wed'=>true,'thu'=>true,'fri'=>true,'sat'=>true,'sun'=>false],
                'responsibilities' => [
                    'Conduct neurological assessments on admitted patients',
                    'Prescribe and monitor treatment for neurological disorders',
                    'Collaborate with rehabilitation team for patient recovery',
                ],
            ],
            [
                'first_name' => 'Ana', 'last_name' => 'Lim',
                'role' => 'Nurse', 'department' => 'Emergency',
                'ward' => 'ICU', 'shift' => 'Night',
                'email' => 'ana.lim@wellmeadows.com', 'status' => 'Active',
                'schedule' => ['mon'=>true,'tue'=>true,'wed'=>false,'thu'=>true,'fri'=>false,'sat'=>true,'sun'=>true],
                'responsibilities' => [
                    'Monitor ICU patients on night shift',
                    'Respond to emergency calls and code situations',
                    'Administer IV medications and manage drips',
                ],
            ],
            [
                'first_name' => 'Mark', 'last_name' => 'Villanueva',
                'role' => 'Admin', 'department' => 'Administration',
                'ward' => null, 'shift' => 'AM',
                'email' => 'mark.villanueva@wellmeadows.com', 'status' => 'Active',
                'schedule' => ['mon'=>true,'tue'=>true,'wed'=>true,'thu'=>true,'fri'=>true,'sat'=>false,'sun'=>false],
                'responsibilities' => [
                    'Process patient admissions and discharge paperwork',
                    'Schedule appointments and coordinate with departments',
                    'Maintain staff attendance and scheduling records',
                    'Handle hospital correspondence and filing',
                ],
            ],
            [
                'first_name' => 'Rosa', 'last_name' => 'Buena',
                'role' => 'Doctor', 'department' => 'Orthopedics',
                'ward' => 'Ward A', 'shift' => 'PM',
                'email' => 'rosa.buena@wellmeadows.com', 'status' => 'On Leave',
                'schedule' => ['mon'=>false,'tue'=>true,'wed'=>false,'thu'=>true,'fri'=>false,'sat'=>true,'sun'=>false],
                'responsibilities' => [
                    'Assess and treat musculoskeletal injuries',
                    'Perform orthopedic surgical procedures',
                    'Review post-operative patient recovery plans',
                ],
            ],
        ];

        foreach ($staffData as $data) {
            $schedule       = $data['schedule'];
            $responsibilities = $data['responsibilities'];
            unset($data['schedule'], $data['responsibilities']);

            $staff = Staff::create($data);

            Schedule::create(array_merge(['staff_id' => $staff->id], $schedule));

            foreach ($responsibilities as $desc) {
                Responsibility::create(['staff_id' => $staff->id, 'description' => $desc]);
            }
        }
    }
}