<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with('schedule')->orderBy('department')->orderBy('last_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('ward', 'like', "%{$search}%");
            });
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $staff = $query->paginate(12)->withQueryString();

        $counts = [
            'total' => Staff::count(),
            'am' => Staff::where('shift', 'AM')->count(),
            'pm' => Staff::where('shift', 'PM')->count(),
            'night' => Staff::where('shift', 'Night')->count(),
        ];

        return view('schedules.index', compact('staff', 'counts'));
    }
}
