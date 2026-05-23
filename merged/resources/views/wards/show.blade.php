@extends('layouts.app')

@section('title', $ward->name . ' — Ward Detail')
@section('page-title', $ward->name)

@section('topbar-action')
    <a href="{{ route('wards.edit', $ward) }}" class="btn-secondary">Edit Ward</a>
    <a href="{{ route('wards.index') }}" class="btn-secondary">← Back</a>
@endsection

@section('content')

<div class="ward-detail-header">
    <div class="ward-detail-meta">
        <span class="badge-type">{{ $ward->type }}</span>
        @if($ward->floor) <span class="badge-floor">Floor {{ $ward->floor }}</span> @endif
        @if($ward->building) <span class="badge-floor">{{ $ward->building }}</span> @endif
        <span class="availability-badge badge-{{ $ward->availability_status }}">{{ ucfirst($ward->availability_status) }}</span>
    </div>
    @if($ward->description)
        <p class="ward-desc">{{ $ward->description }}</p>
    @endif
</div>

{{-- Summary row --}}
<div class="stat-grid" style="grid-template-columns: repeat(4,1fr); margin-bottom:1.5rem">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Beds</div>
        <div class="stat-value">{{ $ward->capacity }}</div>
    </div>
    <div class="stat-card stat-occupied">
        <div class="stat-label">Occupied</div>
        <div class="stat-value">{{ $ward->beds->where('status','occupied')->count() }}</div>
    </div>
    <div class="stat-card stat-vacant">
        <div class="stat-label">Vacant</div>
        <div class="stat-value">{{ $ward->beds->where('status','vacant')->count() }}</div>
    </div>
    <div class="stat-card stat-other">
        <div class="stat-label">Reserved / Maint.</div>
        <div class="stat-value">{{ $ward->beds->whereIn('status',['reserved','maintenance'])->count() }}</div>
    </div>
</div>

{{-- Beds Table --}}
<div class="table-card">
    <div class="section-header" style="padding:1rem 1.25rem 0">
        <h2 class="section-title">All Beds</h2>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Bed #</th>
                <th>Status</th>
                <th>Patient</th>
                <th>Assigned At</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ward->beds->sortBy('bed_number') as $bed)
            <tr>
                <td><strong>{{ $bed->bed_number }}</strong></td>
                <td>
                    <span class="tbl-status-badge {{ $bed->status }}">{{ ucfirst($bed->status) }}</span>
                </td>
                <td>
                    @if($bed->patient)
                        {{ $bed->patient->full_name }}
                        <span class="patient-num">({{ $bed->patient->patient_number }})</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $bed->assigned_at ? $bed->assigned_at->format('M j, Y g:i A') : '—' }}</td>
                <td>{{ $bed->notes ?? '—' }}</td>
                <td class="actions-cell">
                    @if($bed->status === 'occupied')
                        <form method="POST" action="{{ route('wards.beds.release', $bed) }}" style="display:inline"
                              onsubmit="return confirm('Release this bed? Patient will be marked as discharged.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm-red">Release</button>
                        </form>
                    @elseif($bed->status === 'vacant')
                        <button class="btn-sm-blue"
                                onclick="openAssignFromDetail({{ $bed->id }}, '{{ $bed->bed_number }}')">
                            Assign
                        </button>
                        <form method="POST" action="{{ route('wards.beds.status', $bed) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="maintenance">
                            <button type="submit" class="btn-sm-gray">→ Maintenance</button>
                        </form>
                        <form method="POST" action="{{ route('wards.beds.status', $bed) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="reserved">
                            <button type="submit" class="btn-sm-gray">→ Reserve</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('wards.beds.status', $bed) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="vacant">
                            <button type="submit" class="btn-sm-green">→ Vacant</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Assign modal for detail page --}}
<div id="detail-assign-modal" class="modal-overlay" style="display:none">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="detail-modal-title">Assign Patient to Bed</h3>
            <button class="modal-close" onclick="closeDetailModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('wards.beds.assign') }}">
            @csrf
            <input type="hidden" name="bed_id" id="detail-bed-id">
            <div class="form-group" style="margin-bottom:1.25rem">
                <label class="form-label">Select Patient (admitted, unassigned)</label>
                <select name="patient_id" class="form-select" required>
                    <option value="">— Choose patient —</option>
                    @foreach($admittedPatients as $p)
                        <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_number }})</option>
                    @endforeach
                </select>
                @if($admittedPatients->isEmpty())
                    <p class="form-hint">No admitted patients without a bed assignment.</p>
                @endif
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary" style="width:100%"
                        @if($admittedPatients->isEmpty()) disabled @endif>
                    Confirm Assignment
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAssignFromDetail(bedId, bedNumber) {
    document.getElementById('detail-bed-id').value = bedId;
    document.getElementById('detail-modal-title').textContent = 'Assign Patient — Bed ' + bedNumber;
    document.getElementById('detail-assign-modal').style.display = 'flex';
}
function closeDetailModal() {
    document.getElementById('detail-assign-modal').style.display = 'none';
}
document.getElementById('detail-assign-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});
</script>
@endpush
