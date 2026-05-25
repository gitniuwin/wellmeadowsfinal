@extends('layouts.app')
@section('page-title', 'Treatment Details')

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
  .card { background:white; border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:20px; }
  .card-header { border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:16px; }
  .card-title { font-size:18px; font-weight:700; color:var(--navy); }
  .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .detail-row { margin-bottom:16px; }
  .detail-row.full { grid-column:1/-1; }
  .detail-label { font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); font-weight:500; margin-bottom:4px; }
  .detail-value { font-size:14px; color:var(--text); font-weight:500; }
  .actions { display:flex; gap:10px; margin-top:24px; border-top:1px solid var(--border); padding-top:18px; }
  .btn { padding:9px 18px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-weight:500; border:none; }
  .btn-primary { background:var(--navy); color:white; }
  .btn-primary:hover { background:var(--navy-light); }
  .btn-secondary { background:var(--off-white); color:var(--muted); border:1px solid var(--border); }
  .btn-secondary:hover { background:var(--sky-pale); }
  .btn-danger { background:#fdf0f0; color:var(--error); border:1px solid #f5c0c0; }
  .btn-danger:hover { background:#fde4e4; }
  @media (max-width: 760px) { .detail-grid { grid-template-columns:1fr; } .actions { flex-wrap:wrap; } }
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
      <div class="card-title">Treatment Details</div>
    </div>

    <div class="detail-grid">
      <div class="detail-row">
        <div class="detail-label">Patient</div>
        <div class="detail-value">{{ $treatment->patient->full_name }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Attending Doctor</div>
        <div class="detail-value">{{ $treatment->doctor->full_name }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Diagnosis</div>
        <div class="detail-value">{{ $treatment->diagnosis }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Procedure</div>
        <div class="detail-value">{{ $treatment->procedure }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Treatment Date</div>
        <div class="detail-value">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('F d, Y') }}</div>
      </div>
      @if($treatment->appointment)
        <div class="detail-row">
          <div class="detail-label">Linked Appointment</div>
          <div class="detail-value">{{ \Carbon\Carbon::parse($treatment->appointment->appointment_date)->format('F d, Y') }}</div>
        </div>
      @endif
      <div class="detail-row full">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $treatment->notes ?? 'None' }}</div>
      </div>
    </div>

    <div class="actions">
      <a href="{{ route('treatments.edit', $treatment->id) }}" class="btn btn-primary">Edit</a>
      <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Close</a>
      <form action="{{ route('treatments.destroy', $treatment->id) }}" method="POST" onsubmit="return confirm('Delete this treatment?')" style="margin-left:auto;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>
    </div>
  </div>
</div>
@endsection
