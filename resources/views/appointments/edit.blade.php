@extends('layouts.app')
@section('page-title', 'Edit Appointment')

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-light:#A8CBF0; --sky-pale:#D6EAFA;
    --off-white:#F4F8FC; --muted:#6B7E9F; --border:#C8D9EE;
    --text:#1a2640; --error:#D94F4F; --success:#2E8B6A;
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
  .error-message { font-size:12px; color:var(--error); margin-top:4px; }
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
      <div class="card-title">Edit Appointment</div>
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

    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="form-group">
          <label for="patient_id">Patient</label>
          <select name="patient_id" id="patient_id" required>
            <option value="">Select patient…</option>
            @foreach($patients as $patient)
              <option value="{{ $patient->id }}" @if($appointment->patient_id == $patient->id) selected @endif>
                {{ $patient->first_name }} {{ $patient->last_name }}
              </option>
            @endforeach
          </select>
          @error('patient_id')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label for="doctor_id">Doctor</label>
          <select name="doctor_id" id="doctor_id" required>
            <option value="">Select doctor…</option>
            @foreach($doctors as $doctor)
              <option value="{{ $doctor->id }}" @if($appointment->doctor_id == $doctor->id) selected @endif>
                {{ $doctor->first_name }} {{ $doctor->last_name }}
              </option>
            @endforeach
          </select>
          @error('doctor_id')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label for="type">Appointment Type</label>
          <select name="type" id="type" required>
            <option value="">Select type…</option>
            <option value="Consultation" @if($appointment->type == 'Consultation') selected @endif>Consultation</option>
            <option value="Follow-up" @if($appointment->type == 'Follow-up') selected @endif>Follow-up</option>
            <option value="Emergency" @if($appointment->type == 'Emergency') selected @endif>Emergency</option>
            <option value="Routine Check" @if($appointment->type == 'Routine Check') selected @endif>Routine Check</option>
          </select>
          @error('type')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label for="status">Status</label>
          <select name="status" id="status" required>
            <option value="">Select status…</option>
            <option value="scheduled" @if($appointment->status == 'scheduled') selected @endif>Scheduled</option>
            <option value="completed" @if($appointment->status == 'completed') selected @endif>Completed</option>
            <option value="cancelled" @if($appointment->status == 'cancelled') selected @endif>Cancelled</option>
          </select>
          @error('status')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-group full">
          <label for="appointment_date">Appointment Date & Time</label>
          <input type="datetime-local" name="appointment_date" id="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d\TH:i') }}" required>
          @error('appointment_date')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-group full">
          <label for="notes">Notes</label>
          <textarea name="notes" id="notes" rows="3" placeholder="Appointment notes…">{{ old('notes', $appointment->notes) }}</textarea>
          @error('notes')<div class="error-message">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Appointment</button>
      </div>
    </form>
  </div>
</div>
@endsection
