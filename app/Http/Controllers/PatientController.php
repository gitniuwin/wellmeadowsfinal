<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('patient_number', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_admitted', $request->status === 'admitted');
        }

        $patients      = $query->latest()->paginate(10)->withQueryString();
        $totalPatients = Patient::count();
        $admitted      = Patient::where('is_admitted', true)->count();
        $outpatients   = Patient::where('is_admitted', false)->count();

        return view('patients.index', compact(
            'patients', 'totalPatients', 'admitted', 'outpatients'
        ));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:Male,Female,Other',
            'contact_number' => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'is_admitted'    => 'boolean',
        ]);

        $validated['patient_number'] = 'P-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $validated['is_admitted']    = $request->boolean('is_admitted');

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['bed.ward', 'appointments.doctor', 'treatments.doctor', 'bills']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:Male,Female,Other',
            'contact_number' => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'is_admitted'    => 'boolean',
        ]);

        $validated['is_admitted'] = $request->boolean('is_admitted');
        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Patient record updated.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
            ->with('success', 'Patient record deleted.');
    }

    public function discharge(Patient $patient)
    {
        // Check for unpaid bills by checking remaining balance
        $bills = $patient->bills()->get();
        $totalUnpaid = 0;
        $unpaidBills = [];

        foreach ($bills as $bill) {
            if ($bill->remaining_balance > 0) {
                $totalUnpaid += $bill->remaining_balance;
                $unpaidBills[] = $bill;
            }
        }

        // If there are unpaid bills, redirect back with warning
        if ($totalUnpaid > 0) {
            return redirect()->route('patients.show', $patient)
                ->with('warning', "⚠️ Patient {$patient->full_name} has unpaid bills totaling ₱" . number_format($totalUnpaid, 2) . ". Please settle bills before discharge.");
        }

        // Release bed if assigned
        if ($patient->bed) {
            $patient->bed->update([
                'patient_id'  => null,
                'status'      => 'vacant',
                'assigned_at' => null,
                'notes'       => null,
            ]);
        }

        $patient->update(['is_admitted' => false]);

        return redirect()->route('patients.index')
            ->with('success', $patient->full_name . ' has been discharged.');
    }
}