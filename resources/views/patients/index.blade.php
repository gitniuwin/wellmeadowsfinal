@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'Patient Management')

@section('topbar-action')
    <button class="btn-primary" type="button" onclick="openModal('addModal')">Register Patient</button>
@endsection

@push('styles')
<style>
    .patient-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:1.25rem; }
    .filters-bar { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1rem; margin-bottom:1rem; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .filters-bar .search-input { flex:1; min-width:220px; }
    .actions-cell { display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
    .patient-name { font-weight:600; color:var(--text); }
    .patient-num { font-size:12px; color:var(--text-muted); }
    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
    .badge-admitted { background:var(--green-light); color:var(--green); }
    .badge-outpatient { background:var(--orange-light); color:var(--orange); }
    .badge-male { background:var(--blue-light); color:var(--blue); }
    .badge-female { background:#fde8f5; color:#8a1a5a; }
    .badge-other { background:var(--gray-light); color:var(--gray); }
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
<div class="patient-stats">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Patients</div>
        <div class="stat-value">{{ $totalPatients }}</div>
        <div class="stat-sub">All registered patients</div>
    </div>
    <div class="stat-card stat-vacant">
        <div class="stat-label">Admitted</div>
        <div class="stat-value">{{ $admitted }}</div>
        <div class="stat-sub">Currently in ward</div>
    </div>
    <div class="stat-card stat-other">
        <div class="stat-label">Outpatients</div>
        <div class="stat-value">{{ $outpatients }}</div>
        <div class="stat-sub">Not admitted</div>
    </div>
</div>

<form method="GET" action="{{ route('patients.index') }}" class="filters-bar">
    <input class="search-input" type="text" name="search" placeholder="Search by name or patient number" value="{{ request('search') }}">
    <select class="form-select" name="status" style="width:160px">
        <option value="">All status</option>
        <option value="admitted" @selected(request('status') === 'admitted')>Admitted</option>
        <option value="outpatient" @selected(request('status') === 'outpatient')>Outpatient</option>
    </select>
    <button type="submit" class="btn-search">Filter</button>
    @if(request('search') || request('status'))
        <a href="{{ route('patients.index') }}" class="btn-secondary">Clear</a>
    @endif
</form>

<div class="table-card">
    <div class="section-header" style="padding:1rem; margin:0; border-bottom:1px solid var(--border)">
        <div class="section-title">Patient Records</div>
        <span class="text-muted">{{ $patients->total() }} patient(s)</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Patient No.</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patients as $patient)
                <tr>
                    <td><div class="patient-name">{{ $patient->full_name }}</div></td>
                    <td><span class="patient-num">{{ $patient->patient_number }}</span></td>
                    <td>
                        <span class="badge {{ $patient->gender === 'Male' ? 'badge-male' : ($patient->gender === 'Female' ? 'badge-female' : 'badge-other') }}">
                            {{ $patient->gender }}
                        </span>
                    </td>
                    <td>{{ $patient->date_of_birth->format('d M Y') }}<br><small class="text-muted">Age {{ $patient->date_of_birth->age }}</small></td>
                    <td>{{ $patient->contact_number ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $patient->is_admitted ? 'badge-admitted' : 'badge-outpatient' }}">
                            {{ $patient->is_admitted ? 'Admitted' : 'Outpatient' }}
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('patients.show', $patient) }}" class="btn-sm-blue">View</a>
                            <a href="{{ route('patients.edit', $patient) }}" class="btn-sm-gray">Edit</a>
                            @if($patient->is_admitted)
                                <form method="POST" action="{{ route('patients.discharge', $patient) }}" onsubmit="return confirm('Discharge {{ $patient->full_name }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-sm-green">Discharge</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Delete this patient record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm-red">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">No patients found. Register the first patient using the button above.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $patients->links() }}

<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-title">Register New Patient</div>
            <button class="modal-close" type="button" onclick="closeModal('addModal')">x</button>
        </div>
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">First Name <span class="required">*</span></label>
                    <input class="form-input" type="text" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span class="required">*</span></label>
                    <input class="form-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date of Birth <span class="required">*</span></label>
                    <input class="form-input" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender <span class="required">*</span></label>
                    <select class="form-select" name="gender" required>
                        <option value="">Select</option>
                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                        <option value="Other" @selected(old('gender') === 'Other')>Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input class="form-input" type="text" name="contact_number" value="{{ old('contact_number') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Admission</label>
                    <label style="display:flex; align-items:center; gap:8px; padding-top:8px; text-transform:none; letter-spacing:0">
                        <input type="checkbox" name="is_admitted" value="1" @checked(old('is_admitted'))>
                        Admit patient now
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea class="form-textarea" name="address" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn-primary">Register Patient</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) modal.classList.remove('open');
        });
    });

    @if($errors->any())
        openModal('addModal');
    @endif
</script>
@endpush
