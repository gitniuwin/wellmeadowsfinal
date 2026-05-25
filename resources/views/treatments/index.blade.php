@extends('layouts.app')

@section('header-actions')
<style>
  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.15s; }
  .add-btn:hover { opacity:0.9; }
  .add-btn svg { width:14px; height:14px; }
  :root { --navy:#1B2D5B; --navy-dark:#111e3f; --sky:#5B9BD5; }
</style>
<button class="add-btn" onclick="document.getElementById('addTreatmentModal').classList.remove('hidden')">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Record Treatment
</button>
@endsection

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-5 mb-8">
    @php
        $cards = [
            ['label' => 'Total Treatments', 'value' => $totalTreatments ?? 0, 'color' => 'bg-navy', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label' => 'Active Diagnoses', 'value' => $activeDiagnoses ?? 0, 'color' => 'bg-accent', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['label' => 'Procedures Today', 'value' => $proceduresToday ?? 0, 'color' => 'bg-navy-light', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        ];
    @endphp
    @foreach($cards as $i => $card)
    <div class="stat-card {{ $card['color'] }} rounded-2xl p-5 text-white">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-sky-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
            </svg>
        </div>
        <p class="text-3xl font-display font-bold">{{ $card['value'] }}</p>
        <p class="text-sky-soft/70 text-xs mt-1 font-medium">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Treatments Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-display font-semibold text-navy-dark text-base">Treatment Records</h2>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search patient..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-navy/20 w-60">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-sky-pale text-navy/60 uppercase text-xs tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Patient</th>
                    <th class="px-6 py-3 text-left font-semibold">Diagnosis</th>
                    <th class="px-6 py-3 text-left font-semibold">Procedure</th>
                    <th class="px-6 py-3 text-left font-semibold">Attending Doctor</th>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($treatments ?? [] as $treatment)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-sky-pale flex items-center justify-center">
                                <svg class="w-4 h-4 text-navy/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <p class="font-medium text-navy-dark">{{ $treatment->patient->full_name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $treatment->diagnosis }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg bg-sky-pale text-navy text-xs font-medium">{{ $treatment->procedure }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $treatment->doctor->full_name }}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('treatments.show', $treatment->id) }}"
                                class="p-1.5 rounded-lg text-navy/40 hover:bg-sky-pale hover:text-navy transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('treatments.edit', $treatment->id) }}"
                                class="p-1.5 rounded-lg text-navy/40 hover:bg-sky-pale hover:text-navy transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('treatments.destroy', $treatment->id) }}" method="POST" onsubmit="return confirm('Delete this treatment record?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 rounded-lg text-navy/40 hover:bg-red-50 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium">No treatment records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ADD TREATMENT MODAL --}}
<div id="addTreatmentModal" class="hidden fixed inset-0 bg-navy-dark/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h3 class="font-display font-bold text-navy-dark text-lg">Record Treatment</h3>
            <button onclick="document.getElementById('addTreatmentModal').classList.add('hidden')"
                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('treatments.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Patient</label>
                    <select name="patient_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                        <option value="">Select patient...</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Doctor</label>
                    <select name="doctor_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                        <option value="">Select doctor...</option>
                        @foreach($doctors ?? [] as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Diagnosis</label>
                    <input type="text" name="diagnosis" required placeholder="e.g. Hypertension Stage 2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Procedure</label>
                    <input type="text" name="procedure" required placeholder="e.g. Blood pressure monitoring"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Treatment Date</label>
                    <input type="date" name="treatment_date" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Treatment notes..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-navy/20 bg-gray-50 resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addTreatmentModal').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-500 hover:bg-gray-50 transition">Cancel</button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-navy-dark text-white text-sm font-semibold hover:bg-navy-light transition">Save Treatment</button>
            </div>
        </form>
    </div>
</div>

@endsection