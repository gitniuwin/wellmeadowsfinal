<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Appointment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Treatment::with(['patient', 'doctor'])
            ->latest('treatment_date');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $totalTreatments = (clone $query)->count();
        $activeDiagnoses = (clone $query)->whereNotNull('diagnosis')->count();
        $treatments = $query->paginate(10)->withQueryString();
        $selectedPatient = $request->filled('patient_id')
            ? Patient::find($request->patient_id)
            : null;

        return view('treatments.index', [
            'treatments'      => $treatments,
            'totalTreatments' => $totalTreatments,
            'activeDiagnoses' => $activeDiagnoses,
            'proceduresToday' => Treatment::whereDate('treatment_date', today())
                ->when($request->filled('patient_id'), fn ($q) => $q->where('patient_id', $request->patient_id))
                ->count(),
            'patients'        => Patient::orderBy('first_name')->get(),
            'doctors'         => Staff::where('role', 'Doctor')->orderBy('first_name')->get(),
            'selectedPatient' => $selectedPatient,
        ]);
    }

    public function create()
    {
        return view('treatments.create', [
            'patients'     => Patient::orderBy('first_name')->get(),
            'doctors'      => Staff::where('role', 'Doctor')->orderBy('first_name')->get(),
            'appointments' => Appointment::with('patient')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'      => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:staff,id',
            'appointment_id'  => 'nullable|exists:appointments,id',
            'diagnosis'       => 'required|string|max:255',
            'procedure'       => 'required|string|max:255',
            'treatment_date'  => 'required|date',
            'notes'           => 'nullable|string|max:1000',
        ]);

        Treatment::create($validated);

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment recorded successfully.');
    }

    public function show(Treatment $treatment)
    {
        $treatment->load(['patient', 'doctor', 'appointment']);
        return view('treatments.show', compact('treatment'));
    }

    public function edit(Treatment $treatment)
    {
        return view('treatments.edit', [
            'treatment'    => $treatment,
            'patients'     => Patient::orderBy('first_name')->get(),
            'doctors'      => Staff::where('role', 'Doctor')->orderBy('first_name')->get(),
            'appointments' => Appointment::with('patient')->latest()->get(),
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'patient_id'      => 'required|exists:patients,id',
            'doctor_id'       => 'required|exists:staff,id',
            'appointment_id'  => 'nullable|exists:appointments,id',
            'diagnosis'       => 'required|string|max:255',
            'procedure'       => 'required|string|max:255',
            'treatment_date'  => 'required|date',
            'notes'           => 'nullable|string|max:1000',
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
