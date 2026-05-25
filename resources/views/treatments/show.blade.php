@extends('layouts.app')

@section('title', 'Treatment Details')

@section('header-actions')
<a href="{{ route('treatments.index') }}"
    class="flex items-center gap-2 bg-gray-100 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-gray-200 transition-colors">
    ← Back
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <h2 class="font-display font-bold text-navy-dark text-lg">Treatment Details</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Patient</p>
                <p class="text-sm font-medium text-navy-dark">{{ $treatment->patient->first_name }} {{ $treatment->patient->last_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Attending Doctor</p>
                <p class="text-sm font-medium text-navy-dark">{{ $treatment->doctor->first_name }} {{ $treatment->doctor->last_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Diagnosis</p>
                <p class="text-sm text-gray-700">{{ $treatment->diagnosis }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Procedure</p>
                <p class="text-sm text-gray-700">{{ $treatment->procedure }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Treatment Date</p>
                <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('F d, Y') }}</p>
            </div>
            @if($treatment->appointment)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Linked Appointment</p>
                <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($treatment->appointment->appointment_date)->format('F d, Y') }}</p>
            </div>
            @endif
            <div class="col-span-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Notes</p>
                <p class="text-sm text-gray-700">{{ $treatment->notes ?? '—' }}</p>
            </div>
        </div>

        <div class="flex gap-3 pt-2 border-t border-gray-100">
            <a href="{{ route('treatments.edit', $treatment->id) }}"
                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition text-center">Edit</a>
            <form action="{{ route('treatments.destroy', $treatment->id) }}" method="POST" onsubmit="return confirm('Delete this treatment?')">
                @csrf @method('DELETE')
                <button class="px-6 py-2.5 rounded-xl bg-red-50 text-red-500 text-sm font-semibold hover:bg-red-100 transition">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection