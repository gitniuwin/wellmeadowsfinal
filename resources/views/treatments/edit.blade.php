@extends('layouts.app')

@section('title', 'Edit Treatment')

@section('header-actions')
<a href="{{ route('treatments.index') }}"
    class="flex items-center gap-2 bg-gray-100 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-gray-200 transition-colors">
    ← Back
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-display font-bold text-navy-dark text-lg mb-6">Edit Treatment</h2>

        <form action="{{ route('treatments.update', $treatment->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Patient</label>
                    <select name="patient_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ $treatment->patient_id == $patient->id ? 'selected' : '' }}>
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Doctor</label>
                    <select name="doctor_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $treatment->doctor_id == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->first_name }} {{ $doctor->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Linked Appointment</label>
                    <select name="appointment_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                        <option value="">None</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}" {{ $treatment->appointment_id == $appt->id ? 'selected' : '' }}>
                                {{ $appt->patient->first_name }} {{ $appt->patient->last_name }} — {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Treatment Date</label>
                    <input type="date" name="treatment_date" required value="{{ $treatment->treatment_date }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Diagnosis</label>
                    <input type="text" name="diagnosis" required value="{{ $treatment->diagnosis }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Procedure</label>
                    <input type="text" name="procedure" required value="{{ $treatment->procedure }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50 resize-none">{{ $treatment->notes }}</textarea>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <a href="{{ route('treatments.index') }}"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-500 hover:bg-gray-50 transition text-center">Cancel</a>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-navy-dark text-white text-sm font-semibold hover:bg-navy-light transition">Update Treatment</button>
            </div>
        </form>
    </div>
</div>
@endsection