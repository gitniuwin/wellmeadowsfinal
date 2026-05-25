@extends('layouts.app')

@section('page-title', 'Treatments')

@section('topbar-action')
  <button class="add-btn" id="openTreatmentModal" type="button">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Record Treatment
  </button>
@endsection

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-pale:#D6EAFA; --off-white:#F4F8FC;
    --muted:#6B7E9F; --border:#C8D9EE; --text:#1a2640;
    --error:#D94F4F; --success:#2E8B6A;
  }

  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:opacity 0.15s; }
  .add-btn:hover { opacity:0.9; }
  .add-btn svg { width:14px; height:14px; flex-shrink:0; }

  .treatment-wrap { padding:28px; }
  .stat-grid-treatment { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; margin-bottom:24px; }
  .treatment-stat { background:var(--navy); border-radius:12px; padding:18px 20px; color:white; }
  .treatment-stat.sky { background:var(--sky); }
  .treatment-stat.green { background:var(--success); }
  .treatment-stat-value { font-size:28px; font-weight:700; line-height:1; margin-bottom:6px; }
  .treatment-stat-label { font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:rgba(255,255,255,0.72); }

  .table-card { background:white; border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .table-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:16px; }
  .table-title { font-size:14px; font-weight:600; color:var(--navy); }
  .search-wrap { position:relative; flex-shrink:0; }
  .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--muted); fill:none; pointer-events:none; }
  .search-input { padding:8px 12px 8px 32px; border:1px solid var(--border); border-radius:8px; font-size:13px; background:var(--off-white); color:var(--text); outline:none; width:220px; }
  .search-input:focus { border-color:var(--sky); background:white; }

  .treatment-table { width:100%; border-collapse:collapse; }
  .treatment-table th { padding:10px 16px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); font-weight:600; background:var(--off-white); border-bottom:1px solid var(--border); }
  .treatment-table td { padding:12px 16px; font-size:13px; color:var(--text); vertical-align:middle; border-bottom:1px solid var(--border); }
  .treatment-table tr:last-child td { border-bottom:none; }
  .treatment-table tbody tr:hover { background:var(--sky-pale); }
  .patient-name { font-weight:600; color:var(--navy-dark); }
  .badge-procedure { display:inline-flex; max-width:240px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:var(--sky-pale); color:var(--navy); white-space:normal; }
  .actions-cell { display:flex; gap:6px; align-items:center; }
  .action-btn { padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; border:1px solid; text-decoration:none; display:inline-flex; align-items:center; transition:all 0.15s; background:transparent; }
  .btn-view { border-color:var(--sky); color:var(--sky); }
  .btn-view:hover { background:var(--sky-pale); }
  .btn-edit { border-color:var(--navy); color:var(--navy); }
  .btn-edit:hover { background:var(--sky-pale); }
  .btn-delete { border-color:var(--error); color:var(--error); }
  .btn-delete:hover { background:#fdf0f0; }
  .empty-state { text-align:center; padding:56px 20px; color:var(--muted); }
  .empty-state svg { width:44px; height:44px; stroke:var(--border); fill:none; margin:0 auto 12px; }

  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,18,40,0.52); z-index:200; align-items:center; justify-content:center; padding:20px; }
  .modal-overlay.open { display:flex; }
  .modal { background:white; border-radius:14px; width:100%; max-width:560px; max-height:90vh; overflow:auto; box-shadow:0 20px 50px rgba(17,30,63,0.22); }
  .modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px 16px; border-bottom:1px solid var(--border); }
  .modal-title { font-size:18px; font-weight:700; color:var(--navy); }
  .modal-close { width:30px; height:30px; border-radius:8px; background:var(--off-white); border:1px solid var(--border); cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .modal-close svg { width:14px; height:14px; stroke:var(--muted); fill:none; }
  .modal-form { padding:22px 24px 24px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  .form-label { font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  .form-input, .form-select, .form-textarea { width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  .form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--sky); background:white; }
  .form-textarea { resize:vertical; min-height:86px; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
  .btn-cancel { padding:9px 18px; border:1px solid var(--border); border-radius:8px; background:white; font-size:13px; cursor:pointer; color:var(--muted); }
  .btn-submit { padding:9px 18px; border:none; border-radius:8px; background:var(--navy); font-size:13px; cursor:pointer; color:white; font-weight:500; }
  .btn-submit:hover { background:var(--navy-light); }

  @media (max-width: 760px) {
    .treatment-wrap { padding:18px 0; }
    .stat-grid-treatment { grid-template-columns:1fr; }
    .table-header { align-items:flex-start; flex-direction:column; }
    .search-input { width:100%; }
    .search-wrap { width:100%; }
    .form-grid { grid-template-columns:1fr; }
  }
</style>
@endpush

@section('content')
<div class="treatment-wrap">
  <div class="stat-grid-treatment">
    <div class="treatment-stat">
      <div class="treatment-stat-value">{{ $totalTreatments ?? 0 }}</div>
      <div class="treatment-stat-label">Total Treatments</div>
    </div>
    <div class="treatment-stat sky">
      <div class="treatment-stat-value">{{ $activeDiagnoses ?? 0 }}</div>
      <div class="treatment-stat-label">Active Diagnoses</div>
    </div>
    <div class="treatment-stat green">
      <div class="treatment-stat-value">{{ $proceduresToday ?? 0 }}</div>
      <div class="treatment-stat-label">Procedures Today</div>
    </div>
  </div>

  <div class="table-card">
    <div class="table-header">
      <span class="table-title">Treatment Records</span>
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="search-input" type="text" id="treatmentSearch" placeholder="Search patient..." oninput="filterTreatments()">
      </div>
    </div>

    <table class="treatment-table" id="treatmentTable">
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
            <td><div class="patient-name">{{ $treatment->patient->full_name ?? '' }}</div></td>
            <td>{{ $treatment->diagnosis }}</td>
            <td><span class="badge-procedure">{{ $treatment->procedure }}</span></td>
            <td>{{ $treatment->doctor->full_name ?? '' }}</td>
            <td style="color:var(--muted);font-size:12px;">{{ \Carbon\Carbon::parse($treatment->treatment_date)->format('M d, Y') }}</td>
            <td>
              <div class="actions-cell">
                <a href="{{ route('treatments.show', $treatment->id) }}" class="action-btn btn-view">View</a>
                <a href="{{ route('treatments.edit', $treatment->id) }}" class="action-btn btn-edit">Edit</a>
                <form method="POST" action="{{ route('treatments.destroy', $treatment->id) }}" style="display:inline" onsubmit="return confirm('Delete this treatment record?')">
                  @csrf
                  @method('DELETE')
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
                <p>No treatment records found.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="addTreatmentModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Record Treatment</div>
      <button class="modal-close" id="closeTreatmentModal" type="button">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form action="{{ route('treatments.store') }}" method="POST" class="modal-form">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Patient</label>
          <select name="patient_id" required class="form-select">
            <option value="">Select patient...</option>
            @foreach($patients ?? [] as $patient)
              <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Doctor</label>
          <select name="doctor_id" required class="form-select">
            <option value="">Select doctor...</option>
            @foreach($doctors ?? [] as $doctor)
              <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group full">
          <label class="form-label">Diagnosis</label>
          <input type="text" name="diagnosis" required placeholder="Diagnosis" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Procedure</label>
          <input type="text" name="procedure" required placeholder="Procedure" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Treatment Date</label>
          <input type="date" name="treatment_date" required class="form-input">
        </div>
        <div class="form-group full">
          <label class="form-label">Notes</label>
          <textarea name="notes" rows="3" placeholder="Treatment notes..." class="form-textarea"></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" id="cancelTreatmentModal">Cancel</button>
        <button type="submit" class="btn-submit">Save Treatment</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function filterTreatments() {
    const searchValue = document.getElementById('treatmentSearch')?.value.toLowerCase() || '';
    document.querySelectorAll('#treatmentTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addTreatmentModal');
    const openButton = document.getElementById('openTreatmentModal');
    const closeButton = document.getElementById('closeTreatmentModal');
    const cancelButton = document.getElementById('cancelTreatmentModal');

    openButton?.addEventListener('click', () => modal?.classList.add('open'));
    closeButton?.addEventListener('click', () => modal?.classList.remove('open'));
    cancelButton?.addEventListener('click', () => modal?.classList.remove('open'));
    modal?.addEventListener('click', event => {
      if (event.target === modal) {
        modal.classList.remove('open');
      }
    });
  });
</script>
@endpush
