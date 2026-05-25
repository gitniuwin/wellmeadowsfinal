@extends('layouts.app')
@section('page-title', 'Patient Management')

@section('topbar-action')
  <button class="add-btn" onclick="openModal('addModal')">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Register Patient
  </button>
@endsection

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-light:#A8CBF0; --sky-pale:#D6EAFA;
    --white:#FFFFFF; --off-white:#F4F8FC; --muted:#6B7E9F;
    --border:#C8D9EE; --text:#1a2640; --sidebar-w:210px;
    --error:#D94F4F; --success:#2E8B6A; --warn:#C88000;
  }

  /* CONTENT */
  .content { padding:28px; flex:1; }
  .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
  .stat-card { background:white; border:1px solid var(--border); border-radius:12px; padding:20px; }
  .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); font-weight:500; }
  .stat-value { font-family:'Playfair Display',serif; font-size:28px; color:var(--navy); margin-top:4px; }
  .stat-sub { font-size:11px; color:var(--muted); margin-top:2px; }

  /* FILTERS */
  .filters-bar { background:white; border:1px solid var(--border); border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  .search-wrap { position:relative; flex:1; min-width:200px; }
  .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--muted); fill:none; }
  .search-input { width:100%; padding:8px 12px 8px 32px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; background:var(--off-white); color:var(--text); outline:none; }
  .search-input:focus { border-color:var(--sky); }
  .filter-select { padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; background:var(--off-white); color:var(--text); outline:none; cursor:pointer; }
  .filter-select:focus { border-color:var(--sky); }
  .filter-btn { padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; }

  /* TABLE */
  .table-card { background:white; border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .table-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
  .table-title { font-size:14px; font-weight:600; color:var(--navy); }
  .table-count { font-size:12px; color:var(--muted); }
  table { width:100%; border-collapse:collapse; }
  thead th { padding:10px 16px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); font-weight:500; background:var(--off-white); border-bottom:1px solid var(--border); }
  tbody tr { border-bottom:1px solid var(--border); transition:background 0.1s; }
  tbody tr:last-child { border-bottom:none; }
  tbody tr:hover { background:var(--sky-pale); }
  td { padding:12px 16px; font-size:13px; color:var(--text); vertical-align:middle; }
  .patient-name { font-weight:500; color:var(--navy-dark); }
  .patient-num { font-size:11px; color:var(--muted); margin-top:1px; }
  .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
  .badge-admitted { background:#DFFBEF; color:#1a7a50; }
  .badge-outpatient { background:#FFF4E0; color:#a06000; }
  .badge-male { background:#EBF3FF; color:#1a4a8a; }
  .badge-female { background:#FDE8F5; color:#8a1a5a; }
  .badge-other { background:#F0F0F0; color:#555; }
  .action-btn { padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; border:1px solid; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:all 0.15s; }
  .btn-view  { border-color:var(--sky); color:var(--sky); background:transparent; }
  .btn-view:hover  { background:var(--sky-pale); }
  .btn-edit  { border-color:var(--navy); color:var(--navy); background:transparent; }
  .btn-edit:hover  { background:var(--sky-pale); }
  .btn-discharge { border-color:var(--warn); color:var(--warn); background:transparent; }
  .btn-discharge:hover { background:#fff8e6; }
  .btn-delete { border-color:var(--error); color:var(--error); background:transparent; }
  .btn-delete:hover { background:#fdf0f0; }
  .actions-cell { display:flex; gap:6px; flex-wrap:wrap; }

  /* ADD BUTTON */
  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.15s; text-decoration:none; }
  .add-btn:hover { background:var(--navy-light); }
  .add-btn svg { width:14px; height:14px; fill:none; stroke:white; stroke-width:2; stroke-linecap:round; }

  /* EMPTY */
  .empty-state { text-align:center; padding:60px 20px; }
  .empty-state svg { width:48px; height:48px; stroke:var(--border); fill:none; margin-bottom:12px; }
  .empty-state p { color:var(--muted); font-size:14px; }

  /* ALERT */
  .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; }
  .alert-success { background:#DFFBEF; color:#1a7a50; border:1px solid #b2ecd4; }
  .alert-error   { background:#fdf0f0; color:#a03030; border:1px solid #f5c0c0; }

  /* PAGINATION */
  .pagination { padding:12px 20px; display:flex; justify-content:flex-end; border-top:1px solid var(--border); font-size:12px; }
  .pagination nav { width:100%; }
  .pagination svg { width:16px; height:16px; }
  .pagination a, .pagination span { font-size:12px; line-height:1.2; }
  .checkbox-row { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text); }
  .checkbox-row input { width:16px; height:16px; padding:0; flex:0 0 16px; }

  /* MODAL */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal { background:white; border-radius:16px; padding:28px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; }
  .modal-title { font-family:'Playfair Display',serif; font-size:18px; color:var(--navy); margin-bottom:20px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:1/-1; }
  label { font-size:12px; font-weight:500; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; }
  input, select, textarea { padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); background:var(--off-white); outline:none; }
  input:focus, select:focus, textarea:focus { border-color:var(--sky); background:white; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
  .btn-cancel { padding:9px 18px; border:1px solid var(--border); border-radius:8px; background:white; font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:var(--muted); }
  .btn-submit { padding:9px 18px; border:none; border-radius:8px; background:var(--navy); font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:white; font-weight:500; }
</style>
@endpush

@section('content')
<div style="padding:28px;">

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-label">Total Patients</div>
        <div class="stat-value">{{ $totalPatients }}</div>
        <div class="stat-sub">All registered patients</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Admitted</div>
        <div class="stat-value" style="color:var(--success)">{{ $admitted }}</div>
        <div class="stat-sub">Currently in-ward</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Outpatients</div>
        <div class="stat-value" style="color:var(--warn)">{{ $outpatients }}</div>
        <div class="stat-sub">Not admitted</div>
      </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('patients.index') }}" class="filters-bar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input class="search-input" type="text" name="search" placeholder="Search by name or patient number…" value="{{ request('search') }}">
      </div>
      <select class="filter-select" name="status">
        <option value="">All Status</option>
        <option value="admitted"   {{ request('status') === 'admitted'   ? 'selected' : '' }}>Admitted</option>
        <option value="outpatient" {{ request('status') === 'outpatient' ? 'selected' : '' }}>Outpatient</option>
      </select>
      <button type="submit" class="filter-btn">Filter</button>
      @if(request('search') || request('status'))
        <a href="{{ route('patients.index') }}" class="filter-btn" style="background:var(--muted); text-decoration:none;">Clear</a>
      @endif
    </form>

    <!-- Table -->
    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Patient Records</span>
        <span class="table-count">{{ $patients->total() }} patient(s)</span>
      </div>
      @if($patients->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <p>No patients found. Register the first patient using the button above.</p>
        </div>
      @else
        <table>
          <thead>
            <tr>
              <th>Patient</th>
              <th>Patient No.</th>
              <th>Gender</th>
              <th>Date of Birth</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($patients as $patient)
            <tr>
              <td>
                <div class="patient-name">{{ $patient->full_name }}</div>
              </td>
              <td><span class="patient-num">{{ $patient->patient_number }}</span></td>
              <td>
                <span class="badge {{ $patient->gender === 'Male' ? 'badge-male' : ($patient->gender === 'Female' ? 'badge-female' : 'badge-other') }}">
                  {{ $patient->gender }}
                </span>
              </td>
              <td>{{ $patient->date_of_birth->format('d M Y') }}<br><small style="color:var(--muted)">Age {{ $patient->date_of_birth->age }}</small></td>
              <td>{{ $patient->contact_number ?? '—' }}</td>
              <td>
                <span class="badge {{ $patient->is_admitted ? 'badge-admitted' : 'badge-outpatient' }}">
                  {{ $patient->is_admitted ? 'Admitted' : 'Outpatient' }}
                </span>
              </td>
              <td>
                <div class="actions-cell">
                  <a href="{{ route('patients.show', $patient) }}" class="action-btn btn-view">View</a>
                  <a href="{{ route('patients.edit', $patient) }}" class="action-btn btn-edit">Edit</a>
                  @if($patient->is_admitted)
                  <form method="POST" action="{{ route('patients.discharge', $patient) }}" style="display:inline" onsubmit="return confirm('Discharge {{ $patient->full_name }}?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="action-btn btn-discharge">Discharge</button>
                  </form>
                  @endif
                  <form method="POST" action="{{ route('patients.destroy', $patient) }}" style="display:inline" onsubmit="return confirm('Delete this patient record? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn btn-delete">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="pagination">{{ $patients->links('pagination::simple-bootstrap-4') }}</div>
      @endif
    </div>

</div>

<!-- REGISTER PATIENT MODAL -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-title">Register New Patient</div>
    <form method="POST" action="{{ route('patients.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Juan">
          @error('first_name')<small style="color:var(--error)">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Dela Cruz">
          @error('last_name')<small style="color:var(--error)">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Date of Birth *</label>
          <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
          @error('date_of_birth')<small style="color:var(--error)">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="">Select…</option>
            <option value="Male"   {{ old('gender') === 'Male'   ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other"  {{ old('gender') === 'Other'  ? 'selected' : '' }}>Other</option>
          </select>
          @error('gender')<small style="color:var(--error)">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 09XX-XXX-XXXX">
        </div>
        <div class="form-group">
          <label>Admitted?</label>
          <div class="checkbox-row" style="margin-top:8px">
            <input type="checkbox" name="is_admitted" value="1" {{ old('is_admitted') ? 'checked' : '' }}>
            <span>Check if patient is being admitted</span>
          </div>
        </div>
        <div class="form-group full">
          <label>Address</label>
          <textarea name="address" rows="2" placeholder="Street, Barangay, City…">{{ old('address') }}</textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn-submit">Register Patient</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function openModal(id)  { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });
  @if($errors->any())
    openModal('addModal');
  @endif
</script>
@endpush
