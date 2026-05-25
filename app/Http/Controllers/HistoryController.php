<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::orderBy('first_name')->orderBy('last_name')->get();
        $selectedPatient = null;
        $history = collect();

        if ($request->has('patient_id')) {
            $selectedPatient = Patient::findOrFail($request->patient_id);

            $appointments = $selectedPatient->appointments()
                ->with('doctor')
                ->get()
                ->map(fn($a) => (object)[
                    'type'      => 'Appointment — ' . $a->type,
                    'diagnosis' => null,
                    'procedure' => null,
                    'notes'     => $a->notes,
                    'date'      => $a->appointment_date,
                    'doctor'    => $a->doctor,
                ]);

            $treatments = $selectedPatient->treatments()
                ->with('doctor')
                ->get()
                ->map(fn($t) => (object)[
                    'type'      => 'Treatment',
                    'diagnosis' => $t->diagnosis,
                    'procedure' => $t->procedure,
                    'notes'     => $t->notes,
                    'date'      => $t->treatment_date,
                    'doctor'    => $t->doctor,
                ]);

            $history = $appointments->merge($treatments)
                ->sortByDesc('date')
                ->values();
        }

        return view('history.index', compact('patients', 'selectedPatient', 'history'));
    }

    public function show(Patient $patient)
    {
        return redirect()->route('history.index', ['patient_id' => $patient->id]);
    }
}