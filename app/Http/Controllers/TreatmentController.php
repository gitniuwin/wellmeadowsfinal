<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::with(['patient', 'doctor'])
            ->latest('treatment_date')
            ->paginate(10);

        return view('treatments.index', [
            'treatments'      => $treatments,
            'totalTreatments' => Treatment::count(),
            'activeDiagnoses' => Treatment::whereDate('treatment_date', today())->count(),
            'proceduresToday' => Treatment::whereDate('treatment_date', today())->count(),
            'patients'        => Patient::orderBy('first_name')->orderBy('last_name')->get(),
            'doctors'         => Staff::where('role', 'Doctor')->orderBy('first_name')->orderBy('last_name')->get(),
            'nurses'          => Staff::where('role', 'Nurse')->orderBy('first_name')->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'doctor_id'      => 'required|exists:staff,id',
            'diagnosis'      => 'required|string|max:255',
            'procedure'      => 'required|string|max:255',
            'treatment_date' => 'required|date',
            'notes'          => 'nullable|string|max:1000',
            'nurse_ids'      => 'array',
            'nurse_ids.*'    => 'exists:staff,id',
        ]);

        $nurseIds = $validated['nurse_ids'] ?? [];
        unset($validated['nurse_ids']);

        $treatment = Treatment::create($validated);

        $assignedStaff = array_unique(array_merge([$validated['doctor_id']], $nurseIds));
        foreach ($assignedStaff as $staffId) {
            StaffAssignment::firstOrCreate([
                'staff_id' => $staffId,
                'patient_id' => $validated['patient_id'],
                'treatment_id' => $treatment->id,
            ], [
                'assigned_date' => $validated['treatment_date'],
            ]);
        }

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment recorded successfully.');
    }

    public function show(Treatment $treatment)
    {
        $treatment->load(['patient', 'doctor']);
        return view('treatments.show', compact('treatment'));
    }

    public function edit(Treatment $treatment)
    {
        return view('treatments.edit', [
            'treatment' => $treatment,
            'patients'  => Patient::orderBy('name')->get(),
            'doctors'   => Staff::where('role', 'Doctor')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'doctor_id'      => 'required|exists:staff,id',
            'diagnosis'      => 'required|string|max:255',
            'procedure'      => 'required|string|max:255',
            'treatment_date' => 'required|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $treatment->update($validated);

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment updated successfully.');
    }

    public function destroy(Treatment $treatment)
    {
        $treatment->delete();
        return redirect()->route('treatments.index')
            ->with('success', 'Treatment record deleted.');
    }
}
