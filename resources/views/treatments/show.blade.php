@extends('layouts.app')
@section('page-title', 'View Treatment')

@push('styles')
<style>
  .page-wrap { padding:28px; max-width:800px; margin:0 auto; }
  .back-link { display:inline-flex; align-items:center; gap:6px; color:#5B9BD5; text-decoration:none; font-size:13px; margin-bottom:24px; }
  .card { background:white; border:1px solid #C8D9EE; border-radius:12px; padding:24px; }
  .card-title { font-size:18px; font-weight:700; color:#1B2D5B; border-bottom:1px solid #C8D9EE; padding-bottom:16px; margin-bottom:16px; }
  .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
  .detail-row.full { grid-column:1 / -1; }
  .detail-label { font-size:11px; text-transform:uppercase; color:#6B7E9F; font-weight:600; margin-bottom:4px; }
  .detail-value { font-size:14px; color:#1a2640; }
  .actions { display:flex; gap:10px; margin-top:24px; }
  .btn { padding:9px 18px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; font-weight:600; border:1px solid transparent; }
  .btn-primary { background:#1B2D5B; color:white; }
  .btn-secondary { background:#F4F8FC; color:#6B7E9F; border-color:#C8D9EE; }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <a href="{{ route('appointments.index') }}" class="back-link">Back to Appointments</a>

  <div class="card">
    <div class="card-title">Treatment Details</div>

    <div class="detail-grid">
      <div>
        <div class="detail-label">Patient</div>
        <div class="detail-value">{{ $treatment->patient->full_name }}</div>
      </div>
      <div>
        <div class="detail-label">Doctor</div>
        <div class="detail-value">{{ $treatment->doctor->full_name }}</div>
      </div>
      <div>
        <div class="detail-label">Treatment Date</div>
        <div class="detail-value">{{ $treatment->treatment_date->format('M d, Y') }}</div>
      </div>
      <div>
        <div class="detail-label">Related Appointment</div>
        <div class="detail-value">
          @if($treatment->appointment)
            <a href="{{ route('appointments.show', $treatment->appointment) }}">{{ $treatment->appointment->type }} appointment</a>
          @else
            None
          @endif
        </div>
      </div>
      <div class="detail-row full">
        <div class="detail-label">Diagnosis</div>
        <div class="detail-value">{{ $treatment->diagnosis }}</div>
      </div>
      <div class="detail-row full">
        <div class="detail-label">Procedure</div>
        <div class="detail-value">{{ $treatment->procedure }}</div>
      </div>
      <div class="detail-row full">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $treatment->notes ?: 'No notes recorded.' }}</div>
      </div>
    </div>

    <div class="actions">
      <a href="{{ route('treatments.edit', $treatment) }}" class="btn btn-primary">Edit</a>
      <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Close</a>
    </div>
  </div>
</div>
@endsection
