@extends('layouts.app')
@section('page-title', 'Treatments')

@section('topbar-action')
  <button class="add-btn" onclick="document.getElementById('addTreatmentModal').classList.add('open')">
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
    --text:#1a2640; --error:#D94F4F; --success:#2E8B6A; --warn:#C88000;
  }
  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.15s; }
  .add-btn:hover { background:var(--navy-light); }
  .add-btn svg { width:14px; height:14px; }
  .page-wrap { padding:28px; }

  /* STAT CARDS */
  .stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px; }
  .stat-card { background:var(--navy); border-radius:14px; padding:20px 22px; color:white; }
  .stat-card .stat-icon { width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
  .stat-card .stat-icon svg { width:18px; height:18px; stroke:var(--sky-light); fill:none; }
  .stat-card .stat-value { font-family:'Playfair Display',serif; font-size:30px; font-weight:600; line-height:1; margin-bottom:4px; }
  .stat-card .stat-label { font-size:11px; color:rgba(255,255,255,0.55); }

  /* TABLE */
  .table-card { background:white; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
  .table-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
  .table-title { font-size:14px; font-weight:600; color:var(--navy); }
  .search-wrap { position:relative; }
  .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--muted); fill:none; pointer-events:none; }
  .search-input { padding:8px 12px 8px 32px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; background:var(--off-white); color:var(--text); outline:none; width:220px; }
  .search-input:focus { border-color:var(--sky); }
  table { width:100%; border-collapse:collapse; }
  thead th { padding:10px 16px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); font-weight:500; background:var(--off-white); border-bottom:1px solid var(--border); }
  tbody tr { border-bottom:1px solid var(--border); transition:background 0.1s; }
  tbody tr:last-child { border-bottom:none; }
  tbody tr:hover { background:var(--sky-pale); }
  td { padding:12px 16px; font-size:13px; color:var(--text); vertical-align:middle; }
  .patient-name { font-weight:500; color:var(--navy-dark); }
  .badge-procedure { background:var(--sky-pale); color:var(--navy); display:inline-flex; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
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
  .alert-error   { background:#fdf0f0; color:#a03030; border:1px solid #f5c0c0; }

  /* MODAL */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal { background:white; border-radius:16px; padding:28px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; }
  .modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .modal-title { font-family:'Playfair Display',serif; font-size:18px; color:var(--navy); }
  .modal-close { width:30px; height:30px; border-radius:8px; background:var(--off-white); border:1px solid var(--border); cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .modal-close svg { width:14px; height:14px; stroke:var(--muted); fill:none; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  label { font-size:12px; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  input, select, textarea { padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  input:focus, select:focus, textarea:focus { border-color:var(--sky); background:white; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
  .btn-cancel { padding:9px 18px; border:1px solid var(--border); border-radius:8px; background:white; font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:var(--muted); }
  .btn-submit { padding:9px 18px; border:none; border-radius:8px; background:var(--navy); font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:white; font-weight:500; }
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
      <div class="stat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
      <div class="stat-value">{{ $totalTreatments ?? 0 }}</div>
      <div class="stat-label">Total Treatments</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
      <div class="stat-value">{{ $activeDiagnoses ?? 0 }}</div>
      <div class="stat-label">Active Diagnoses</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg></div>
      <div class="stat-value">{{ $proceduresToday ?? 0 }}</div>
      <div class="stat-label">Procedures Today</div>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <div class="table-header">
      <span class="table-title">Treatment Records</span>
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="search-input" type="text" id="treatSearch" placeholder="Search patient…" oninput="filterTreatments()">
      </div>
    </div>
    <table id="treatTable">
      <thead>
        <tr>
          <th>Patient</th>
          <th>Diagnosis</th>
          <th>Procedure</th>
          <th>Attending Doctor</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($treatments ?? [] as $treatment)
        <tr>
          <td><div class="patient-name">{{ $treatment->patient->full_name ?? $treatment->patient->name }}</div></td>
          <td>{{ $treatment->diagnosis }}</td>
          <td><span class="badge-procedure">{{ $treatment->procedure }}</span></td>
          <td>{{ $treatment->doctor->full_name ?? $treatment->doctor->name }}</td>
          <td style="color:var(--muted); font-size:12px;">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}</td>
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
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <p>No treatment records yet.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

{{-- ADD TREATMENT MODAL --}}
<div class="modal-overlay" id="addTreatmentModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Record Treatment</div>
      <button class="modal-close" onclick="document.getElementById('addTreatmentModal').classList.remove('open')">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('treatments.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Patient</label>
          <select name="patient_id" required>
            <option value="">Select patient…</option>
            @foreach($patients ?? [] as $patient)
              <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Doctor</label>
          <select name="doctor_id" required>
            <option value="">Select doctor…</option>
            @foreach($doctors ?? [] as $doctor)
              <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
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
        <button type="button" class="btn-cancel" onclick="document.getElementById('addTreatmentModal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn-submit">Save Treatment</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });
  function filterTreatments() {
    const search = document.getElementById('treatSearch').value.toLowerCase();
    document.querySelectorAll('#treatTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
  }
  @if($errors->any()) document.getElementById('addTreatmentModal').classList.add('open'); @endif
</script>
@endpush