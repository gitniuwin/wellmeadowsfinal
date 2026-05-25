@extends('layouts.app')
@section('page-title', 'Edit Treatment')

@push('styles')
<style>
  .page-wrap { padding:28px; max-width:800px; margin:0 auto; }
  .back-link { display:inline-flex; color:#5B9BD5; text-decoration:none; font-size:13px; margin-bottom:24px; }
  .card { background:white; border:1px solid #C8D9EE; border-radius:12px; padding:24px; }
  .card-title { font-size:18px; font-weight:700; color:#1B2D5B; border-bottom:1px solid #C8D9EE; padding-bottom:16px; margin-bottom:20px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group.full { grid-column:1 / -1; }
  label { display:block; font-size:12px; font-weight:600; color:#6B7E9F; text-transform:uppercase; margin-bottom:5px; }
  input, select, textarea { width:100%; padding:9px 12px; border:1px solid #C8D9EE; border-radius:8px; font-size:13px; color:#1a2640; background:#F4F8FC; box-sizing:border-box; }
  textarea { resize:vertical; }
  .alert { background:#fdf0f0; color:#a03030; border:1px solid #f5c0c0; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
  .form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:24px; }
  .btn { padding:9px 18px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; font-weight:600; border:1px solid transparent; }
  .btn-primary { background:#1B2D5B; color:white; }
  .btn-secondary { background:#F4F8FC; color:#6B7E9F; border-color:#C8D9EE; }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <a href="{{ route('appointments.index') }}" class="back-link">Back to Appointments</a>

  <div class="card">
    <div class="card-title">Edit Treatment</div>

    @if($errors->any())
      <div class="alert">
        <strong>Validation errors:</strong>
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('treatments.update', $treatment) }}">
      @csrf
      @method('PUT')

      <div class="form-grid">
        <div class="form-group">
          <label for="patient_id">Patient</label>
          <select name="patient_id" id="patient_id" required>
            <option value="">Select patient...</option>
            @foreach($patients as $patient)
              <option value="{{ $patient->id }}" @selected(old('patient_id', $treatment->patient_id) == $patient->id)>
                {{ $patient->full_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="doctor_id">Doctor</label>
          <select name="doctor_id" id="doctor_id" required>
            <option value="">Select doctor...</option>
            @foreach($doctors as $doctor)
              <option value="{{ $doctor->id }}" @selected(old('doctor_id', $treatment->doctor_id) == $doctor->id)>
                {{ $doctor->full_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group full">
          <label for="appointment_id">Related Appointment</label>
          <select name="appointment_id" id="appointment_id">
            <option value="">No related appointment</option>
            @foreach($appointments as $appointment)
              <option value="{{ $appointment->id }}" @selected(old('appointment_id', $treatment->appointment_id) == $appointment->id)>
                {{ $appointment->patient->full_name }} - {{ $appointment->type }} ({{ $appointment->appointment_date->format('M d, Y') }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="diagnosis">Diagnosis</label>
          <input type="text" name="diagnosis" id="diagnosis" value="{{ old('diagnosis', $treatment->diagnosis) }}" required>
        </div>

        <div class="form-group">
          <label for="procedure">Procedure</label>
          <input type="text" name="procedure" id="procedure" value="{{ old('procedure', $treatment->procedure) }}" required>
        </div>

        <div class="form-group full">
          <label for="treatment_date">Treatment Date</label>
          <input type="date" name="treatment_date" id="treatment_date" value="{{ old('treatment_date', $treatment->treatment_date->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group full">
          <label for="notes">Notes</label>
          <textarea name="notes" id="notes" rows="4">{{ old('notes', $treatment->notes) }}</textarea>
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Treatment</button>
      </div>
    </form>
  </div>
</div>
@endsection
