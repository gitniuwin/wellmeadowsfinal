@extends('layouts.app')
@section('page-title', 'Record Treatment')

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-pale:#D6EAFA; --off-white:#F4F8FC;
    --muted:#6B7E9F; --border:#C8D9EE; --text:#1a2640; --error:#D94F4F;
  }
  .page-wrap { padding:28px; max-width:800px; margin:0 auto; }
  .back-link { display:inline-flex; align-items:center; gap:6px; color:var(--sky); text-decoration:none; font-size:13px; margin-bottom:24px; }
  .back-link:hover { color:var(--navy); }
  .back-link svg { width:16px; height:16px; }
  .card { background:white; border:1px solid var(--border); border-radius:12px; padding:24px; }
  .card-header { border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:20px; }
  .card-title { font-size:18px; font-weight:700; color:var(--navy); }
  .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
  .alert-error { background:#fdf0f0; color:#a03030; border:1px solid #f5c0c0; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  label { font-size:12px; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  input, select, textarea { padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  input:focus, select:focus, textarea:focus { border-color:var(--sky); background:white; }
  textarea { resize:vertical; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:24px; }
  .btn { padding:9px 18px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-weight:500; border:none; }
  .btn-primary { background:var(--navy); color:white; }
  .btn-primary:hover { background:var(--navy-light); }
  .btn-secondary { background:var(--off-white); color:var(--muted); border:1px solid var(--border); }
  .btn-secondary:hover { background:var(--sky-pale); }
  @media (max-width: 760px) { .form-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <a href="{{ route('appointments.index') }}" class="back-link">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back to Appointments
  </a>

  <div class="card">
    <div class="card-header">
      <div class="card-title">Record New Treatment</div>
    </div>

    @if($errors->any())
      <div class="alert alert-error">
        <strong>Validation errors:</strong>
        <ul style="margin:8px 0 0 16px; padding:0;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('treatments.store') }}" method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label for="patient_id">Patient</label>
          <select name="patient_id" id="patient_id" required>
            <option value="">Select patient...</option>
            @foreach($patients ?? [] as $patient)
              <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="doctor_id">Doctor</label>
          <select name="doctor_id" id="doctor_id" required>
            <option value="">Select doctor...</option>
            @foreach($doctors ?? [] as $doctor)
              <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="appointment_id">Linked Appointment</label>
          <select name="appointment_id" id="appointment_id">
            <option value="">None</option>
            @foreach($appointments ?? [] as $appt)
              <option value="{{ $appt->id }}">{{ $appt->patient->full_name ?? '' }} - {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="treatment_date">Treatment Date</label>
          <input type="date" name="treatment_date" id="treatment_date" required>
        </div>

        <div class="form-group full">
          <label for="diagnosis">Diagnosis</label>
          <input type="text" name="diagnosis" id="diagnosis" required placeholder="Diagnosis">
        </div>

        <div class="form-group full">
          <label for="procedure">Procedure</label>
          <input type="text" name="procedure" id="procedure" required placeholder="Procedure">
        </div>

        <div class="form-group full">
          <label for="notes">Notes</label>
          <textarea name="notes" id="notes" rows="3" placeholder="Treatment notes..."></textarea>
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Treatment</button>
      </div>
    </form>
  </div>
</div>
@endsection
