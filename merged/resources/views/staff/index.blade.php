<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Wellmeadows — Staff & Department Management</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
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
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">

    <div class="logo-icon-sm">
      <svg fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">

        <path d="M493.666,102.065H380.904V61.664c0-10.125-8.209-18.334-18.334-18.334H149.43c-10.125,0-18.334,8.209-18.334,18.334v40.401H18.334C8.209,102.065,0,110.274,0,120.399v329.937c0,10.125,8.209,18.334,18.334,18.334c7.547,0,449.061,0,475.333,0c10.125,0,18.334-8.209,18.334-18.334V120.399C512,110.274,503.791,102.065,493.666,102.065z M131.096,432.002H36.667V216.347h94.429V432.002z M131.096,179.68H36.667v-40.947h94.429V179.68z M276.014,432.002h-40.539v-78.518h40.539V432.002z M344.238,432.002h-31.556v-96.851c0-10.125-8.209-18.334-18.334-18.334h-77.206c-10.125,0-18.334,8.209-18.334,18.334v96.851h-31.045c-0.001-13.078-0.001-335.565-0.001-352.004h176.475C344.238,96.603,344.238,419.21,344.238,432.002z M380.904,138.732h94.429v40.947h-94.429V138.732z M475.334,432.002h-94.429V216.347h94.429V432.002z"></path>

        <path d="M217.193,177.54h20.474v20.474c0,10.125,8.209,18.334,18.334,18.334s18.334-8.209,18.334-18.334V177.54h20.474c10.125,0,18.334-8.209,18.334-18.334s-8.209-18.334-18.334-18.334h-20.474v-20.474c0-10.125-8.209-18.334-18.334-18.334s-18.334,8.209-18.334,18.334v20.474h-20.474c-10.125,0-18.334,8.209-18.334,18.334S207.068,177.54,217.193,177.54z"></path>

      </svg>
    </div>
    <div class="logo-text">Wellmeadows <span>Hospital System</span></div>
  </div>
  <div class="nav-section">Main Menu</div>
  <a class="nav-item" href="{{ route('dashboard') }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    Dashboard
  </a>
  <a class="nav-item active" href="{{ route('staff.index') }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    Staff & Depts
  </a>
  <a class="nav-item" href="#">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    Schedules
  </a>
  <a class="nav-item" href="#">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
    Wards
  </a>
  <a class="nav-item" href="#">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    Appointment
  </a>
  <a class="nav-item" href="#">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Patient Care
  </a>
  <a class="nav-item" href="#">
  <!-- Updated Billing Icon -->
  <svg viewBox="0 0 32 32" fill="currentColor">
    <path d="M15.002,8l-0.998,0c-0.552,0 -1,0.448 -1,1c0,0.552 0.448,1 1,1l1.002,-0l0.002,1.002c0.001,0.552 0.45,0.999 1.002,0.998c0.552,-0.001 0.999,-0.45 0.998,-1.002l-0.002,-0.998l0.998,0c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1l-1.002,0l-0.002,-1.002c-0.001,-0.552 -0.45,-0.999 -1.002,-0.998c-0.552,0.001 -0.999,0.45 -0.998,1.002l0.002,0.998Z"></path>
    <path d="M26,9l-0,-4c-0,-0.796 -0.316,-1.559 -0.879,-2.121c-0.562,-0.563 -1.325,-0.879 -2.121,-0.879c-3.463,0 -10.537,0 -14,-0c-0.796,-0 -1.559,0.316 -2.121,0.879c-0.563,0.562 -0.879,1.325 -0.879,2.121l0,22c0,0.796 0.316,1.559 0.879,2.121c0.562,0.563 1.325,0.879 2.121,0.879c2.6,-0 10.316,0 13.999,0c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1c-3.683,0 -11.399,-0 -13.999,-0c-0.265,0 -0.52,-0.105 -0.707,-0.293c-0.188,-0.187 -0.293,-0.442 -0.293,-0.707c-0,-0 -0,-22 -0,-22c0,-0.265 0.105,-0.52 0.293,-0.707c0.187,-0.188 0.442,-0.293 0.707,-0.293l14,0c0.265,-0 0.52,0.105 0.707,0.293c0.188,0.187 0.293,0.442 0.293,0.707c-0,-0 -0,4 -0,4c-0,0.552 0.448,1 1,1c0.552,0 1,-0.448 1,-1Z"></path>
    <path d="M23.982,14.206c-1.159,0.414 -1.99,1.523 -1.99,2.825c0,1.659 1.333,2.987 2.992,2.987c0.553,0 1.008,0.448 1.008,1.001c0,0.552 -0.448,1 -1,1c-0.552,-0 -1,-0.448 -1,-1c0,-0.552 -0.448,-1 -1,-1c-0.552,-0 -1,0.448 -1,1c0,1.302 0.832,2.412 1.993,2.826l-0.005,1.163c-0.002,0.552 0.444,1.002 0.996,1.004c0.552,0.003 1.002,-0.444 1.004,-0.996l0.005,-1.166c1.168,-0.41 2.007,-1.523 2.007,-2.831c0,-1.655 -1.354,-3.001 -3.008,-3.001c-0.549,0 -0.992,-0.438 -0.992,-0.987c0,-0.552 0.448,-1 1,-1c0.55,0 0.997,0.445 1,0.994l0,0.013c0.004,0.548 0.45,0.993 1,0.993c0.552,0 1,-0.448 1,-1c0,-1.309 -0.84,-2.423 -2.01,-2.832l0.01,-1.179c0.004,-0.552 -0.44,-1.003 -0.992,-1.008c-0.552,-0.004 -1.004,0.441 -1.008,0.992l-0.01,1.202Z"></path>
    <path d="M11,17.019l8,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-8,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1Z"></path>
    <path d="M11,21.031l8,-0c0.552,-0 1,-0.448 1,-1c-0,-0.552 -0.448,-1 -1,-1l-8,-0c-0.552,-0 -1,0.448 -1,1c-0,0.552 0.448,1 1,1Z"></path>
    <path d="M11,25.012l8,0c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1l-8,0c-0.552,0 -1,0.448 -1,1c0,0.552 0.448,1 1,1Z"></path>
  </svg>
  Billing & Reporting
</a>
  <div class="nav-section">System</div>
<a class="nav-item" href="#">
  <svg viewBox="0 0 24 24" fill="none"
       xmlns="http://www.w3.org/2000/svg"
       stroke="currentColor"
       stroke-width="1.8"
       stroke-linecap="round"
       stroke-linejoin="round">
    <!-- outer gear ring -->
    <path d="M12 8.5a3.5 3.5 0 1 0 0 7a3.5 3.5 0 0 0 0-7z"/>
    <!-- gear body simplified -->
    <path d="M19.4 15a7.8 7.8 0 0 0 .1-2l2-1.2-2-3.4-2.3.6a7.6 7.6 0 0 0-1.7-1L15 5h-6l-.5 2a7.6 7.6 0 0 0-1.7 1L4.5 7.4l-2 3.4 2 1.2a7.8 7.8 0 0 0 .1 2l-2 1.2 2 3.4 2.3-.6a7.6 7.6 0 0 0 1.7 1L9 21h6l.5-2a7.6 7.6 0 0 0 1.7-1l2.3.6 2-3.4-2-1.2z"/>
  </svg>
  Settings
</a>
  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">{{ auth()->user()->initials }}</div>
      <div>
        <div class="user-name">{{ auth()->user()->full_name }}</div>
        <div class="user-role">{{ auth()->user()->role }}</div>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sign-out-btn">Sign Out</button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="topbar">
    <div class="topbar-left"><h2>Staff & Department Management</h2></div>
    <div class="topbar-right">
      <div class="date-badge">{{ now()->format('F d, Y') }}</div>
      @if(auth()->user()->role !== 'Charge Nurse')
      <button class="add-btn" onclick="openModal('add')">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Staff
      </button>
      @endif
    </div>
  </div>

  <div class="content">

    {{-- Flash Messages --}}
    @if(session('success'))
      <div class="flash success">{{ session('success') }}</div>
    @endif
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
                  @if(auth()->user()->role !== 'Charge Nurse')
                  <button class="action-btn" onclick="openEditModal({{ $s->id }})">Edit</button>
                  @endif
                  <button class="action-btn" onclick="openViewModal({{ $s->id }})">View</button>
                  @if(auth()->user()->role !== 'Charge Nurse')
                  <button class="action-btn del" onclick="openDeleteModal({{ $s->id }}, '{{ $s->full_name }}')">Remove</button>
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
          <button class="action-btn" onclick="openScheduleModal({{ $s->id }}, '{{ $s->full_name }}', '{{ $s->shift }}', {{ json_encode($sched) }})">Edit</button>
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
          <button class="action-btn" style="margin-left:auto;font-size:12px" onclick="openRespModal({{ $s->id }}, '{{ $s->full_name }}', {{ json_encode($s->responsibilities->pluck('description')) }})">Edit Responsibilities</button>
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
</main>

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
    row.innerHTML = `
      <input type="text" name="responsibilities[]" value="${value}" placeholder="Enter responsibility…">
      <button type="button" class="remove-resp" onclick="this.parentElement.remove()">✕</button>
    `;
    container.appendChild(row);
  }
</script>
</body>
</html>