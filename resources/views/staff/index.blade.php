@extends('layouts.app')
@section('page-title', 'Staff & Department Management')

@section('topbar-action')
  @if(auth()->user()->role !== 'Charge Nurse')
  <button class="add-btn" onclick="openModal('add')">
    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Staff
  </button>
  @endif
@endsection

@push('styles')
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-light:#A8CBF0; --sky-pale:#D6EAFA;
    --white:#FFFFFF; --off-white:#F4F8FC; --muted:#6B7E9F;
    --border:#C8D9EE; --text:#1a2640; --sidebar-w:210px;
    --error:#D94F4F; --success:#2E8B6A; --warn:#C88000;
  }
  body { font-family:'DM Sans',sans-serif; background:var(--off-white); color:var(--text); display:flex; min-height:100vh; }

  /* SIDEBAR */
  .sidebar { width:var(--sidebar-w); background:var(--navy); display:flex; flex-direction:column; position:fixed; top:0; left:0; bottom:0; z-index:100; }
  .sidebar-logo { padding:22px 20px 18px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; gap:10px; }
  .logo-icon-sm { width:34px; height:34px; background:var(--sky); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .logo-icon-sm svg { width:18px; height:18px; fill:white; }
  .logo-text { font-family:'Playfair Display',serif; font-size:13px; color:white; line-height:1.2; }
  .logo-text span { display:block; font-family:'DM Sans',sans-serif; font-size:9px; color:var(--sky-light); letter-spacing:1px; text-transform:uppercase; font-weight:400; }
  .nav-section { padding:16px 12px 8px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.3); font-weight:500; }
  .nav-item { display:flex; align-items:center; gap:10px; padding:10px 16px; margin:1px 8px; border-radius:8px; cursor:pointer; color:rgba(255,255,255,0.55); font-size:13px; font-weight:400; transition:all 0.15s; text-decoration:none; }
  .nav-item:hover { background:rgba(255,255,255,0.08); color:white; }
  .nav-item.active { background:rgba(91,155,213,0.25); color:white; font-weight:500; }
  .nav-item svg { width:16px; height:16px; flex-shrink:0; opacity:0.8; }
  .nav-item.active svg { opacity:1; }
  .sidebar-footer { margin-top:auto; padding:16px 12px; border-top:1px solid rgba(255,255,255,0.08); }
  .avatar { width:32px; height:32px; border-radius:50%; background:var(--sky); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; color:white; flex-shrink:0; }
  .user-card { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; }
  .user-name { font-size:12px; font-weight:500; color:white; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .user-role { font-size:10px; color:var(--sky-light); }
  .sign-out-btn { width:100%; margin-top:6px; padding:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:7px; color:rgba(255,255,255,0.5); font-family:'DM Sans',sans-serif; font-size:12px; cursor:pointer; transition:all 0.15s; }
  .sign-out-btn:hover { background:rgba(255,255,255,0.12); color:white; }

  /* MAIN */
  .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }
  .topbar { background:white; border-bottom:1px solid var(--border); padding:0 28px; height:58px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:50; }
  .topbar-left h2 { font-family:'Playfair Display',serif; font-size:20px; color:var(--text); }
  .topbar-right { display:flex; align-items:center; gap:10px; }
  .date-badge { font-size:12px; color:var(--muted); background:var(--off-white); border:1px solid var(--border); border-radius:6px; padding:5px 12px; }
  .add-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--navy); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.15s; }
  .add-btn:hover { background:var(--navy-light); }
  .add-btn svg { width:14px; height:14px; fill:none; stroke:white; stroke-width:2; stroke-linecap:round; }

  /* CONTENT */
  .content { padding:24px 28px; flex:1; }
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
  .stat-card { background:var(--navy); border-radius:12px; padding:18px 20px; color:white; }
  .flow-panel { background:white; border:1px solid var(--border); border-radius:16px; padding:22px; margin-bottom:24px; box-shadow:0 10px 40px rgba(29,53,91,.05); }
  .flow-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .flow-header h3 { margin:0; font-size:16px; font-weight:600; color:var(--text); }
  .flow-header span { font-size:12px; color:var(--muted); }
  .flow-timeline { display:flex; flex-direction:column; gap:16px; }
  .flow-item { display:grid; grid-template-columns:92px 1fr; gap:14px; align-items:start; }
  .flow-time { font-size:13px; color:var(--sky); font-weight:700; }
  .flow-content { padding:16px 18px; border-radius:14px; background:var(--off-white); }
  .flow-title { font-size:14px; font-weight:600; margin-bottom:6px; }
  .flow-desc { font-size:13px; color:var(--muted); line-height:1.5; }
  .flow-badge { margin-top:8px; display:inline-flex; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:0.02em; }
  .flow-badge.Active { background:#E3F7EF; color:#1B7A54; }
  .flow-badge.Leave { background:#FFF3E0; color:#A06000; }
  .stat-card.light { background:white; border:1px solid var(--border); color:var(--text); }
  .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; opacity:0.65; margin-bottom:8px; }
  .stat-value { font-family:'Playfair Display',serif; font-size:30px; line-height:1; }
  .stat-sub { font-size:11px; opacity:0.55; margin-top:4px; }

  /* TABS */
  .tab-bar { display:flex; gap:0; border-bottom:1.5px solid var(--border); margin-bottom:20px; }
  .tab { padding:10px 18px; font-size:13px; font-weight:500; color:var(--muted); cursor:pointer; border-bottom:2.5px solid transparent; margin-bottom:-1.5px; transition:all 0.15s; white-space:nowrap; }
  .tab:hover { color:var(--navy); }
  .tab.active { color:var(--navy); border-bottom-color:var(--navy); }
  .panel { display:none; }
  .panel.active { display:block; }

  /* TABLE */
  .table-card { background:white; border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .table-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border); }
  .table-title { font-size:14px; font-weight:500; }
  .search-form { display:flex; align-items:center; gap:8px; border:1.5px solid var(--border); border-radius:8px; padding:6px 12px; background:var(--off-white); }
  .search-form svg { width:14px; height:14px; stroke:var(--muted); stroke-width:2; fill:none; }
  .search-form input { border:none; background:transparent; font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text); outline:none; width:180px; }
  .search-form input::placeholder { color:var(--muted); }
  table { width:100%; border-collapse:collapse; font-size:13px; }
  thead th { text-align:left; padding:10px 20px; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:var(--muted); font-weight:500; background:var(--off-white); border-bottom:1px solid var(--border); }
  tbody tr { border-bottom:1px solid rgba(200,217,238,0.4); transition:background 0.1s; }
  tbody tr:last-child { border-bottom:none; }
  tbody tr:hover { background:var(--off-white); }
  td { padding:12px 20px; vertical-align:middle; }
  .staff-cell { display:flex; align-items:center; gap:10px; }
  .av-sm { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; flex-shrink:0; }
  .role-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; }
  .role-Doctor { background:#E3EFFE; color:#1B4FA8; }
  .role-Nurse { background:#E3F7EF; color:#1B7A54; }
  .role-Admin { background:#FFF3E0; color:#A06000; }
  .role-Manager { background:#EDE7FE; color:#5B21B6; }
  .shift-pill { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:500; }
  .shift-AM { background:#FFF8DB; color:#7A5A00; }
  .shift-PM { background:#E0F0FF; color:#004A8C; }
  .shift-Night { background:#E8E3FF; color:#4B2D9E; }
  .status-dot { display:inline-flex; align-items:center; gap:5px; font-size:12px; }
  .dot-Active { width:7px; height:7px; border-radius:50%; background:var(--success); }
  .dot-Leave { width:7px; height:7px; border-radius:50%; background:var(--warn); }
  .action-btn { padding:4px 10px; border-radius:6px; border:1px solid var(--border); background:white; font-family:'DM Sans',sans-serif; font-size:11px; color:var(--navy); cursor:pointer; font-weight:500; transition:all 0.1s; }
  .action-btn:hover { background:var(--sky-pale); border-color:var(--sky); }
  .action-btn.del { color:var(--error); border-color:#f5c5c5; }
  .action-btn.del:hover { background:#FFF0F0; }
  .pagination-wrap { padding:12px 20px; display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border); font-size:12px; color:var(--muted); }
  .page-links { display:flex; gap:4px; }
  .page-links a, .page-links span { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); background:white; font-size:12px; cursor:pointer; color:var(--text); text-decoration:none; }
  .page-links span.active-page { background:var(--navy); color:white; border-color:var(--navy); }

  /* DEPARTMENTS */
  .dept-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
  .dept-card { background:white; border:1px solid var(--border); border-radius:12px; padding:18px 20px; }
  .dept-card-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
  .dept-name { font-weight:500; font-size:14px; margin-bottom:2px; }
  .dept-head { font-size:11px; color:var(--muted); }
  .dept-stats { display:flex; gap:16px; margin-top:10px; padding-top:10px; border-top:1px solid var(--border); }
  .dept-stat { font-size:11px; color:var(--muted); }
  .dept-stat strong { display:block; font-size:16px; font-weight:600; color:var(--text); line-height:1.2; }

  /* SCHEDULE */
  .schedule-grid { display:grid; gap:12px; }
  .schedule-row { background:white; border:1px solid var(--border); border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
  .sched-info { flex:1; min-width:150px; }
  .sched-name { font-weight:500; font-size:13px; }
  .sched-dept { font-size:11px; color:var(--muted); }
  .sched-days { display:flex; gap:4px; }
  .day-pill { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; background:var(--off-white); color:var(--muted); border:1px solid var(--border); }
  .day-pill.on { background:var(--navy); color:white; border-color:var(--navy); }

  /* RESPONSIBILITIES */
  .resp-card { background:white; border:1px solid var(--border); border-radius:12px; padding:18px 20px; margin-bottom:12px; }
  .resp-header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
  .resp-list { list-style:none; display:flex; flex-direction:column; gap:6px; }
  .resp-list li { display:flex; align-items:flex-start; gap:8px; font-size:13px; }
  .resp-list li::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--sky); margin-top:6px; flex-shrink:0; }

  /* FLASH MESSAGE */
  .flash { padding:12px 20px; border-radius:8px; margin-bottom:16px; font-size:13px; font-weight:500; }
  .flash.success { background:#E3F7EF; color:#1B7A54; border:1px solid #9FE1CB; }
  .flash.error { background:#FDEDEC; color:#C0392B; border:1px solid #F5C6C6; }

  /* MODAL */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(10,18,40,0.55); z-index:200; align-items:center; justify-content:center; overflow-y:auto; padding:20px; }
  .modal-overlay.open { display:flex; }
  .modal { background:white; border-radius:14px; padding:28px 32px; width:100%; max-width:520px; position:relative; margin:auto; }
  .modal h3 { font-family:'Playfair Display',serif; font-size:20px; margin-bottom:6px; }
  .modal-sub { font-size:12px; color:var(--muted); margin-bottom:20px; }
  .modal-fields { display:flex; flex-direction:column; gap:14px; }
  .modal-row { display:flex; gap:12px; }
  .modal-row .mfield { flex:1; }
  .mfield { display:flex; flex-direction:column; gap:5px; }
  .mfield label { font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:0.5px; color:var(--text); }
  .mfield input, .mfield select, .mfield textarea { border:1.5px solid var(--border); border-radius:7px; padding:8px 12px; font-family:'DM Sans',sans-serif; font-size:13px; outline:none; width:100%; }
  .mfield input:focus, .mfield select:focus, .mfield textarea:focus { border-color:var(--sky); }
  .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; }
  .btn-cancel { padding:9px 18px; border:1.5px solid var(--border); border-radius:8px; background:white; font-family:'DM Sans',sans-serif; font-size:13px; cursor:pointer; color:var(--muted); }
  .btn-save { padding:9px 18px; border:none; border-radius:8px; background:var(--navy); color:white; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; }
  .close-modal { position:absolute; top:18px; right:20px; background:none; border:none; cursor:pointer; color:var(--muted); font-size:20px; line-height:1; }

  /* RESP INPUTS */
  .resp-input-row { display:flex; gap:8px; align-items:center; margin-bottom:8px; }
  .resp-input-row input { flex:1; border:1.5px solid var(--border); border-radius:7px; padding:7px 10px; font-family:'DM Sans',sans-serif; font-size:13px; outline:none; }
  .resp-input-row input:focus { border-color:var(--sky); }
  .remove-resp { background:none; border:none; cursor:pointer; color:var(--error); font-size:18px; line-height:1; padding:0 4px; }
  .add-resp-btn { font-size:12px; color:var(--sky); background:none; border:none; cursor:pointer; font-family:'DM Sans',sans-serif; font-weight:500; padding:0; margin-top:4px; }

  /* DELETE CONFIRM */
  .confirm-box { background:#FDEDEC; border:1px solid #F5C6C6; border-radius:8px; padding:14px; margin-bottom:16px; font-size:13px; color:#7A1010; }

  .avbg { background:#E3EFFE; color:#1B4FA8; }
  .avbg:nth-child(2) { background:#E3F7EF; color:#1B7A54; }
</style>
@endpush

@section('content')
<div style="padding:28px;">

    {{-- Flash Messages --}}
    @if($errors->any())
      <div class="flash error">{{ $errors->first() }}</div>
    @endif

    {{-- STAT CARDS --}}
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Total Staff</div>
        <div class="stat-value">{{ $counts['total'] }}</div>
        <div class="stat-sub">All active members</div>
      </div>
      <div class="stat-card" style="background:var(--sky)">
        <div class="stat-label">Doctors</div>
        <div class="stat-value">{{ $counts['doctors'] }}</div>
        <div class="stat-sub">Across departments</div>
      </div>
      <div class="stat-card" style="background:#2E7D9B">
        <div class="stat-label">Nurses</div>
        <div class="stat-value">{{ $counts['nurses'] }}</div>
        <div class="stat-sub">Ward & ICU assigned</div>
      </div>
      <div class="stat-card light">
        <div class="stat-label">Admin Staff</div>
        <div class="stat-value">{{ $counts['admin'] }}</div>
        <div class="stat-sub">Front desk & records</div>
      </div>
    </div>

    <div class="flow-panel">
      <div class="flow-header">
        <h3>Live staff flow</h3>
        <span>Real-world assignments and shift activity</span>
      </div>
      <div class="flow-timeline">
        @foreach($flowEvents as $event)
        <div class="flow-item">
          <div class="flow-time">{{ $event['time'] }}</div>
          <div class="flow-content">
            <div class="flow-title">{{ $event['title'] }}</div>
            <div class="flow-desc">{{ $event['description'] }}</div>
            <div class="flow-badge {{ $event['statusClass'] }}">{{ $event['status'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- TABS --}}
    <div class="tab-bar">
      <div class="tab active" onclick="switchTab('all-staff',this)">All Staff</div>
      <div class="tab" onclick="switchTab('departments',this)">Departments</div>
      <div class="tab" onclick="switchTab('schedules',this)">Schedules & Roles</div>
      <div class="tab" onclick="switchTab('responsibilities',this)">Patient Responsibilities</div>
    </div>

    {{-- ===== TAB 1: ALL STAFF ===== --}}
    <div class="panel active" id="panel-all-staff">
      <div class="table-card">
        <div class="table-header">
          <div class="table-title">Staff Records</div>
          <form method="GET" action="{{ route('staff.index') }}" class="search-form">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" placeholder="Search staff…" value="{{ request('search') }}">
          </form>
        </div>
        <table>
          <thead>
            <tr>
              <th>Name</th><th>Role</th><th>Department</th>
              <th>Ward</th><th>Schedule</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($staff as $s)
            @php
              $colors = [
                ['#E3EFFE','#1B4FA8'],['#E3F7EF','#1B7A54'],['#EDE7FE','#7B3FA0'],
                ['#FFF3E0','#A06000'],['#FDEDEC','#C0392B'],['#D6EAFA','#185FA5']
              ];
              $c = $colors[$s->id % 6];
            @endphp
            <tr>
              <td>
                <div class="staff-cell">
                  <div class="av-sm" style="background:{{ $c[0] }};color:{{ $c[1] }}">{{ $s->initials }}</div>
                  <span style="font-weight:500">{{ $s->full_name }}</span>
                </div>
              </td>
              <td><span class="role-badge role-{{ $s->role }}">{{ $s->role }}</span></td>
              <td style="color:var(--muted)">{{ $s->department }}</td>
              <td>{{ $s->ward ?? '—' }}</td>
              <td><span class="shift-pill shift-{{ $s->shift }}">{{ $s->shift }}</span></td>
              <td>
                <span class="status-dot">
                  <span class="dot-{{ $s->status === 'Active' ? 'Active' : 'Leave' }}"></span>
                  {{ $s->status }}
                </span>
              </td>
              <td>
                <div style="display:flex;gap:6px">
                  @if(auth()->user()->role === 'Medical Director' || auth()->user()->role === 'Personnel/HR Staff')
                  <button class="action-btn" onclick="openEditModal({{ $s->id }})">Edit</button>
                  @endif
                  <button class="action-btn" onclick="openViewModal({{ $s->id }})">View</button>
                  @if(auth()->user()->role === 'Medical Director' || auth()->user()->role === 'Personnel/HR Staff')
                  <button class="action-btn del" onclick="openDeleteModal({{ $s->id }}, @js($s->full_name))">Remove</button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">No staff records found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="pagination-wrap">
          <span>Showing {{ $staff->firstItem() }}–{{ $staff->lastItem() }} of {{ $staff->total() }}</span>
          <div class="page-links">
            {{ $staff->links('pagination::simple-bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>

    {{-- ===== TAB 2: DEPARTMENTS ===== --}}
    <div class="panel" id="panel-departments">
      <div class="dept-grid">
        @php
          $deptIcons = ['Emergency'=>'🚨','Cardiology'=>'❤️','Pediatrics'=>'🧒','Orthopedics'=>'🦴','Neurology'=>'🧠','General Medicine'=>'💊','Administration'=>'🗂️'];
          $deptColors = ['Emergency'=>['#FDEDEC','#C0392B'],'Cardiology'=>['#E3EFFE','#1B4FA8'],'Pediatrics'=>['#E3F7EF','#1B7A54'],'Orthopedics'=>['#FFF3E0','#A06000'],'Neurology'=>['#EDE7FE','#7B3FA0'],'General Medicine'=>['#D6EAFA','#185FA5'],'Administration'=>['#F1EFE8','#5F5E5A']];
        @endphp
        @foreach($deptSummary as $dept => $info)
        @php $dc = $deptColors[$dept] ?? ['#E3EFFE','#1B4FA8']; @endphp
        <div class="dept-card">
          <div class="dept-card-top">
            <div>
              <div class="dept-name">{{ $dept }}</div>
              <div class="dept-head">Head: {{ $info['head'] ? 'Dr. '.$info['head']->last_name : 'Not assigned' }}</div>
            </div>
            <div style="width:36px;height:36px;border-radius:8px;background:{{ $dc[0] }};display:flex;align-items:center;justify-content:center;font-size:18px">
              {{ $deptIcons[$dept] ?? '🏥' }}
            </div>
          </div>
          <div class="dept-stats">
            <div class="dept-stat"><strong style="color:{{ $dc[1] }}">{{ $info['doctors'] }}</strong>Doctors</div>
            <div class="dept-stat"><strong>{{ $info['nurses'] }}</strong>Nurses</div>
            <div class="dept-stat"><strong>{{ $info['doctors'] + $info['nurses'] }}</strong>Total</div>
          </div>
          <div style="margin-top:12px;display:flex;gap:8px">
            <a href="{{ route('staff.index', ['department' => $dept]) }}" class="action-btn" style="font-size:12px;text-decoration:none">Manage Staff</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- ===== TAB 3: SCHEDULES & ROLES ===== --}}
    <div class="panel" id="panel-schedules">
      <div class="schedule-grid">
        @foreach($allStaff as $s)
        @php $c = $colors[$s->id % 6] ?? ['#E3EFFE','#1B4FA8']; $sched = $s->schedule; @endphp
        <div class="schedule-row">
          <div class="av-sm" style="background:{{ $c[0] }};color:{{ $c[1] }}">{{ $s->initials }}</div>
          <div class="sched-info">
            <div class="sched-name">{{ $s->full_name }}</div>
            <div class="sched-dept">{{ $s->department }} · {{ $s->role }}</div>
          </div>
          <span class="shift-pill shift-{{ $s->shift }}">{{ $s->shift }} Shift</span>
          <div class="sched-days">
            @foreach(['mon'=>'M','tue'=>'T','wed'=>'W','thu'=>'Th','fri'=>'F','sat'=>'Sa','sun'=>'Su'] as $key=>$label)
            <div class="day-pill {{ $sched && $sched->$key ? 'on' : '' }}">{{ $label }}</div>
            @endforeach
          </div>
          @if(auth()->user()->role !== 'Charge Nurse')
          <button class="action-btn" onclick="openScheduleModal({{ $s->id }}, @js($s->full_name), @js($s->shift), @js($sched))">Edit</button>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    {{-- ===== TAB 4: RESPONSIBILITIES ===== --}}
    <div class="panel" id="panel-responsibilities">
      @foreach($allStaff as $s)
      @php $c = $colors[$s->id % 6] ?? ['#E3EFFE','#1B4FA8']; @endphp
      <div class="resp-card">
        <div class="resp-header">
          <div class="av-sm" style="background:{{ $c[0] }};color:{{ $c[1] }};width:38px;height:38px;font-size:13px">{{ $s->initials }}</div>
          <div>
            <div style="font-weight:500;font-size:14px">{{ $s->full_name }}</div>
            <div style="font-size:11px;color:var(--muted)">{{ $s->role }} · {{ $s->department }}</div>
          </div>
          @if(auth()->user()->role !== 'Charge Nurse')
          <button class="action-btn" style="margin-left:auto;font-size:12px" onclick="openRespModal({{ $s->id }}, @js($s->full_name), @js($s->responsibilities->pluck('description')->values()))">Edit Responsibilities</button>
          @endif
        </div>
        <ul class="resp-list">
          @forelse($s->responsibilities as $r)
          <li>{{ $r->description }}</li>
          @empty
          <li style="color:var(--muted);list-style:none">No responsibilities assigned yet.</li>
          @endforelse
        </ul>
      </div>
      @endforeach
    </div>

  </div>{{-- end .content --}}

{{-- ===== MODAL: ADD STAFF ===== --}}
<div class="modal-overlay" id="modal-add">
  <div class="modal">
    <button class="close-modal" onclick="closeModal('add')">✕</button>
    <h3>Add New Staff</h3>
    <p class="modal-sub">Fill in the details to register a new staff member.</p>
    <form method="POST" action="{{ route('staff.store') }}">
      @csrf
      <div class="modal-fields">
        <div class="modal-row">
          <div class="mfield"><label>First Name</label><input type="text" name="first_name" required placeholder="Jane"></div>
          <div class="mfield"><label>Last Name</label><input type="text" name="last_name" required placeholder="Doe"></div>
        </div>
        <div class="mfield"><label>Email</label><input type="email" name="email" required placeholder="staff@wellmeadows.com"></div>
        <div class="mfield"><label>Phone</label><input type="text" name="phone" placeholder="09xxxxxxxxx"></div>
        <div class="modal-row">
          <div class="mfield"><label>Role</label>
            <select name="role" required>
              <option value="">Select role</option>
              <option>Doctor</option><option>Nurse</option><option>Admin</option><option>Ward Manager</option>
            </select>
          </div>
          <div class="mfield"><label>Status</label>
            <select name="status">
              <option value="Active">Active</option><option value="On Leave">On Leave</option>
            </select>
          </div>
        </div>
        <div class="modal-row">
          <div class="mfield"><label>Department</label>
            <select name="department" required>
              <option value="">Select department</option>
              @foreach($departments as $d)<option>{{ $d }}</option>@endforeach
            </select>
          </div>
          <div class="mfield"><label>Ward</label>
            <select name="ward">
              <option value="">None</option>
              <option>Ward A</option><option>Ward B</option><option>Ward C</option><option>ICU</option>
            </select>
          </div>
        </div>
        <div class="mfield"><label>Shift</label>
          <select name="shift" required>
            <option value="">Select shift</option>
            <option>AM</option><option>PM</option><option>Night</option>
          </select>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('add')">Cancel</button>
        <button type="submit" class="btn-save">Save Staff</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL: EDIT STAFF ===== --}}
<div class="modal-overlay" id="modal-edit">
  <div class="modal">
    <button class="close-modal" onclick="closeModal('edit')">✕</button>
    <h3>Edit Staff</h3>
    <p class="modal-sub">Update staff member information.</p>
    <form method="POST" id="edit-form">
      @csrf @method('PUT')
      <div class="modal-fields">
        <div class="modal-row">
          <div class="mfield"><label>First Name</label><input type="text" name="first_name" id="edit-first" required></div>
          <div class="mfield"><label>Last Name</label><input type="text" name="last_name" id="edit-last" required></div>
        </div>
        <div class="mfield"><label>Email</label><input type="email" name="email" id="edit-email" required></div>
        <div class="mfield"><label>Phone</label><input type="text" name="phone" id="edit-phone"></div>
        <div class="modal-row">
          <div class="mfield"><label>Role</label>
            <select name="role" id="edit-role" required>
              <option>Doctor</option><option>Nurse</option><option>Admin</option><option>Ward Manager</option>
            </select>
          </div>
          <div class="mfield"><label>Status</label>
            <select name="status" id="edit-status">
              <option value="Active">Active</option><option value="On Leave">On Leave</option>
            </select>
          </div>
        </div>
        <div class="modal-row">
          <div class="mfield"><label>Department</label>
            <select name="department" id="edit-dept" required>
              @foreach($departments as $d)<option>{{ $d }}</option>@endforeach
            </select>
          </div>
          <div class="mfield"><label>Ward</label>
            <select name="ward" id="edit-ward">
              <option value="">None</option>
              <option>Ward A</option><option>Ward B</option><option>Ward C</option><option>ICU</option>
            </select>
          </div>
        </div>
        <div class="mfield"><label>Shift</label>
          <select name="shift" id="edit-shift" required>
            <option>AM</option><option>PM</option><option>Night</option>
          </select>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('edit')">Cancel</button>
        <button type="submit" class="btn-save">Update Staff</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL: VIEW STAFF ===== --}}
<div class="modal-overlay" id="modal-view">
  <div class="modal">
    <button class="close-modal" onclick="closeModal('view')">✕</button>
    <h3 id="view-name">Staff Details</h3>
    <p class="modal-sub" id="view-role-dept"></p>
    <div id="view-body" style="font-size:13px;display:flex;flex-direction:column;gap:10px"></div>
    <div class="modal-actions">
      <button type="button" class="btn-cancel" onclick="closeModal('view')">Close</button>
    </div>
  </div>
</div>

{{-- ===== MODAL: DELETE CONFIRM ===== --}}
<div class="modal-overlay" id="modal-delete">
  <div class="modal" style="max-width:420px">
    <button class="close-modal" onclick="closeModal('delete')">✕</button>
    <h3>Remove Staff</h3>
    <p class="modal-sub">This action cannot be undone.</p>
    <div class="confirm-box">You are about to remove <strong id="delete-name"></strong> from the system.</div>
    <form method="POST" id="delete-form">
      @csrf @method('DELETE')
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('delete')">Cancel</button>
        <button type="submit" class="btn-save" style="background:var(--error)">Yes, Remove</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL: EDIT SCHEDULE ===== --}}
<div class="modal-overlay" id="modal-schedule">
  <div class="modal">
    <button class="close-modal" onclick="closeModal('schedule')">✕</button>
    <h3>Edit Schedule</h3>
    <p class="modal-sub" id="sched-modal-name"></p>
    <form method="POST" id="schedule-form">
      @csrf @method('PUT')
      <div class="modal-fields">
        <div class="mfield"><label>Shift</label>
          <select name="shift" id="sched-shift">
            <option>AM</option><option>PM</option><option>Night</option>
          </select>
        </div>
        <div class="mfield">
          <label>Working Days</label>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
            @foreach(['mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday','sun'=>'Sunday'] as $key=>$label)
            <label style="display:flex;align-items:center;gap:5px;font-size:13px;text-transform:none;letter-spacing:0;font-weight:400;cursor:pointer">
              <input type="checkbox" name="{{ $key }}" id="sched-{{ $key }}" style="width:auto"> {{ $label }}
            </label>
            @endforeach
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('schedule')">Cancel</button>
        <button type="submit" class="btn-save">Save Schedule</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL: EDIT RESPONSIBILITIES ===== --}}
<div class="modal-overlay" id="modal-resp">
  <div class="modal">
    <button class="close-modal" onclick="closeModal('resp')">✕</button>
    <h3>Edit Responsibilities</h3>
    <p class="modal-sub" id="resp-modal-name"></p>
    <form method="POST" id="resp-form">
      @csrf @method('PUT')
      <div class="modal-fields">
        <div class="mfield">
          <label>Patient Care Responsibilities</label>
          <div id="resp-inputs"></div>
          <button type="button" class="add-resp-btn" onclick="addRespInput()">+ Add responsibility</button>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('resp')">Cancel</button>
        <button type="submit" class="btn-save">Save Responsibilities</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Store all staff data for JS use
  const allStaffData = @json($staffJson);

  // TABS
  function switchTab(name, el) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');
  }

  // MODALS
  function openModal(id) {
    document.getElementById('modal-' + id).classList.add('open');
  }

  function closeModal(id) {
    document.getElementById('modal-' + id).classList.remove('open');
  }

  // Close on overlay click
  document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('open');
    });
  });

  // EDIT STAFF
  function openEditModal(id) {
    const s = allStaffData.find(x => x.id === id);
    if (!s) return;
    document.getElementById('edit-form').action = '/staff/' + id;
    document.getElementById('edit-first').value  = s.first_name;
    document.getElementById('edit-last').value   = s.last_name;
    document.getElementById('edit-email').value  = s.email;
    document.getElementById('edit-phone').value  = s.phone || '';
    document.getElementById('edit-role').value   = s.role;
    document.getElementById('edit-status').value = s.status;
    document.getElementById('edit-dept').value   = s.department;
    document.getElementById('edit-ward').value   = s.ward || '';
    document.getElementById('edit-shift').value  = s.shift;
    openModal('edit');
  }

  // VIEW STAFF
  function openViewModal(id) {
    const s = allStaffData.find(x => x.id === id);
    if (!s) return;
    document.getElementById('view-name').textContent = s.full_name;
    document.getElementById('view-role-dept').textContent = s.role + ' · ' + s.department;
    document.getElementById('view-body').innerHTML = `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">EMAIL</div><div>${s.email}</div></div>
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">PHONE</div><div>${s.phone || '—'}</div></div>
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">WARD</div><div>${s.ward || '—'}</div></div>
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">SHIFT</div><div>${s.shift}</div></div>
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">STATUS</div><div>${s.status}</div></div>
        <div><div style="font-size:11px;color:var(--muted);margin-bottom:2px">DEPARTMENT</div><div>${s.department}</div></div>
      </div>
    `;
    openModal('view');
  }

  // DELETE STAFF
  function openDeleteModal(id, name) {
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-form').action = '/staff/' + id;
    openModal('delete');
  }

  // SCHEDULE MODAL
  function openScheduleModal(id, name, shift, sched) {
    document.getElementById('sched-modal-name').textContent = name;
    document.getElementById('schedule-form').action = '/staff/' + id + '/schedule';
    document.getElementById('sched-shift').value = shift;
    const days = ['mon','tue','wed','thu','fri','sat','sun'];
    days.forEach(d => {
      document.getElementById('sched-' + d).checked = sched && sched[d] ? true : false;
    });
    openModal('schedule');
  }

  // RESPONSIBILITIES MODAL
  function openRespModal(id, name, resps) {
    document.getElementById('resp-modal-name').textContent = name;
    document.getElementById('resp-form').action = '/staff/' + id + '/responsibilities';
    const container = document.getElementById('resp-inputs');
    container.innerHTML = '';
    if (resps.length === 0) resps = [''];
    resps.forEach(r => addRespInput(r));
    openModal('resp');
  }

  function addRespInput(value = '') {
    const container = document.getElementById('resp-inputs');
    const row = document.createElement('div');
    row.className = 'resp-input-row';
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'responsibilities[]';
    input.value = value;
    input.placeholder = 'Enter responsibility...';
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'remove-resp';
    button.textContent = 'x';
    button.addEventListener('click', () => row.remove());
    row.append(input, button);
    container.appendChild(row);
  }
</script>
@endpush
