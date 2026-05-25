<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->latest('appointment_date')
            ->paginate(10);

        return view('appointments.index', [
            'appointments'      => $appointments,
            'totalAppointments' => Appointment::count(),
            'scheduled'         => Appointment::where('status', 'scheduled')->count(),
            'completed'         => Appointment::where('status', 'completed')->count(),
            'cancelled'         => Appointment::where('status', 'cancelled')->count(),
            'patients' => Patient::orderBy('first_name')->get(),
            'doctors'  => Staff::where('role', 'Doctor')->orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:staff,id',
            'appointment_date' => 'required|date',
            'type'             => 'required|in:Consultation,Follow-up,Emergency,Routine Check',
            'notes'            => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'scheduled';
        Appointment::create($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment scheduled successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        return view('appointments.edit', [
            'appointment' => $appointment,
            'patients'    => Patient::orderBy('name')->get(),
            'doctors'     => Staff::where('role', 'Doctor')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:staff,id',
            'appointment_date' => 'required|date',
            'type'             => 'required|in:Consultation,Follow-up,Emergency,Routine Check',
            'status'           => 'required|in:scheduled,completed,cancelled',
            'notes'            => 'nullable|string|max:500',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment status updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted.');
    }
}
