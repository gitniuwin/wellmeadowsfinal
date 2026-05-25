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

  .links-card { background:white; border:1px solid var(--border); border-radius:12px; padding:12px; }
  .links-title { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); font-weight:500; margin-bottom:8px; }
  .link-row { display:flex; justify-content:space-between; align-items:center; padding:3px 0; border-bottom:1px solid var(--border); line-height:1; }
  .link-row:last-child { border-bottom:none; }
  .link-label { font-size:12px; color:var(--text); line-height:1.2; }
  .link-action { font-size:11px; color:var(--sky); text-decoration:none; font-weight:500; line-height:1; }
  .link-action:hover { text-decoration:underline; }
</style>
@endpush

@section('content')
<div class="content">

  @if(session('warning'))
  <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; color: #856404; font-size: 13px; display: flex; align-items: flex-start; gap: 12px;">
    <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-top: 2px; flex-shrink: 0;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0l-5.708 9.614a1.13 1.13 0 0 0 1.97 1.32h11.456a1.13 1.13 0 0 0 1.97-1.32L8.982 1.566ZM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5Zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z"/></svg>
    <div>{{ session('warning') }}</div>
  </div>
  @endif

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

  <!-- BILLING INFORMATION CARD -->
  @php
    $bills = $patient->bills;
    $totalBilled = $bills->sum('total_amount');
    $totalPaid = $bills->sum('amount_paid');
    $totalUnpaid = $totalBilled - $totalPaid;
    $unpaidBills = $bills->filter(fn($b) => $b->remaining_balance > 0);
  @endphp
  
  <div class="info-card" style="margin-bottom: 20px; border-left: 4px solid {{ $unpaidBills->count() > 0 ? '#ffc107' : '#28a745' }};">
    <div class="info-card-title">💰 Billing Information</div>
    
    @if($bills->isEmpty())
      <div style="text-align: center; padding: 16px; color: var(--muted); font-size: 13px;">
        No bills created for this patient yet.
      </div>
    @else
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
        <div style="background: var(--off-white); padding: 12px; border-radius: 8px;">
          <div style="font-size: 10px; color: var(--muted); text-transform: uppercase; font-weight: 500; margin-bottom: 4px;">Total Billed</div>
          <div style="font-size: 18px; font-weight: 700; color: var(--navy);">₱{{ number_format($totalBilled, 2) }}</div>
        </div>
        <div style="background: var(--off-white); padding: 12px; border-radius: 8px;">
          <div style="font-size: 10px; color: var(--muted); text-transform: uppercase; font-weight: 500; margin-bottom: 4px;">Paid</div>
          <div style="font-size: 18px; font-weight: 700; color: #28a745;">₱{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div style="background: {{ $totalUnpaid > 0 ? '#fff3cd' : '#d4edda' }}; padding: 12px; border-radius: 8px;">
          <div style="font-size: 10px; color: var(--muted); text-transform: uppercase; font-weight: 500; margin-bottom: 4px;">Remaining</div>
          <div style="font-size: 18px; font-weight: 700; color: {{ $totalUnpaid > 0 ? '#c88000' : '#28a745' }};">₱{{ number_format($totalUnpaid, 2) }}</div>
        </div>
      </div>

      @if($unpaidBills->count() > 0)
      <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px; color: #856404; font-size: 12px; font-weight: 500;">
          <span>⚠️</span>
          <span><strong>{{ $unpaidBills->count() }} unpaid bill(s) totaling ₱{{ number_format($totalUnpaid, 2) }}</strong></span>
        </div>
        <div style="font-size: 11px; color: #856404; margin-top: 6px;">
          Patient cannot be discharged until all bills are settled.
        </div>
      </div>
      @else
      <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px; color: #155724; font-size: 12px; font-weight: 500;">
          <span>✓</span>
          <span><strong>All bills paid</strong> — Patient can be discharged</span>
        </div>
      </div>
      @endif

      <div style="font-size: 12px;">
        <div style="font-weight: 500; color: var(--text); margin-bottom: 8px;">Bill Details:</div>
        <div style="max-height: 200px; overflow-y: auto;">
          @foreach($bills as $bill)
          <div style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 10px; margin-bottom: 8px; font-size: 11px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
              <span style="font-weight: 600; color: var(--text);">{{ $bill->service_type }}</span>
              <span style="padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 500; background: {{ $bill->status === 'paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $bill->status === 'paid' ? '#155724' : '#856404' }};">
                {{ ucfirst($bill->status) }}
              </span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; font-size: 10px; color: var(--muted);">
              <div>Total: <strong style="color: var(--text);">₱{{ number_format($bill->total_amount, 2) }}</strong></div>
              <div>Paid: <strong style="color: var(--text);">₱{{ number_format($bill->amount_paid, 2) }}</strong></div>
              <div>Remaining: <strong style="color: {{ $bill->remaining_balance > 0 ? 'var(--error)' : '#28a745' }};">₱{{ number_format($bill->remaining_balance, 2) }}</strong></div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border);">
        <a href="{{ route('billing.index') }}" style="display: inline-block; padding: 8px 16px; background: var(--sky); color: white; text-decoration: none; border-radius: 8px; font-size: 12px; font-weight: 500;">
          View Full Billing Details →
        </a>
      </div>
    @endif
  </div>

  <div class="links-card">
    <div class="links-title">Patient Records</div>
    <div class="link-row">
      <span class="link-label">Appointments</span>
      <a href="{{ route('appointments.index') }}" class="link-action">View all →</a>
    </div>
    <div class="link-row">
      <span class="link-label">Treatments & Diagnoses</span>
      <a href="{{ route('treatments.index') }}" class="link-action">View all →</a>
    </div>
    <div class="link-row">
      <span class="link-label">Treatment History</span>
      <a href="{{ route('history.index', ['patient_id' => $patient->id]) }}" class="link-action">View history →</a>
    </div>
  </div>

</div>
@endsection