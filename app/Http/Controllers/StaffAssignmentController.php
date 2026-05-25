<?php

namespace App\Http\Controllers;

use App\Models\StaffAssignment;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Treatment;
use Illuminate\Http\Request;

class StaffAssignmentController extends Controller
{
    public function index()
    {
        return redirect()->route('treatments.index')
            ->with('success', 'Staff assignments are managed when recording a treatment.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id'      => 'required|exists:staff,id',
            'patient_id'    => 'required|exists:patients,id',
            'treatment_id'  => 'nullable|exists:treatments,id',
            'assigned_date' => 'required|date',
        ]);

        StaffAssignment::create($validated);

        return redirect()->route('treatments.index')
            ->with('success', 'Staff assigned successfully.');
    }

    public function destroy(StaffAssignment $staffAssignment)
    {
        $staffAssignment->delete();
        return redirect()->route('staff-assignment.index')
            ->with('success', 'Assignment removed.');
    }
}
