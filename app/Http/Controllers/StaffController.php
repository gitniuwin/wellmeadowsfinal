<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Schedule;
use App\Models\Responsibility;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Staff::with(['schedule', 'responsibilities']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('role', 'like', "%$s%")
                  ->orWhere('department', 'like', "%$s%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $staff    = $query->paginate(10)->withQueryString();
        $allStaff = Staff::with(['schedule', 'responsibilities'])->get();

        $staffJson = $allStaff->map(function ($s) {
            return [
                'id'         => $s->id,
                'first_name' => $s->first_name,
                'last_name'  => $s->last_name,
                'full_name'  => $s->full_name,
                'role'       => $s->role,
                'department' => $s->department,
                'ward'       => $s->ward,
                'shift'      => $s->shift,
                'email'      => $s->email,
                'phone'      => $s->phone,
                'status'     => $s->status,
            ];
        })->values();

        $departments = [
            'Emergency', 'Cardiology', 'Pediatrics',
            'Orthopedics', 'Neurology', 'General Medicine', 'Administration'
        ];

        $deptSummary = [];
        foreach ($departments as $dept) {
            $deptSummary[$dept] = [
                'doctors' => Staff::where('department', $dept)->where('role', 'Doctor')->count(),
                'nurses'  => Staff::where('department', $dept)->where('role', 'Nurse')->count(),
                'head'    => Staff::where('department', $dept)->where('role', 'Doctor')->first(),
            ];
        }

        $counts = [
            'total'   => Staff::count(),
            'doctors' => Staff::where('role', 'Doctor')->count(),
            'nurses'  => Staff::where('role', 'Nurse')->count(),
            'admin'   => Staff::where('role', 'Admin')->count(),
        ];

        $dayMap = ['Mon' => 'mon', 'Tue' => 'tue', 'Wed' => 'wed', 'Thu' => 'thu', 'Fri' => 'fri', 'Sat' => 'sat', 'Sun' => 'sun'];
        $todayKey = $dayMap[Carbon::now()->format('D')];
        $timelineTimes = ['07:30 AM', '08:10 AM', '08:45 AM', '09:20 AM', '09:55 AM', '10:30 AM'];

        $flowEvents = $allStaff->filter(function ($s) use ($todayKey) {
            return optional($s->schedule)->{$todayKey};
        })->take(6)->values()->map(function ($s, $idx) use ($timelineTimes) {
            $time = $timelineTimes[$idx] ?? Carbon::now()->subMinutes(10 * $idx)->format('h:i A');
            $wardLabel = $s->ward ? $s->ward : $s->department;
            $action = match($s->shift) {
                'AM' => "Started morning rounds in {$wardLabel}",
                'PM' => "Taking afternoon coverage in {$wardLabel}",
                'Night' => "On night watch for {$wardLabel}",
                default => "Scheduled for {$s->shift} in {$wardLabel}",
            };
            return [
                'time' => $time,
                'title' => "{$s->full_name} · {$s->role}",
                'description' => $action,
                'status' => $s->status,
                'statusClass' => $s->status === 'Active' ? 'Active' : 'Leave',
            ];
        });

        if ($flowEvents->isEmpty()) {
            $flowEvents = $allStaff->take(4)->map(function ($s, $idx) use ($timelineTimes) {
                $time = $timelineTimes[$idx] ?? Carbon::now()->subMinutes(10 * $idx)->format('h:i A');
                $wardLabel = $s->ward ? $s->ward : $s->department;
                $action = match($s->shift) {
                    'AM' => "Prepared for morning coverage in {$wardLabel}",
                    'PM' => "Reviewed afternoon duty roster for {$wardLabel}",
                    'Night' => "Confirmed night shift assignment for {$wardLabel}",
                    default => "Confirmed staffing assignment for {$wardLabel}",
                };
                return [
                    'time' => $time,
                    'title' => "{$s->full_name} · {$s->role}",
                    'description' => $action,
                    'status' => $s->status,
                    'statusClass' => $s->status === 'Active' ? 'Active' : 'Leave',
                ];
            });
        }

        return view('staff.index', compact(
            'staff', 'allStaff', 'staffJson',
            'departments', 'deptSummary', 'counts', 'user', 'flowEvents'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'role'       => 'required|string',
            'department' => 'required|string',
            'shift'      => 'required|string',
            'email'      => 'required|email|unique:staff,email',
        ]);

        $staff = Staff::create($request->only([
            'first_name', 'last_name', 'role',
            'department', 'ward', 'shift', 'email', 'phone', 'status'
        ]));

        Schedule::create(['staff_id' => $staff->id]);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit($id)
    {
        $staff = Staff::with(['schedule', 'responsibilities'])->findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'role'       => 'required|string',
            'department' => 'required|string',
            'shift'      => 'required|string',
            'email'      => 'required|email|unique:staff,email,' . $id,
        ]);

        $staff->update($request->only([
            'first_name', 'last_name', 'role',
            'department', 'ward', 'shift', 'email', 'phone', 'status'
        ]));

        return redirect()->route('staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy($id)
    {
        Staff::findOrFail($id)->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member removed.');
    }

    public function updateSchedule(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $days  = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $data  = ['staff_id' => $staff->id];
        foreach ($days as $day) {
            $data[$day] = $request->has($day);
        }
        Schedule::updateOrCreate(['staff_id' => $staff->id], $data);
        $staff->update(['shift' => $request->shift]);
        return redirect()->route('staff.index')->with('success', 'Schedule updated.');
    }

    public function updateResponsibilities(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->responsibilities()->delete();
        if ($request->filled('responsibilities')) {
            foreach ($request->responsibilities as $desc) {
                if (trim($desc) !== '') {
                    Responsibility::create(['staff_id' => $staff->id, 'description' => trim($desc)]);
                }
            }
        }
        return redirect()->route('staff.index')->with('success', 'Responsibilities updated.');
    }

    public function search(Request $request)
    {
        $q       = $request->get('q', '');
        $results = Staff::where('first_name', 'like', "%$q%")
            ->orWhere('last_name', 'like', "%$q%")
            ->limit(8)->get()
            ->map(function ($s) {
                return [
                    'id'        => $s->id,
                    'full_name' => $s->full_name,
                    'role'      => $s->role,
                    'initials'  => $s->initials,
                ];
            });
        return response()->json($results);
    }
}