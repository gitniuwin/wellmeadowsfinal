@extends('layouts.app')
@section('page-title', 'View Appointment')

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
  .card { background:white; border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:20px; }
  .card-header { border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:16px; }
  .card-title { font-size:18px; font-weight:700; color:var(--navy); }
  .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .detail-row { margin-bottom:16px; }
  .detail-label { font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); font-weight:500; margin-bottom:4px; }
  .detail-value { font-size:14px; color:var(--text); font-weight:500; }
  .badge { display:inline-flex; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:500; }
  .badge-scheduled { background:#FFF3CD; color:#856404; }
  .badge-completed { background:#D1FAE5; color:#065F46; }
  .badge-cancelled { background:#FEE2E2; color:#991B1B; }
  .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
  .alert-success { background:#DFFBEF; color:#1a7a50; border:1px solid #b2ecd4; }
  .actions { display:flex; gap:10px; margin-top:24px; }
  .btn { padding:9px 18px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-weight:500; border:none; }
  .btn-primary { background:var(--navy); color:white; }
  .btn-primary:hover { background:var(--navy-light); }
  .btn-secondary { background:var(--off-white); color:var(--muted); border:1px solid var(--border); }
  .btn-secondary:hover { background:var(--sky-pale); }
  .treatments-section { background:var(--off-white); padding:16px; border-radius:8px; margin-top:16px; }
  .treatments-list { list-style:none; padding:0; margin:0; }
  .treatments-list li { padding:10px 0; border-bottom:1px solid var(--border); }
  .treatments-list li:last-child { border-bottom:none; }
</style>
@endpush

@section('content')
<div class="page-wrap">
  <a href="{{ route('appointments.index') }}" class="back-link">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back to Appointments
  </a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-header">
      <div class="card-title">Appointment Details</div>
    </div>

    <div class="detail-grid">
      <div class="detail-row">
        <div class="detail-label">Patient Name</div>
        <div class="detail-value">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Doctor</div>
        <div class="detail-value">{{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Appointment Type</div>
        <div class="detail-value">{{ $appointment->type }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
          <span class="badge badge-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
        </div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Date & Time</div>
        <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}</div>
      </div>
    </div>

    @if($appointment->notes)
      <div class="detail-row" style="grid-column:1/-1; margin-top:16px;">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $appointment->notes }}</div>
      </div>
    @endif

    @if($appointment->treatments->count())
      <div class="treatments-section" style="grid-column:1/-1;">
        <div class="detail-label">Associated Treatments ({{ $appointment->treatments->count() }})</div>
        <ul class="treatments-list">
          @foreach($appointment->treatments as $treatment)
            <li>
              <strong>{{ $treatment->diagnosis }}</strong> - {{ $treatment->procedure }}
              <br><small style="color:var(--muted);">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}</small>
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="actions">
      <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
      </a>
      <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Close</a>
    </div>
  </div>
</div>
@endsection
