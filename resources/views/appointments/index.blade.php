@extends('layouts.app')
@section('page-title', 'Appointments')

@section('topbar-action')
  <button class="add-btn" id="openApptBtn">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Appointment
  </button>
  <button class="add-btn" id="openTreatBtn" style="background:var(--sky); margin-left:8px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Record Treatment
  </button>
@endsection

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-light:#A8CBF0; --sky-pale:#D6EAFA;
    --off-white:#F4F8FC; --muted:#6B7E9F; --border:#C8D9EE;
    --text:#1a2640; --error:#D94F4F; --success:#2E8B6A;
  }
  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.15s; }
  .add-btn:hover { opacity:0.9; }
  .add-btn svg { width:14px; height:14px; }
  .page-wrap { padding:28px; }
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
  .stat-card { background:var(--navy); border-radius:14px; padding:20px 22px; color:white; }
  .stat-card .stat-value { font-size:28px; font-weight:700; line-height:1; margin-bottom:4px; }
  .stat-card .stat-label { font-size:11px; color:rgba(255,255,255,0.55); }
  .tabs { display:flex; gap:0; margin-bottom:0; border-bottom:2px solid var(--border); }
  .tab-btn { padding:10px 22px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:none; color:var(--muted); border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.15s; }
  .tab-btn.active { color:var(--navy); border-bottom-color:var(--navy); }
  .tab-content { display:none; }
  .tab-content.active { display:block; }
  .table-card { background:white; border:1px solid var(--border); border-radius:0 0 14px 14px; overflow:hidden; }
  .table-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
  .table-title { font-size:14px; font-weight:600; color:var(--navy); }
  .search-wrap { position:relative; }
  .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--muted); fill:none; pointer-events:none; }
  .search-input { padding:8px 12px 8px 32px; border:1px solid var(--border); border-radius:8px; font-size:13px; background:var(--off-white); color:var(--text); outline:none; width:220px; }
  .search-input:focus { border-color:var(--sky); }
  table { width:100%; border-collapse:collapse; }
  thead th { padding:10px 16px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); font-weight:500; background:var(--off-white); border-bottom:1px solid var(--border); }
  tbody tr { border-bottom:1px solid var(--border); transition:background 0.1s; }
  tbody tr:last-child { border-bottom:none; }
  tbody tr:hover { background:var(--sky-pale); }
  td { padding:12px 16px; font-size:13px; color:var(--text); vertical-align:middle; }
  .patient-name { font-weight:500; color:var(--navy-dark); }
  .badge { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
  .badge-scheduled { background:#FFF3CD; color:#856404; }
  .badge-completed { background:#D1FAE5; color:#065F46; }
  .badge-cancelled { background:#FEE2E2; color:#991B1B; }
  .badge-procedure { background:var(--sky-pale); color:var(--navy); }
  .action-btn { padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; border:1px solid; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:all 0.15s; background:transparent; }
  .btn-view { border-color:var(--sky); color:var(--sky); }
  .btn-view:hover { background:var(--sky-pale); }
  .btn-edit { border-color:var(--navy); color:var(--navy); }
  .btn-edit:hover { background:var(--sky-pale); }
  .btn-delete { border-color:var(--error); color:var(--error); }
  .btn-delete:hover { background:#fdf0f0; }
  .actions-cell { display:flex; gap:6px; align-items:center; }
  .empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
  .empty-state svg { width:48px; height:48px; stroke:var(--border); fill:none; margin:0 auto 12px; display:block; }
  .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
  .alert-success { background:#DFFBEF; color:#1a7a50; border:1px solid #b2ecd4; }
  .alert-error { background:#fdf0f0; color:#a03030; border:1px solid #f5c0c0; }
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal { background:white; border-radius:16px; padding:28px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; }
  .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .modal-title { font-size:18px; font-weight:700; color:var(--navy); }
  .modal-close { width:30px; height:30px; border-radius:8px; background:var(--off-white); border:1px solid var(--border); cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .modal-close svg { width:14px; height:14px; stroke:var(--muted); fill:none; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  label { font-size:12px; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  input, select, textarea { padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  input:focus, select:focus, textarea:focus { border-color:var(--sky); background:white; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
  .btn-cancel { padding:9px 18px; border:1px solid var(--border); border-radius:8px; background:white; font-size:13px; cursor:pointer; color:var(--muted); }
  .btn-submit { padding:9px 18px; border:none; border-radius:8px; background:var(--navy); font-size:13px; cursor:pointer; color:white; font-weight:500; }
  .btn-submit:hover { background:var(--navy-light); }
</style>
@endpush

@section('content')
<div class="page-wrap">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  {{-- STAT CARDS --}}
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-value">{{ $totalAppointments ?? 0 }}</div>
      <div class="stat-label">Total Appointments</div>
    </div>
    <div class="stat-card" style="background:var(--sky);">
      <div class="stat-value">{{ $scheduled ?? 0 }}</div>
      <div class="stat-label">Scheduled</div>
    </div>
    <div class="stat-card" style="background:var(--success);">
      <div class="stat-value">{{ $completed ?? 0 }}</div>
      <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card" style="background:var(--navy-light);">
      <div class="stat-value">{{ $totalTreatments ?? 0 }}</div>
      <div class="stat-label">Total Treatments</div>
    </div>
  </div>

  {{-- TABS --}}
  <div class="tabs">
    <button class="tab-btn active" onclick="switchTab('appointments', this)">Appointments</button>
    <button class="tab-btn" onclick="switchTab('treatments', this)">Treatment Records</button>
  </div>

  {{-- APPOINTMENTS TAB --}}
  <div class="tab-content active" id="tab-appointments">
    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Appointment Records</span>
        <div class="search-wrap">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input class="search-input" type="text" id="apptSearch" placeholder="Search patient…" oninput="filterTable('apptSearch','apptTable')">
        </div>
      </div>
      <table id="apptTable">
        <thead>
          <tr>
            <th>Patient</th><th>Doctor</th><th>Type</th><th>Date</th><th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($appointments ?? [] as $appt)
          <tr>
            <td><div class="patient-name">{{ $appt->patient->first_name ?? '' }} {{ $appt->patient->last_name ?? '' }}</div></td>
            <td>{{ $appt->doctor->first_name ?? '' }} {{ $appt->doctor->last_name ?? '' }}</td>
            <td><span class="badge badge-procedure">{{ $appt->type }}</span></td>
            <td style="color:var(--muted);font-size:12px;">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y h:i A') }}</td>
            <td><span class="badge badge-{{ $appt->status }}">{{ ucfirst($appt->status) }}</span></td>
            <td>
              <div class="actions-cell">
                <a href="{{ route('appointments.show', $appt->id) }}" class="action-btn btn-view">View</a>
                <a href="{{ route('appointments.edit', $appt->id) }}" class="action-btn btn-edit">Edit</a>
                <form method="POST" action="{{ route('appointments.destroy', $appt->id) }}" style="display:inline" onsubmit="return confirm('Delete this appointment?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn btn-delete">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6">
            <div class="empty-state">
              <svg viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <p>No appointments yet.</p>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- TREATMENTS TAB --}}
  <div class="tab-content" id="tab-treatments">
    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Treatment Records</span>
        <div class="search-wrap">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input class="search-input" type="text" id="treatSearch" placeholder="Search patient…" oninput="filterTable('treatSearch','treatTable')">
        </div>
      </div>
      <table id="treatTable">
        <thead>
          <tr>
            <th>Patient</th><th>Diagnosis</th><th>Procedure</th><th>Attending Doctor</th><th>Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($treatments ?? [] as $treatment)
          <tr>
            <td><div class="patient-name">{{ $treatment->patient->first_name ?? '' }} {{ $treatment->patient->last_name ?? '' }}</div></td>
            <td>{{ $treatment->diagnosis }}</td>
            <td><span class="badge badge-procedure">{{ $treatment->procedure }}</span></td>
            <td>{{ $treatment->doctor->first_name ?? '' }} {{ $treatment->doctor->last_name ?? '' }}</td>
            <td style="color:var(--muted);font-size:12px;">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}</td>
            <td>
              <div class="actions-cell">
                <a href="{{ route('treatments.show', $treatment->id) }}" class="action-btn btn-view">View</a>
                <a href="{{ route('treatments.edit', $treatment->id) }}" class="action-btn btn-edit">Edit</a>
                <form method="POST" action="{{ route('treatments.destroy', $treatment->id) }}" style="display:inline" onsubmit="return confirm('Delete this treatment record?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn btn-delete">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6">
            <div class="empty-state">
              <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <p>No treatment records yet.</p>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- ADD APPOINTMENT MODAL --}}
<div class="modal-overlay" id="addApptModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">New Appointment</div>
      <button class="modal-close" id="closeApptModal"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form method="POST" action="{{ route('appointments.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Patient</label>
          <select name="patient_id" required>
            <option value="">Select patient…</option>
            @foreach($patients ?? [] as $patient)
              <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Doctor</label>
          <select name="doctor_id" required>
            <option value="">Select doctor…</option>
            @foreach($doctors ?? [] as $doctor)
              <option value="{{ $doctor->id }}">{{ $doctor->first_name }} {{ $doctor->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Type</label>
          <select name="type" required>
            <option value="">Select type…</option>
            <option value="Consultation">Consultation</option>
            <option value="Follow-up">Follow-up</option>
            <option value="Emergency">Emergency</option>
            <option value="Routine Check">Routine Check</option>
          </select>
        </div>
        <div class="form-group">
          <label>Appointment Date</label>
          <input type="datetime-local" name="appointment_date" required>
        </div>
        <div class="form-group full">
          <label>Notes</label>
          <textarea name="notes" rows="3" placeholder="Appointment notes…"></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" id="cancelApptModal">Cancel</button>
        <button type="submit" class="btn-submit">Save Appointment</button>
      </div>
    </form>
  </div>
</div>

{{-- ADD TREATMENT MODAL --}}
<div class="modal-overlay" id="addTreatmentModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Record Treatment</div>
      <button class="modal-close" id="closeTreatModal"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <form method="POST" action="{{ route('treatments.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Patient</label>
          <select name="patient_id" required>
            <option value="">Select patient…</option>
            @foreach($patients ?? [] as $patient)
              <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Doctor</label>
          <select name="doctor_id" required>
            <option value="">Select doctor…</option>
            @foreach($doctors ?? [] as $doctor)
              <option value="{{ $doctor->id }}">{{ $doctor->first_name }} {{ $doctor->last_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group full">
          <label>Diagnosis</label>
          <input type="text" name="diagnosis" required placeholder="e.g. Hypertension Stage 2">
        </div>
        <div class="form-group">
          <label>Procedure</label>
          <input type="text" name="procedure" required placeholder="e.g. Blood pressure monitoring">
        </div>
        <div class="form-group">
          <label>Treatment Date</label>
          <input type="date" name="treatment_date" required>
        </div>
        <div class="form-group full">
          <label>Notes</label>
          <textarea name="notes" rows="3" placeholder="Treatment notes…"></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" id="cancelTreatModal">Cancel</button>
        <button type="submit" class="btn-submit">Save Treatment</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // Tab switching
  function switchTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
  }

  // Search filter
  function filterTable(inputId, tableId) {
    const q = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  // Modal controls
  document.getElementById('openApptBtn')?.addEventListener('click', () => document.getElementById('addApptModal').classList.add('open'));
  document.getElementById('closeApptModal')?.addEventListener('click', () => document.getElementById('addApptModal').classList.remove('open'));
  document.getElementById('cancelApptModal')?.addEventListener('click', () => document.getElementById('addApptModal').classList.remove('open'));

  document.getElementById('openTreatBtn')?.addEventListener('click', () => {
    document.getElementById('addTreatmentModal').classList.add('open');
    switchTab('treatments', document.querySelectorAll('.tab-btn')[1]);
  });
  document.getElementById('closeTreatModal')?.addEventListener('click', () => document.getElementById('addTreatmentModal').classList.remove('open'));
  document.getElementById('cancelTreatModal')?.addEventListener('click', () => document.getElementById('addTreatmentModal').classList.remove('open'));

  // Close on backdrop click
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });
</script>
@endpush
