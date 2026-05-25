@extends('layouts.app')
@section('page-title', 'Treatment History')

@push('styles')
<style>
  :root {
    --navy:#1B2D5B; --navy-dark:#111e3f; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-pale:#D6EAFA; --off-white:#F4F8FC;
    --muted:#6B7E9F; --border:#C8D9EE; --text:#1a2640;
  }
  .history-wrap { padding:28px; display:grid; grid-template-columns:320px 1fr; gap:20px; }
  .card { background:white; border:1px solid var(--border); border-radius:12px; overflow:hidden; }
  .card.pad { padding:20px; }
  .card-header { border-bottom:1px solid var(--border); padding:16px 20px; }
  .card-title { font-size:14px; font-weight:700; color:var(--navy); }
  .search-wrap { position:relative; margin-bottom:14px; }
  .search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; stroke:var(--muted); fill:none; pointer-events:none; }
  .search-input { width:100%; padding:9px 12px 9px 32px; border:1px solid var(--border); border-radius:8px; font-size:13px; background:var(--off-white); color:var(--text); outline:none; }
  .search-input:focus { border-color:var(--sky); background:white; }
  .patient-list { display:flex; flex-direction:column; gap:8px; max-height:520px; overflow-y:auto; padding-right:4px; }
  .patient-link { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; color:var(--text); text-decoration:none; border:1px solid transparent; }
  .patient-link:hover, .patient-link.active { background:var(--sky-pale); border-color:var(--border); }
  .patient-avatar { width:32px; height:32px; border-radius:50%; background:var(--off-white); color:var(--navy); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
  .patient-name { font-size:13px; font-weight:600; color:var(--navy-dark); }
  .patient-id { font-size:11px; color:var(--muted); margin-top:2px; }
  .patient-banner { background:var(--navy); color:white; border-radius:12px; padding:20px; display:flex; align-items:center; gap:14px; margin-bottom:20px; }
  .banner-avatar { width:48px; height:48px; border-radius:50%; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; font-weight:700; }
  .banner-name { font-size:20px; font-weight:700; }
  .banner-sub { color:rgba(255,255,255,0.62); font-size:12px; margin-top:2px; }
  .banner-count { margin-left:auto; text-align:right; }
  .banner-count-value { font-size:26px; font-weight:700; line-height:1; }
  .banner-count-label { color:rgba(255,255,255,0.62); font-size:11px; margin-top:4px; }
  .timeline { padding:20px; }
  .timeline-item { position:relative; padding-left:28px; padding-bottom:24px; }
  .timeline-item:last-child { padding-bottom:0; }
  .timeline-dot { position:absolute; left:0; top:5px; width:13px; height:13px; border-radius:50%; border:2px solid var(--navy); background:white; }
  .timeline-line { position:absolute; left:6px; top:20px; bottom:0; width:1px; background:var(--border); }
  .record-card { background:var(--off-white); border:1px solid transparent; border-radius:10px; padding:14px 16px; }
  .record-card:hover { border-color:var(--border); background:var(--sky-pale); }
  .record-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:8px; }
  .record-type { display:inline-flex; padding:3px 10px; border-radius:20px; background:var(--navy); color:white; font-size:11px; font-weight:600; margin-bottom:6px; }
  .record-title { color:var(--navy-dark); font-size:14px; font-weight:700; }
  .record-date { color:var(--muted); font-size:12px; white-space:nowrap; }
  .record-text { color:var(--text); font-size:13px; margin-top:6px; }
  .record-meta { color:var(--muted); font-size:12px; margin-top:8px; }
  .empty-state { text-align:center; padding:70px 20px; color:var(--muted); }
  .empty-state svg { width:48px; height:48px; stroke:var(--border); fill:none; margin:0 auto 12px; }
  @media (max-width: 900px) { .history-wrap { grid-template-columns:1fr; padding:18px 0; } .patient-list { max-height:none; } }
</style>
@endpush

@section('content')
<div class="history-wrap">
  <div class="card pad">
    <div class="card-title" style="margin-bottom:14px;">Patient History Lookup</div>
    <div class="search-wrap">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="patientSearch" placeholder="Search by name or ID..." class="search-input">
    </div>

    <div class="patient-list" id="patientList">
      @forelse($patients ?? [] as $patient)
        <a href="{{ route('history.index', ['patient_id' => $patient->id]) }}" class="patient-link {{ (request('patient_id') == $patient->id) ? 'active' : '' }}">
          <div class="patient-avatar">{{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}</div>
          <div>
            <div class="patient-name">{{ $patient->full_name }}</div>
            <div class="patient-id">ID #{{ $patient->id }}</div>
          </div>
        </a>
      @empty
        <div class="empty-state" style="padding:30px 10px;">No patients found.</div>
      @endforelse
    </div>
  </div>

  <div>
    @if(isset($selectedPatient))
      <div class="patient-banner">
        <div class="banner-avatar">{{ strtoupper(substr($selectedPatient->first_name, 0, 1) . substr($selectedPatient->last_name, 0, 1)) }}</div>
        <div>
          <div class="banner-name">{{ $selectedPatient->full_name }}</div>
          <div class="banner-sub">Patient ID #{{ $selectedPatient->id }}</div>
        </div>
        <div class="banner-count">
          <div class="banner-count-value">{{ $history->count() }}</div>
          <div class="banner-count-label">Total Records</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Treatment History</div>
        </div>
        <div class="timeline">
          @forelse($history ?? [] as $record)
            <div class="timeline-item">
              <div class="timeline-dot"></div>
              @if(!$loop->last)
                <div class="timeline-line"></div>
              @endif
              <div class="record-card">
                <div class="record-head">
                  <div>
                    <span class="record-type">{{ $record->type ?? 'Treatment' }}</span>
                    <div class="record-title">{{ $record->diagnosis ?? $record->title }}</div>
                  </div>
                  <div class="record-date">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</div>
                </div>
                @if($record->procedure ?? false)
                  <div class="record-text"><strong>Procedure:</strong> {{ $record->procedure }}</div>
                @endif
                @if($record->notes ?? false)
                  <div class="record-text">{{ $record->notes }}</div>
                @endif
                <div class="record-meta">Attending: {{ $record->doctor->full_name ?? 'N/A' }}</div>
              </div>
            </div>
          @empty
            <div class="empty-state">
              <svg viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <p>No history records found.</p>
            </div>
          @endforelse
        </div>
      </div>
    @else
      <div class="card">
        <div class="empty-state">
          <svg viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <p style="font-weight:600;color:var(--text);">Select a patient</p>
          <p style="font-size:13px;margin-top:4px;">Choose a patient from the list to view their treatment history.</p>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('patientSearch')?.addEventListener('input', event => {
    const value = event.target.value.toLowerCase();
    document.querySelectorAll('#patientList .patient-link').forEach(link => {
      link.style.display = link.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
  });
</script>
@endpush
