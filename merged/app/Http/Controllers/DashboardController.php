<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'total'   => Staff::count(),
            'doctors' => Staff::where('role', 'Doctor')->count(),
            'nurses'  => Staff::where('role', 'Nurse')->count(),
            'admin'   => Staff::where('role', 'Admin')->count(),
        ];

        $recentStaff = Staff::latest()->take(5)->get();

        $departments = [
            'Emergency', 'Cardiology', 'Pediatrics',
            'Orthopedics', 'Neurology', 'General Medicine'
        ];

        $deptSummary = [];
        foreach ($departments as $dept) {
            $doctors = Staff::where('department', $dept)->where('role', 'Doctor')->count();
            $nurses  = Staff::where('department', $dept)->where('role', 'Nurse')->count();
            $deptSummary[$dept] = [
                'doctors' => $doctors,
                'nurses'  => $nurses,
                'total'   => $doctors + $nurses,
            ];
        }

        return view('dashboard.index', compact('counts', 'recentStaff', 'deptSummary'));
    }
}