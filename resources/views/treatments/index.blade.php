@extends('layouts.app')

@section('title', 'Treatments')
@section('page-title', 'Appointment & Treatment')

@section('topbar-action')
    <button class="btn-primary" type="button" onclick="openTreatmentModal()">Record Treatment</button>
@endsection

@push('styles')
<style>
    .treatment-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:1.25rem; }
    .actions-cell { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(20,30,55,.55); z-index:200; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal { background:#fff; width:100%; max-width:560px; border-radius:10px; border:1px solid var(--border); padding:1.25rem; max-height:90vh; overflow:auto; }
    .modal-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .modal-title { font-size:16px; font-weight:600; color:var(--text); }
    .modal-close { border:1px solid var(--border); background:var(--white); border-radius:6px; width:30px; height:30px; cursor:pointer; }
    .form-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:1rem; }
</style>
@endpush

@section('content')
<div class="tabs-bar">
    <a href="{{ route('appointments.index') }}" class="tab">Appointments</a>
    <a href="{{ route('treatments.index') }}" class="tab active">Treatments</a>
    <a href="{{ route('history.index') }}" class="tab">Patient History</a>
</div>

<div class="treatment-stats">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Treatments</div>
        <div class="stat-value">{{ $totalTreatments ?? 0 }}</div>
        <div class="stat-sub">All treatment records</div>
    </div>
    <div class="stat-card stat-vacant">
        <div class="stat-label">Active Diagnoses</div>
        <div class="stat-value">{{ $activeDiagnoses ?? 0 }}</div>
        <div class="stat-sub">Recorded today</div>
    </div>
    <div class="stat-card stat-other">
        <div class="stat-label">Procedures Today</div>
        <div class="stat-value">{{ $proceduresToday ?? 0 }}</div>
        <div class="stat-sub">Daily procedure count</div>
    </div>
</div>

<div class="table-card">
    <div class="section-header" style="padding:1rem; margin:0; border-bottom:1px solid var(--border)">
        <div class="section-title">Treatment Records</div>
        <a class="btn-secondary" href="{{ route('history.index') }}">Patient History</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Diagnosis</th>
                <th>Procedure</th>
                <th>Attending Doctor</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($treatments ?? [] as $treatment)
                <tr>
                    <td><strong>{{ $treatment->patient->full_name }}</strong></td>
                    <td>{{ $treatment->diagnosis }}</td>
                    <td><span class="tbl-status-badge reserved">{{ $treatment->procedure }}</span></td>
                    <td>{{ $treatment->doctor->full_name }}</td>
                    <td>{{ $treatment->treatment_date->format('M d, Y') }}</td>
                    <td>
                        <div class="actions-cell">
                            <form action="{{ route('treatments.destroy', $treatment) }}" method="POST" onsubmit="return confirm('Delete this treatment record?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-sm-red" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">No treatment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $treatments->links() }}

<div class="modal-overlay" id="treatment-modal">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-title">Record Treatment</div>
            <button class="modal-close" type="button" onclick="closeTreatmentModal()">x</button>
        </div>
        <form action="{{ route('treatments.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Patient <span class="required">*</span></label>
                    <select class="form-select" name="patient_id" required>
                        <option value="">Select patient</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Doctor <span class="required">*</span></label>
                    <select class="form-select" name="doctor_id" required>
                        <option value="">Select doctor</option>
                        @foreach($doctors ?? [] as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Assigned Nurses</label>
                <select class="form-select" name="nurse_ids[]" multiple size="4">
                    @foreach($nurses ?? [] as $nurse)
                        <option value="{{ $nurse->id }}">{{ $nurse->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Diagnosis <span class="required">*</span></label>
                <input class="form-input" type="text" name="diagnosis" value="{{ old('diagnosis') }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Procedure <span class="required">*</span></label>
                    <input class="form-input" type="text" name="procedure" value="{{ old('procedure') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Treatment Date <span class="required">*</span></label>
                    <input class="form-input" type="date" name="treatment_date" value="{{ old('treatment_date', now()->toDateString()) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-textarea" name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeTreatmentModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Treatment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openTreatmentModal() { document.getElementById('treatment-modal').classList.add('open'); }
    function closeTreatmentModal() { document.getElementById('treatment-modal').classList.remove('open'); }

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) modal.classList.remove('open');
        });
    });

    @if($errors->any())
        openTreatmentModal();
    @endif
</script>
@endpush
