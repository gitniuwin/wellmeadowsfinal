@extends('layouts.app')
@section('page-title', 'Edit Patient')

@section('topbar-action')
  <a href="{{ route('patients.index') }}" class="back-btn">← Back to Patients</a>
@endsection

@push('styles')
<style>
  .back-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; border:1px solid var(--border); border-radius:8px; font-size:13px; color:var(--muted); text-decoration:none; }
  .back-btn:hover { background:var(--sky-pale); color:var(--navy); }

  .content { padding:28px; max-width:680px; }
  .form-card { background:white; border:1px solid var(--border); border-radius:12px; padding:28px; }
  .form-card-title { font-family:'Playfair Display',serif; font-size:18px; color:var(--navy); margin-bottom:6px; }
  .form-card-sub { font-size:13px; color:var(--muted); margin-bottom:24px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  label { font-size:11px; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  input, select, textarea { padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  input:focus, select:focus, textarea:focus { border-color:var(--sky); background:white; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:24px; padding-top:20px; border-top:1px solid var(--border); }
  .btn-cancel { padding:9px 20px; border:1px solid var(--border); border-radius:8px; background:white; font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:var(--muted); text-decoration:none; }
  .btn-submit { padding:9px 20px; border:none; border-radius:8px; background:var(--navy); font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:white; font-weight:500; }
  .btn-submit:hover { background:var(--navy-light); }
  .checkbox-row { display:flex; align-items:center; gap:8px; font-size:13px; margin-top:6px; }
  .checkbox-row input { width:16px; height:16px; padding:0; }
</style>
@endpush

@section('content')
<div class="content">
  <div class="form-card">
    <div class="form-card-title">{{ $patient->full_name }}</div>
    <div class="form-card-sub">Patient No: {{ $patient->patient_number }}</div>

    <form method="POST" action="{{ route('patients.update', $patient) }}">
      @csrf @method('PUT')
      <div class="form-grid">
        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required>
          @error('first_name')<small style="color:#D94F4F">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required>
          @error('last_name')<small style="color:#D94F4F">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Date of Birth *</label>
          <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required>
          @error('date_of_birth')<small style="color:#D94F4F">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="Male"   {{ old('gender', $patient->gender) === 'Male'   ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('gender', $patient->gender) === 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other"  {{ old('gender', $patient->gender) === 'Other'  ? 'selected' : '' }}>Other</option>
          </select>
        </div>
        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contact_number" value="{{ old('contact_number', $patient->contact_number) }}">
        </div>
        <div class="form-group">
          <label>Admission Status</label>
          <div class="checkbox-row">
            <input type="checkbox" name="is_admitted" value="1" {{ old('is_admitted', $patient->is_admitted) ? 'checked' : '' }}>
            <span>Currently admitted</span>
          </div>
        </div>
        <div class="form-group full">
          <label>Address</label>
          <textarea name="address" rows="2">{{ old('address', $patient->address) }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <a href="{{ route('patients.index') }}" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection