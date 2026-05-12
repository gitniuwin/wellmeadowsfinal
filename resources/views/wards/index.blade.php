@extends('layouts.app')

@section('title', 'Ward & Bed Management')
@section('page-title', 'Ward & Bed Management')

@section('topbar-action')
    <a href="{{ route('wards.create') }}" class="btn-primary">+ Add Ward</a>
@endsection

@section('content')

{{-- ══════════════════════════════════════
     TABS
══════════════════════════════════════ --}}
<div class="tabs-bar">
    <button class="tab active" onclick="showTab('overview', this)">Ward Overview</button>
    <button class="tab" onclick="showTab('allocation', this)">Bed Allocation</button>
    <button class="tab" onclick="showTab('assign', this)">Assign Bed</button>
    <button class="tab" onclick="showTab('maintenance', this)">Maintenance</button>
</div>

{{-- ══════════════════════════════════════
     TAB: WARD OVERVIEW
══════════════════════════════════════ --}}
<div id="tab-overview" class="tab-content active">

    {{-- STAT CARDS --}}
    <div class="stat-grid">
        <div class="stat-card stat-total">
            <div class="stat-label">Total Beds</div>
            <div class="stat-value">{{ $totalBeds }}</div>
            <div class="stat-sub">Across {{ $wards->count() }} wards</div>
        </div>
        <div class="stat-card stat-occupied">
            <div class="stat-label">Occupied</div>
            <div class="stat-value">{{ $occupiedBeds }}</div>
            <div class="stat-sub">
                {{ $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0 }}% occupancy rate
            </div>
        </div>
        <div class="stat-card stat-vacant">
            <div class="stat-label">Vacant</div>
            <div class="stat-value">{{ $vacantBeds }}</div>
            <div class="stat-sub">Available now</div>
        </div>
        <div class="stat-card stat-other">
            <div class="stat-label">Reserved / Maintenance</div>
            <div class="stat-value">{{ $otherBeds }}</div>
            <div class="stat-sub">Temporarily unavailable</div>
        </div>
    </div>

    {{-- FILTER + SEARCH --}}
    <div class="section-header">
        <h2 class="section-title">Ward Directory</h2>
        <form method="GET" action="{{ route('wards.index') }}" class="filter-form">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search ward..." class="search-input">
            <div class="filter-group">
                <a href="{{ route('wards.index', ['filter' => 'all', 'search' => $search]) }}"
                   class="filter-btn {{ $filter === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('wards.index', ['filter' => 'available', 'search' => $search]) }}"
                   class="filter-btn {{ $filter === 'available' ? 'active' : '' }}">Available</a>
                <a href="{{ route('wards.index', ['filter' => 'limited', 'search' => $search]) }}"
                   class="filter-btn {{ $filter === 'limited' ? 'active' : '' }}">Limited</a>
                <a href="{{ route('wards.index', ['filter' => 'full', 'search' => $search]) }}"
                   class="filter-btn {{ $filter === 'full' ? 'active' : '' }}">Full</a>
            </div>
            <button type="submit" class="btn-search">Search</button>
        </form>
    </div>

    {{-- BED LEGEND --}}
    <div class="bed-legend">
        <span class="legend-item"><span class="bed-dot occupied"></span> Occupied</span>
        <span class="legend-item"><span class="bed-dot vacant"></span> Vacant</span>
        <span class="legend-item"><span class="bed-dot reserved"></span> Reserved</span>
        <span class="legend-item"><span class="bed-dot maintenance"></span> Maintenance</span>
    </div>

    {{-- WARD CARDS GRID --}}
    @if($wards->isEmpty())
        <div class="empty-state">
            <p>No wards found. <a href="{{ route('wards.create') }}">Add a ward</a> to get started.</p>
        </div>
    @else
    <div class="ward-grid">
        @foreach($wards as $ward)
            @php
                $occupied    = $ward->beds->where('status', 'occupied')->count();
                $vacant      = $ward->beds->where('status', 'vacant')->count();
                $reserved    = $ward->beds->where('status', 'reserved')->count();
                $maintenance = $ward->beds->where('status', 'maintenance')->count();
                $pct         = $ward->occupancy_percentage;
                $status      = $ward->availability_status;
                $barClass    = $pct >= 90 ? 'fill-high' : ($pct >= 70 ? 'fill-mid' : 'fill-low');
            @endphp
            <div class="ward-card">
                <div class="ward-card-header">
                    <div>
                        <div class="ward-name">{{ $ward->name }}</div>
                        <div class="ward-meta">{{ $ward->type }} &nbsp;·&nbsp; Capacity: {{ $ward->capacity }}
                            @if($ward->floor) &nbsp;·&nbsp; Floor {{ $ward->floor }} @endif
                        </div>
                    </div>
                    <span class="availability-badge badge-{{ $status }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                {{-- Occupancy bar --}}
                <div class="bed-bar-wrap">
                    <div class="bed-bar-meta">
                        <span>Bed usage</span>
                        <span>{{ $occupied }} / {{ $ward->capacity }}</span>
                    </div>
                    <div class="bed-bar-track">
                        <div class="bed-bar-fill {{ $barClass }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Visual bed grid --}}
                <div class="bed-icon-grid">
                    @foreach($ward->beds as $bed)
                        <div class="bed-icon {{ $bed->status }}"
                             title="Bed {{ $bed->bed_number }} — {{ ucfirst($bed->status) }}{{ $bed->patient ? ' · ' . $bed->patient->full_name : '' }}">
                        </div>
                    @endforeach
                </div>

                {{-- Quick stats row --}}
                <div class="ward-quick-stats">
                    <span class="qs qs-occupied">{{ $occupied }} occupied</span>
                    <span class="qs qs-vacant">{{ $vacant }} vacant</span>
                    @if($reserved > 0)<span class="qs qs-reserved">{{ $reserved }} reserved</span>@endif
                    @if($maintenance > 0)<span class="qs qs-maintenance">{{ $maintenance }} maint.</span>@endif
                </div>

                <div class="ward-card-footer">
                    @if($status !== 'full')
                        <button class="btn-assign-quick"
                                onclick="openQuickAssign({{ $ward->id }}, '{{ $ward->name }}')">
                            Assign Bed
                        </button>
                    @else
                        <button class="btn-assign-quick disabled" disabled>Ward Full</button>
                    @endif
                    <a href="{{ route('wards.show', $ward) }}" class="btn-view-ward">View Details</a>
                    <a href="{{ route('wards.edit', $ward) }}" class="btn-edit-ward">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════
     TAB: BED ALLOCATION TABLE
══════════════════════════════════════ --}}
<div id="tab-allocation" class="tab-content">
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ward</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Occupied</th>
                    <th>Vacant</th>
                    <th>Reserved</th>
                    <th>Maintenance</th>
                    <th>Occupancy</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wards as $ward)
                    @php
                        $occ  = $ward->beds->where('status','occupied')->count();
                        $vac  = $ward->beds->where('status','vacant')->count();
                        $res  = $ward->beds->where('status','reserved')->count();
                        $mnt  = $ward->beds->where('status','maintenance')->count();
                        $pct  = $ward->occupancy_percentage;
                    @endphp
                    <tr>
                        <td><a href="{{ route('wards.show', $ward) }}" class="link-ward">{{ $ward->name }}</a></td>
                        <td>{{ $ward->type }}</td>
                        <td>{{ $ward->capacity }}</td>
                        <td><span class="tbl-badge occupied">{{ $occ }}</span></td>
                        <td><span class="tbl-badge vacant">{{ $vac }}</span></td>
                        <td><span class="tbl-badge reserved">{{ $res }}</span></td>
                        <td><span class="tbl-badge maintenance">{{ $mnt }}</span></td>
                        <td>
                            <div class="mini-bar">
                                <div class="mini-fill {{ $pct >= 90 ? 'fill-high' : ($pct >= 70 ? 'fill-mid' : 'fill-low') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="pct-label">{{ $pct }}%</span>
                        </td>
                        <td><span class="availability-badge badge-{{ $ward->availability_status }}">{{ ucfirst($ward->availability_status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════
     TAB: ASSIGN BED FORM
══════════════════════════════════════ --}}
<div id="tab-assign" class="tab-content">
    <div class="form-card">
        <h3 class="form-card-title">Assign Bed to Patient</h3>
        <form method="POST" action="{{ route('wards.beds.assign') }}" class="assign-form">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Select Ward</label>
                    <select name="ward_id" id="ward-select" class="form-select" onchange="loadBeds(this.value)" required>
                        <option value="">— Choose ward —</option>
                        @foreach($wards as $ward)
                            @if($ward->availability_status !== 'full')
                                <option value="{{ $ward->id }}">
                                    {{ $ward->name }} ({{ $ward->beds->where('status','vacant')->count() }} beds free)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Bed</label>
                    <select name="bed_id" id="bed-select" class="form-select" required>
                        <option value="">— Choose ward first —</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Select Patient</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">— Choose patient —</option>
                        @php
                            $unassignedPatients = \App\Models\Patient::where('is_admitted', true)
                                ->whereDoesntHave('bed')->get();
                        @endphp
                        @foreach($unassignedPatients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->full_name }} ({{ $patient->patient_number }})
                            </option>
                        @endforeach
                    </select>
                    @if($unassignedPatients->isEmpty())
                        <p class="form-hint">No admitted patients without a bed assignment.</p>
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary" @if($unassignedPatients->isEmpty()) disabled @endif>
                    Assign Bed
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════
     TAB: MAINTENANCE BEDS
══════════════════════════════════════ --}}
<div id="tab-maintenance" class="tab-content">
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ward</th>
                    <th>Bed #</th>
                    <th>Current Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $maintenanceBeds = \App\Models\Bed::with('ward')
                        ->whereIn('status', ['maintenance', 'reserved'])
                        ->get();
                @endphp
                @forelse($maintenanceBeds as $bed)
                    <tr>
                        <td>{{ $bed->ward->name }}</td>
                        <td><strong>{{ $bed->bed_number }}</strong></td>
                        <td><span class="availability-badge badge-{{ $bed->status === 'maintenance' ? 'full' : 'limited' }}">
                            {{ ucfirst($bed->status) }}
                        </span></td>
                        <td>{{ $bed->notes ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('wards.beds.status', $bed) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="vacant">
                                <button type="submit" class="btn-sm-green">Mark Vacant</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">No beds under maintenance or reservation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════
     QUICK ASSIGN MODAL
══════════════════════════════════════ --}}
<div id="quick-assign-modal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-ward-name">Assign Bed</h3>
            <button class="modal-close" onclick="closeQuickAssign()">✕</button>
        </div>
        <form method="POST" action="{{ route('wards.beds.assign') }}">
            @csrf
            <input type="hidden" name="ward_id" id="modal-ward-id">
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">Select Bed</label>
                <select name="bed_id" id="modal-bed-select" class="form-select" required>
                    <option value="">Loading...</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.25rem">
                <label class="form-label">Select Patient</label>
                <select name="patient_id" class="form-select" required>
                    <option value="">— Choose patient —</option>
                    @foreach($unassignedPatients ?? [] as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->patient_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary" style="width:100%">Confirm Assignment</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Tab switching
function showTab(name, el) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    el.classList.add('active');
}

// Load vacant beds for a ward via inline data
const wardBeds = @json($wards->mapWithKeys(fn($w) => [
    $w->id => $w->beds->where('status','vacant')->values()->map(fn($b) => ['id' => $b->id, 'number' => $b->bed_number])
]));

function loadBeds(wardId) {
    const select = document.getElementById('bed-select');
    select.innerHTML = '<option value="">— Choose bed —</option>';
    if (wardBeds[wardId]) {
        wardBeds[wardId].forEach(b => {
            select.innerHTML += `<option value="${b.id}">Bed ${b.number}</option>`;
        });
    }
}

// Quick assign modal
function openQuickAssign(wardId, wardName) {
    document.getElementById('modal-ward-id').value = wardId;
    document.getElementById('modal-ward-name').textContent = 'Assign Bed — ' + wardName;
    const sel = document.getElementById('modal-bed-select');
    sel.innerHTML = '<option value="">— Choose bed —</option>';
    if (wardBeds[wardId]) {
        wardBeds[wardId].forEach(b => {
            sel.innerHTML += `<option value="${b.id}">Bed ${b.number}</option>`;
        });
    }
    document.getElementById('quick-assign-modal').style.display = 'flex';
}

function closeQuickAssign() {
    document.getElementById('quick-assign-modal').style.display = 'none';
}

// Close modal on overlay click
document.getElementById('quick-assign-modal').addEventListener('click', function(e) {
    if (e.target === this) closeQuickAssign();
});
</script>
@endpush
