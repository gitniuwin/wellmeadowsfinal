@extends('layouts.app')
@section('page-title', 'Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:#1B2D5B; --navy-light:#2a4080; --sky:#5B9BD5; --sky-light:#A8CBF0;
    --sky-pale:#D6EAFA; --off-white:#F4F8FC; --muted:#6B7E9F; --border:#C8D9EE;
    --text:#1a2640; --sidebar-w:210px; --success:#2E8B6A; --warn:#C88000; --error:#D94F4F;
  }

  .role-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:500; }
  .role-director { background:#E3EFFE; color:#1B4FA8; }
  .role-nurse { background:#E3F7EF; color:#1B7A54; }
  .role-hr { background:#FFF3E0; color:#A06000; }

  /* HERO BANNER */
  .hero { position:relative; min-height:260px; overflow:hidden; display:flex; align-items:flex-end; padding:36px 36px 32px; }
  .hero-bg { position:absolute; inset:0; background:url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat; }
  .hero-bg::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg, rgba(11,22,55,0.92) 0%, rgba(27,45,91,0.80) 50%, rgba(11,22,55,0.55) 100%); }
  .hero-content { position:relative; z-index:1; display:flex; align-items:flex-end; justify-content:space-between; width:100%; flex-wrap:wrap; gap:16px; }
  .hero-left h1 { font-family:'Playfair Display',serif; font-size:28px; color:white; margin-bottom:6px; }
  .hero-left p { font-size:13px; color:rgba(255,255,255,0.6); max-width:460px; line-height:1.6; }
  .hero-clock { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:12px; padding:14px 20px; text-align:center; backdrop-filter:blur(8px); }
  .hero-clock-time { font-size:32px; font-weight:600; color:white; letter-spacing:1px; line-height:1; }
  .hero-clock-date { font-size:12px; color:rgba(255,255,255,0.6); margin-top:6px; }

  /* CONTENT */
  .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
  .stat-card { background:white; border:1px solid var(--border); border-radius:14px; padding:20px 22px; cursor:pointer; transition:transform .15s, box-shadow .15s; text-decoration:none; color:inherit; display:block; }
  .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(27,45,91,0.1); }
  .stat-card.navy { background:var(--navy); border-color:var(--navy); }
  .stat-card.sky { background:var(--sky); border-color:var(--sky); }
  .stat-card.teal { background:#2E7D9B; border-color:#2E7D9B; }
  .stat-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
  .stat-icon svg { width:20px; height:20px; }
  .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px; }
  .stat-value { font-family:'Playfair Display',serif; font-size:32px; line-height:1; margin-bottom:4px; }
  .stat-sub { font-size:11px; }
  .stat-card.navy .stat-label, .stat-card.navy .stat-sub { color:rgba(255,255,255,0.55); }
  .stat-card.navy .stat-value { color:white; }
  .stat-card.sky .stat-label, .stat-card.sky .stat-sub { color:rgba(255,255,255,0.65); }
  .stat-card.sky .stat-value { color:white; }
  .stat-card.teal .stat-label, .stat-card.teal .stat-sub { color:rgba(255,255,255,0.65); }
  .stat-card.teal .stat-value { color:white; }
  .stat-card:not(.navy):not(.sky):not(.teal) .stat-label { color:var(--muted); }
  .stat-card:not(.navy):not(.sky):not(.teal) .stat-value { color:var(--text); }
  .stat-card:not(.navy):not(.sky):not(.teal) .stat-sub { color:var(--muted); }
  .stat-icon.light { background:var(--sky-pale); }
  .stat-icon.white { background:rgba(255,255,255,0.15); }

  .section-title { font-family:'Playfair Display',serif; font-size:18px; margin-bottom:16px; }
  .section-sub { font-size:12px; color:var(--muted); margin-top:-10px; margin-bottom:16px; }
  .module-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; }
  .module-card { background:white; border:1px solid var(--border); border-radius:14px; padding:22px; cursor:pointer; transition:all .18s; text-decoration:none; color:inherit; display:flex; flex-direction:column; gap:12px; position:relative; overflow:hidden; }
  .module-card:hover { transform:translateY(-3px); box-shadow:0 10px 28px rgba(27,45,91,0.1); border-color:var(--sky); }
  .module-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-color, var(--sky)); }
  .module-num { font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:var(--muted); }
  .module-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
  .module-icon svg { width:22px; height:22px; fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
  .module-name { font-weight:600; font-size:15px; line-height:1.3; }
  .module-desc { font-size:12px; color:var(--muted); line-height:1.6; }
  .module-tasks { display:flex; flex-direction:column; gap:5px; margin-top:4px; }
  .module-task { display:flex; align-items:center; gap:7px; font-size:11px; color:var(--muted); }
  .module-task::before { content:''; width:5px; height:5px; border-radius:50%; background:var(--accent-color, var(--sky)); flex-shrink:0; }
  .module-arrow { margin-top:auto; display:flex; align-items:center; gap:5px; font-size:12px; font-weight:500; color:var(--accent-color, var(--sky)); }
  .module-arrow svg { width:14px; height:14px; stroke:currentColor; stroke-width:2; fill:none; transition:transform .15s; }
  .module-card:hover .module-arrow svg { transform:translateX(4px); }

  .bottom-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .card { background:white; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
  .card-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
  .card-title { font-size:14px; font-weight:500; }
  .card-link { font-size:12px; color:var(--sky); text-decoration:none; }
  .card-body { padding:0; }
  .staff-row { display:flex; align-items:center; gap:12px; padding:12px 20px; border-bottom:1px solid rgba(200,217,238,0.3); transition:background .1s; }
  .staff-row:last-child { border-bottom:none; }
  .staff-row:hover { background:var(--off-white); }
  .av { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; flex-shrink:0; }
  .staff-name { font-weight:500; font-size:13px; }
  .staff-sub { font-size:11px; color:var(--muted); }
  .role-pill { display:inline-block; padding:2px 9px; border-radius:20px; font-size:10px; font-weight:500; margin-left:auto; flex-shrink:0; }
  .pill-doctor { background:#E3EFFE; color:#1B4FA8; }
  .pill-nurse { background:#E3F7EF; color:#1B7A54; }
  .pill-admin { background:#FFF3E0; color:#A06000; }
  .activity-row { display:flex; align-items:flex-start; gap:12px; padding:12px 20px; border-bottom:1px solid rgba(200,217,238,0.3); }
  .activity-row:last-child { border-bottom:none; }
  .act-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .act-icon svg { width:15px; height:15px; fill:none; stroke-width:2; stroke-linecap:round; }
  .act-text { font-size:12px; line-height:1.5; color:var(--text); }
  .act-text strong { color:var(--navy); }
  .act-time { font-size:11px; color:var(--muted); margin-top:2px; }
</style>
@endpush

@section('content')

{{-- HERO BANNER --}}
<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <div class="hero-left">
      <h1>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->first_name }}!</h1>
      <p>Welcome to Wellmeadows Hospital Management System. Here's an overview of today's hospital operations.</p>
    </div>
    <div class="hero-clock">
      <div class="hero-clock-time" id="hero-clock">--:--:--</div>
      <div class="hero-clock-date" id="hero-date"></div>
    </div>
  </div>
</div>

<div style="padding:28px 36px;">

  {{-- STAT CARDS --}}
  <div class="stat-grid">
    <a class="stat-card navy" href="{{ route('staff.index') }}">
      <div class="stat-icon white">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-label">Total Staff</div>
      <div class="stat-value">{{ $counts['total'] }}</div>
      <div class="stat-sub">All registered members</div>
    </a>
    <a class="stat-card sky" href="{{ route('staff.index') }}">
      <div class="stat-icon white">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="stat-label">Doctors</div>
      <div class="stat-value">{{ $counts['doctors'] }}</div>
      <div class="stat-sub">Across all departments</div>
    </a>
    <a class="stat-card teal" href="{{ route('staff.index') }}">
      <div class="stat-icon white">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </div>
      <div class="stat-label">Nurses</div>
      <div class="stat-value">{{ $counts['nurses'] }}</div>
      <div class="stat-sub">Ward & ICU assigned</div>
    </a>
    <a class="stat-card" href="{{ route('staff.index') }}">
      <div class="stat-icon light">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2" stroke="var(--sky)"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="var(--sky)"/></svg>
      </div>
      <div class="stat-label">Admin Staff</div>
      <div class="stat-value">{{ $counts['admin'] }}</div>
      <div class="stat-sub">Front desk & records</div>
    </a>
  </div>

  {{-- MODULE CARDS --}}
  <div class="section-title">Hospital Modules</div>
  <div class="section-sub">Click a module to navigate directly to it</div>
  <div class="module-grid">
    <a class="module-card" href="{{ route('patients.index') }}" style="--accent-color:#4FC3F7">
      <div class="module-num">Module 1</div>
      <div class="module-icon" style="background:#E3F7FA">
        <svg viewBox="0 0 24 24" stroke="#0E8FAB"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="module-name">Patient Management</div>
      <div class="module-desc">Register patients, manage records, assign wards and track admissions.</div>
      <div class="module-tasks">
        <div class="module-task">Register & update patient info</div>
        <div class="module-task">Maintain medical records</div>
        <div class="module-task">Assign to wards & beds</div>
        <div class="module-task">Track admission & discharge</div>
      </div>
      <div class="module-arrow">Go to Module <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
    </a>
    <a class="module-card" href="{{ route('staff.index') }}" style="--accent-color:#f24c4c">
      <div class="module-num">Module 2</div>
      <div class="module-icon" style="background:#f6d3d3">
        <svg viewBox="0 0 24 24" stroke="#f24c4c"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="module-name">Staff & Department Management</div>
      <div class="module-desc">Manage all hospital staff records, schedules, and responsibilities.</div>
      <div class="module-tasks">
        <div class="module-task">Manage staff records</div>
        <div class="module-task">Assign to departments & wards</div>
        <div class="module-task">Maintain schedules & roles</div>
        <div class="module-task">Track patient responsibilities</div>
      </div>
      <div class="module-arrow">Go to Module <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
    </a>
    <a class="module-card" href="{{ route('wards.index') }}" style="--accent-color:#2E8B6A">
      <div class="module-num">Module 3</div>
      <div class="module-icon" style="background:#E3F7EF">
        <svg viewBox="0 0 24 24" stroke="#2E8B6A"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </div>
      <div class="module-name">Ward & Bed Management</div>
      <div class="module-desc">Monitor ward capacity, assign beds and track availability.</div>
      <div class="module-tasks">
        <div class="module-task">Maintain ward details</div>
        <div class="module-task">Manage bed allocation</div>
        <div class="module-task">Track occupied & vacant beds</div>
        <div class="module-task">Assign beds to patients</div>
      </div>
      <div class="module-arrow">Go to Module <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
    </a>
    <a class="module-card" href="{{ route('appointments.index') }}" style="--accent-color:#C88000">
      <div class="module-num">Module 4</div>
      <div class="module-icon" style="background:#FFF3E0">
        <svg viewBox="0 0 24 24" stroke="#C88000"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      </div>
      <div class="module-name">Appointment & Treatment</div>
      <div class="module-desc">Schedule appointments, record diagnoses and assign doctors.</div>
      <div class="module-tasks">
        <div class="module-task">Schedule appointments</div>
        <div class="module-task">Record treatments & diagnoses</div>
        <div class="module-task">Maintain treatment history</div>
        <div class="module-task">Assign doctors & nurses</div>
      </div>
      <div class="module-arrow">Go to Module <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
    </a>
    <a class="module-card" href="{{ route('billing.index') }}" style="--accent-color:#7B3FA0">
      <div class="module-num">Module 5</div>
      <div class="module-icon" style="background:#EDE7FE">
        <svg viewBox="0 0 24 24" fill="none" stroke="#7B3FA0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="5" y="2" width="14" height="20" rx="2"/>
          <line x1="9" y1="7" x2="15" y2="7"/>
          <line x1="9" y1="11" x2="15" y2="11"/>
          <line x1="9" y1="15" x2="12" y2="15"/>
          <line x1="12" y1="1" x2="12" y2="4"/>
        </svg>
      </div>
      <div class="module-name">Billing & Reporting</div>
      <div class="module-desc">Generate patient bills, track payments and produce hospital reports.</div>
      <div class="module-tasks">
        <div class="module-task">Generate patient bills</div>
        <div class="module-task">Track payments & balances</div>
        <div class="module-task">Produce occupancy reports</div>
        <div class="module-task">Generate hospital summaries</div>
      </div>
      <div class="module-arrow">Go to Module <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
    </a>
  </div>

  {{-- BOTTOM ROW --}}
  <div class="bottom-grid">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Recent Staff</div>
        <a href="{{ route('staff.index') }}" class="card-link">View all →</a>
      </div>
      <div class="card-body">
        @php $colors = [['#E3EFFE','#1B4FA8'],['#E3F7EF','#1B7A54'],['#EDE7FE','#7B3FA0'],['#FFF3E0','#A06000'],['#FDEDEC','#C0392B'],['#D6EAFA','#185FA5']]; @endphp
        @foreach($recentStaff as $s)
        @php $c = $colors[$s->id % 6]; @endphp
        <div class="staff-row">
          <div class="av" style="background:{{ $c[0] }};color:{{ $c[1] }}">{{ $s->initials }}</div>
          <div>
            <div class="staff-name">{{ $s->full_name }}</div>
            <div class="staff-sub">{{ $s->department }} · {{ $s->shift }} Shift</div>
          </div>
          <span class="role-pill pill-{{ strtolower($s->role) }}">{{ $s->role }}</span>
        </div>
        @endforeach
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="card-title">Department Summary</div>
        <a href="{{ route('staff.index') }}" class="card-link">View all →</a>
      </div>
      <div class="card-body">
        @foreach($deptSummary as $dept => $info)
        <div class="activity-row">
          <div class="act-icon" style="background:var(--sky-pale)">
            <svg viewBox="0 0 24 24" stroke="var(--sky)"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          </div>
          <div style="flex:1">
            <div class="act-text"><strong>{{ $dept }}</strong></div>
            <div class="act-time">{{ $info['doctors'] }} doctors · {{ $info['nurses'] }} nurses · {{ $info['total'] }} total</div>
          </div>
          <div style="font-size:18px;font-weight:600;color:var(--navy)">{{ $info['total'] }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  function updateClock() {
    const now = new Date();
    document.getElementById('hero-clock').textContent =
      now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    document.getElementById('hero-date').textContent =
      now.toLocaleDateString('en-US', { weekday:'short', month:'long', day:'numeric', year:'numeric' });
  }
  updateClock();
  setInterval(updateClock, 1000);
</script>
@endpush