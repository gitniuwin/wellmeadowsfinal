@extends('layouts.app')

@section('content')

<div class="grid grid-cols-3 gap-6">
    {{-- Patient Search Panel --}}
    <div class="col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-28">
            <h2 class="font-display font-semibold text-navy-dark mb-4">Patient History Lookup</h2>
            <div class="relative mb-4">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="patientSearch" placeholder="Search by name or ID..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-navy/20">
            </div>

            <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                @forelse($patients ?? [] as $patient)
                <a href="{{ route('history.index', ['patient_id' => $patient->id]) }}"
                    class="flex items-center gap-3 p-3 rounded-xl hover:bg-sky-pale transition cursor-pointer {{ (request('patient_id') == $patient->id) ? 'bg-sky-pale ring-1 ring-navy/20' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-navy/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-navy-dark">{{ $patient->full_name }}</p>
                        <p class="text-xs text-gray-400">ID #{{ $patient->id }}</p>
                    </div>
                </a>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">No patients found</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- History Timeline --}}
    <div class="col-span-2">
        @if(isset($selectedPatient))
        {{-- Patient Info Banner --}}
        <div class="bg-navy rounded-2xl p-5 text-white mb-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-sky-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-display font-bold text-xl">{{ $selectedPatient->full_name }}</h3>
                <p class="text-sky-soft/70 text-sm">Patient ID #{{ $selectedPatient->id }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-display font-bold">{{ $history->count() }}</p>
                <p class="text-sky-soft/70 text-xs">Total Records</p>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-display font-semibold text-navy-dark mb-6">Treatment History</h2>

            @forelse($history ?? [] as $record)
            <div class="relative pl-8 pb-8 last:pb-0">
                {{-- Timeline dot --}}
                <div class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-navy bg-white"></div>
                @if(!$loop->last)
                <div class="absolute left-[7px] top-5 bottom-0 w-0.5 bg-gray-100"></div>
                @endif

                <div class="bg-gray-50 rounded-xl p-4 hover:bg-sky-pale/50 transition">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-navy text-white text-xs font-semibold mb-2">
                                {{ $record->type ?? 'Treatment' }}
                            </span>
                            <h4 class="font-semibold text-navy-dark text-sm">{{ $record->diagnosis ?? $record->title }}</h4>
                        </div>
                        <p class="text-xs text-gray-400 flex-shrink-0 ml-4">
                            {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                        </p>
                    </div>
                    @if($record->procedure ?? false)
                    <p class="text-sm text-gray-600 mb-2"><span class="font-medium">Procedure:</span> {{ $record->procedure }}</p>
                    @endif
                    @if($record->notes ?? false)
                    <p class="text-sm text-gray-500">{{ $record->notes }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Attending: <span class="font-medium text-navy/60">{{ $record->doctor->full_name ?? 'N/A' }}</span></p>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium">No history records found</p>
                <p class="text-xs text-gray-300 mt-1">This patient has no recorded treatments yet</p>
            </div>
            @endforelse
        </div>

        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center py-24 text-gray-400">
            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="text-base font-semibold text-gray-500">Select a patient</p>
            <p class="text-sm text-gray-300 mt-1">Choose a patient from the list to view their treatment history</p>
        </div>
        @endif
    </div>
</div>

@endsection
