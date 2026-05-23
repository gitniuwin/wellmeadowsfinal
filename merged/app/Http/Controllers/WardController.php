<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use Illuminate\Http\Request;

class WardController extends Controller
{
    // ───────────────────────────────────────────
    // Module 3 Main Page — Ward Overview
    // ───────────────────────────────────────────
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');

        $query = Ward::with(['beds', 'beds.patient'])->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
        }

        $wards = $query->get();

        // Apply availability filter after loading (uses computed attribute)
        if ($filter !== 'all') {
            $wards = $wards->filter(fn($w) => $w->availability_status === $filter)->values();
        }

        // Summary stats
        $totalBeds     = Bed::count();
        $occupiedBeds  = Bed::where('status', 'occupied')->count();
        $vacantBeds    = Bed::where('status', 'vacant')->count();
        $otherBeds     = Bed::whereIn('status', ['reserved', 'maintenance'])->count();

        return view('wards.index', compact(
            'wards', 'totalBeds', 'occupiedBeds', 'vacantBeds', 'otherBeds', 'filter', 'search'
        ));
    }

    // ───────────────────────────────────────────
    // Ward Detail — all beds for one ward
    // ───────────────────────────────────────────
    public function show(Ward $ward)
    {
        $ward->load(['beds.patient']);
        $admittedPatients = Patient::where('is_admitted', true)
            ->whereDoesntHave('bed')
            ->get();

        return view('wards.show', compact('ward', 'admittedPatients'));
    }

    // ───────────────────────────────────────────
    // Create Ward Form
    // ───────────────────────────────────────────
    public function create()
    {
        $types = ['General', 'ICU', 'Pediatric', 'Maternity', 'Surgical', 'Orthopedic', 'Cardiac', 'Oncology', 'Emergency'];
        return view('wards.create', compact('types'));
    }

    // ───────────────────────────────────────────
    // Store New Ward + auto-generate beds
    // ───────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:General,ICU,Pediatric,Maternity,Surgical,Orthopedic,Cardiac,Oncology,Emergency',
            'capacity'    => 'required|integer|min:1|max:200',
            'floor'       => 'nullable|string|max:20',
            'building'    => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $ward = Ward::create($validated);

        // Auto-generate bed records based on capacity
        for ($i = 1; $i <= $ward->capacity; $i++) {
            Bed::create([
                'ward_id'    => $ward->id,
                'bed_number' => $ward->name[0] . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status'     => 'vacant',
            ]);
        }

        return redirect()->route('wards.index')->with('success', "Ward \"{$ward->name}\" created with {$ward->capacity} beds.");
    }

    // ───────────────────────────────────────────
    // Edit Ward Form
    // ───────────────────────────────────────────
    public function edit(Ward $ward)
    {
        $types = ['General', 'ICU', 'Pediatric', 'Maternity', 'Surgical', 'Orthopedic', 'Cardiac', 'Oncology', 'Emergency'];
        return view('wards.edit', compact('ward', 'types'));
    }

    // ───────────────────────────────────────────
    // Update Ward
    // ───────────────────────────────────────────
    public function update(Request $request, Ward $ward)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:General,ICU,Pediatric,Maternity,Surgical,Orthopedic,Cardiac,Oncology,Emergency',
            'capacity'    => 'required|integer|min:1|max:200',
            'floor'       => 'nullable|string|max:20',
            'building'    => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $oldCapacity = $ward->capacity;
        $ward->update($validated);

        // If capacity increased, add more beds
        if ($validated['capacity'] > $oldCapacity) {
            for ($i = $oldCapacity + 1; $i <= $validated['capacity']; $i++) {
                Bed::firstOrCreate(
                    ['ward_id' => $ward->id, 'bed_number' => $ward->name[0] . str_pad($i, 2, '0', STR_PAD_LEFT)],
                    ['status' => 'vacant']
                );
            }
        }

        return redirect()->route('wards.show', $ward)->with('success', 'Ward updated successfully.');
    }

    // ───────────────────────────────────────────
    // Soft-delete (deactivate) Ward
    // ───────────────────────────────────────────
    public function destroy(Ward $ward)
    {
        if ($ward->beds()->where('status', 'occupied')->count() > 0) {
            return back()->with('error', 'Cannot deactivate ward with occupied beds.');
        }
        $ward->update(['is_active' => false]);
        return redirect()->route('wards.index')->with('success', "Ward \"{$ward->name}\" has been deactivated.");
    }

    // ───────────────────────────────────────────
    // Assign Bed to Patient
    // ───────────────────────────────────────────
    public function assignBed(Request $request)
    {
        $validated = $request->validate([
            'bed_id'     => 'required|exists:beds,id',
            'patient_id' => 'required|exists:patients,id',
        ]);

        $bed = Bed::findOrFail($validated['bed_id']);
        $patient = Patient::findOrFail($validated['patient_id']);

        if ($bed->status !== 'vacant') {
            return back()->with('error', 'Selected bed is no longer available.');
        }

        if ($patient->bed) {
            return back()->with('error', 'Patient is already assigned to a bed.');
        }

        $bed->update([
            'status'      => 'occupied',
            'patient_id'  => $patient->id,
            'assigned_at' => now(),
        ]);

        $patient->update(['is_admitted' => true]);

        return back()->with('success', "Bed {$bed->bed_number} assigned to {$patient->full_name}.");
    }

    // ───────────────────────────────────────────
    // Release Bed (discharge patient)
    // ───────────────────────────────────────────
    public function releaseBed(Bed $bed)
    {
        if ($bed->patient) {
            $bed->patient->update(['is_admitted' => false]);
        }

        $bed->update([
            'status'      => 'vacant',
            'patient_id'  => null,
            'assigned_at' => null,
            'notes'       => null,
        ]);

        return back()->with('success', "Bed {$bed->bed_number} has been released.");
    }

    // ───────────────────────────────────────────
    // Update Bed Status (maintenance / reserve)
    // ───────────────────────────────────────────
    public function updateBedStatus(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'status' => 'required|in:vacant,reserved,maintenance',
            'notes'  => 'nullable|string|max:255',
        ]);

        if ($bed->status === 'occupied') {
            return back()->with('error', 'Cannot change status of an occupied bed. Release patient first.');
        }

        $bed->update($validated);
        return back()->with('success', "Bed {$bed->bed_number} status updated to {$validated['status']}.");
    }
}
