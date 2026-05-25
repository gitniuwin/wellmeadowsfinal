@extends('layouts.app')
@section('page-title', 'Patient Details')

@section('topbar-action')
  <a href="{{ route('patients.index') }}" class="back-btn">← Back</a>
  <a href="{{ route('patients.edit', $patient) }}" class="edit-btn">Edit Patient</a>
@endsection

@push('styles')
<style>
  .back-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; border:1px solid var(--border); border-radius:8px; font-size:13px; color:var(--muted); text-decoration:none; }
  .back-btn:hover { background:var(--sky-pale); }
  .edit-btn { padding:8px 16px; background:var(--navy); color:white; border-radius:8px; font-size:13px; text-decoration:none; }
  .edit-btn:hover { background:var(--navy-light); }

  .content { padding:28px; max-width:800px; }
  .profile-header { background:white; border:1px solid var(--border); border-radius:12px; padding:24px; display:flex; align-items:center; gap:20px; margin-bottom:20px; }
  .profile-avatar { width:64px; height:64px; border-radius:50%; background:var(--sky-pale); border:2px solid var(--sky-light); display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-size:24px; color:var(--navy); flex-shrink:0; }
  .profile-name { font-family:'Playfair Display',serif; font-size:22px; color:var(--navy-dark); }
  .profile-num { font-size:13px; color:var(--muted); margin-top:2px; }
  .profile-badges { display:flex; gap:8px; margin-top:8px; }
  .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
  .badge-admitted   { background:#DFFBEF; color:#1a7a50; }
  .badge-outpatient { background:#FFF4E0; color:#a06000; }
  .badge-male   { background:#EBF3FF; color:#1a4a8a; }
  .badge-female { background:#FDE8F5; color:#8a1a5a; }
  .badge-other  { background:#F0F0F0; color:#555; }

  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
  .info-card { background:white; border:1px solid var(--border); border-radius:12px; padding:20px; }
  .info-card-title { font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); font-weight:500; margin-bottom:14px; }
  .info-row { display:flex; flex-direction:column; margin-bottom:12px; }
  .info-row:last-child { margin-bottom:0; }
  .info-label { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  .info-value { font-size:14px; color:var(--text); font-weight:500; margin-top:2px; }

  .ward-card { background:var(--sky-pale); border:1px solid var(--sky-light); border-radius:12px; padding:20px; margin-bottom:20px; }
  .ward-card-title { font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--navy); font-weight:600; margin-bottom:10px; }
  .ward-detail { font-size:14px; color:var(--navy-dark); }
  .no-ward { font-size:13px; color:var(--muted); font-style:italic; }

  .links-card { background:white; border:1px solid var(--border); border-radius:12px; padding:20px; }
  .links-title { font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); font-weight:500; margin-bottom:14px; }
  .link-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border); }
  .link-row:last-child { border-bottom:none; }
  .link-label { font-size:13px; color:var(--text); }
  .link-action { font-size:12px; color:var(--sky); text-decoration:none; font-weight:500; }
  .link-action:hover { text-decoration:underline; }
  .record-list { margin-top:10px; display:flex; flex-direction:column; gap:8px; }
  .record-item { background:var(--off-white); border:1px solid var(--border); border-radius:8px; padding:10px 12px; font-size:12px; }
  .record-main { font-weight:600; color:var(--navy-dark); margin-bottom:2px; }
  .record-meta { color:var(--muted); }
</style>
@endpush

@section('content')
<div class="content">

  <div class="profile-header">
    <div class="profile-avatar">{{ strtoupper(substr($patient->first_name, 0, 1)) }}</div>
    <div>
      <div class="profile-name">{{ $patient->full_name }}</div>
      <div class="profile-num">{{ $patient->patient_number }}</div>
      <div class="profile-badges">
        <span class="badge {{ $patient->is_admitted ? 'badge-admitted' : 'badge-outpatient' }}">
          {{ $patient->is_admitted ? 'Admitted' : 'Outpatient' }}
        </span>
        <span class="badge {{ $patient->gender === 'Male' ? 'badge-male' : ($patient->gender === 'Female' ? 'badge-female' : 'badge-other') }}">
          {{ $patient->gender }}
        </span>
      </div>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-card">
      <div class="info-card-title">Personal Information</div>
      <div class="info-row">
        <span class="info-label">Full Name</span>
        <span class="info-value">{{ $patient->full_name }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Date of Birth</span>
        <span class="info-value">{{ $patient->date_of_birth->format('d M Y') }} (Age {{ $patient->date_of_birth->age }})</span>
      </div>
      <div class="info-row">
        <span class="info-label">Gender</span>
        <span class="info-value">{{ $patient->gender }}</span>
      </div>
    </div>
    <div class="info-card">
      <div class="info-card-title">Contact Information</div>
      <div class="info-row">
        <span class="info-label">Contact Number</span>
        <span class="info-value">{{ $patient->contact_number ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Address</span>
        <span class="info-value">{{ $patient->address ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Registered On</span>
        <span class="info-value">{{ $patient->created_at->format('d M Y') }}</span>
      </div>
    </div>
  </div>

  @if($patient->is_admitted)
  <div class="ward-card">
    <div class="ward-card-title">Ward & Bed Assignment</div>
    @if($patient->bed)
      <div class="ward-detail">
        Bed <strong>{{ $patient->bed->bed_number }}</strong>
        — Ward: <strong>{{ $patient->bed->ward->name ?? 'N/A' }}</strong>
      </div>
    @else
      <div class="no-ward">Patient is admitted but not yet assigned to a bed.
        Go to <a href="{{ route('wards.index') }}">Wards</a> to assign one.
      </div>
    @endif
  </div>
  @endif

  <div class="links-card">
    <div class="links-title">Patient Records</div>
    <div class="link-row">
      <span class="link-label">Appointments</span>
      <a href="{{ route('appointments.index') }}" class="link-action">View all →</a>
    </div>
    <div class="link-row">
      <span class="link-label">Treatments & Diagnoses</span>
      <a href="{{ route('treatments.index', ['patient_id' => $patient->id]) }}" class="link-action">View patient treatments →</a>
    </div>
    @if($patient->treatments->isNotEmpty())
      <div class="record-list">
        @foreach($patient->treatments->sortByDesc('treatment_date')->take(4) as $treatment)
          <div class="record-item">
            <div class="record-main">{{ $treatment->diagnosis }} — {{ $treatment->procedure }}</div>
            <div class="record-meta">
              {{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}
              @if($treatment->doctor)
                · Dr. {{ $treatment->doctor->last_name }}
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
    <div class="link-row">
      <span class="link-label">Treatment History</span>
      <a href="{{ route('history.index', ['patient_id' => $patient->id]) }}" class="link-action">View history →</a>
    </div>
  </div>

</div>
@endsection
