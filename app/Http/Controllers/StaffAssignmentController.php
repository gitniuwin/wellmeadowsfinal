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
        $assignments = StaffAssignment::with(['staff', 'patient', 'treatment'])
            ->latest('assigned_date')
            ->paginate(10);

        return view('staff.index', [
            'assignments'        => $assignments,
            'totalDoctors'       => Staff::where('role', 'Doctor')->count(),
            'totalNurses'        => Staff::where('role', 'Nurse')->count(),
            'assignedToday'      => StaffAssignment::whereDate('assigned_date', today())->count(),
            'pendingAssignments' => StaffAssignment::whereNull('treatment_id')->count(),
            'staff'              => Staff::orderBy('name')->get(),
            'patients'           => Patient::orderBy('name')->get(),
            'treatments'         => Treatment::with('patient')->latest()->get(),
        ]);
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

        return redirect()->route('staff-assignment.index')
            ->with('success', 'Staff assigned successfully.');
    }

    public function destroy(StaffAssignment $staffAssignment)
    {
        $staffAssignment->delete();
        return redirect()->route('staff-assignment.index')
            ->with('success', 'Assignment removed.');
    }
}