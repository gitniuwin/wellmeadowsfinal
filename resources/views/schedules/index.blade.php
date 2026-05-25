@extends('layouts.app')

@section('title', 'Schedules')
@section('page-title', 'Schedules')

@section('content')
<div class="stat-grid">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Staff</div>
        <div class="stat-value">{{ $counts['total'] }}</div>
        <div class="stat-sub">Included in staff roster</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">AM Shift</div>
        <div class="stat-value">{{ $counts['am'] }}</div>
        <div class="stat-sub">Morning coverage</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">PM Shift</div>
        <div class="stat-value">{{ $counts['pm'] }}</div>
        <div class="stat-sub">Afternoon coverage</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Night Shift</div>
        <div class="stat-value">{{ $counts['night'] }}</div>
        <div class="stat-sub">Overnight coverage</div>
    </div>
</div>

<div class="section-header">
    <div class="section-title">Staff Schedules</div>
    <form method="GET" action="{{ route('schedules.index') }}" class="filter-form">
        <input class="search-input" type="text" name="search" value="{{ request('search') }}" placeholder="Search staff, ward, role">
        <select class="form-select" name="shift" style="width:140px">
            <option value="">All shifts</option>
            @foreach(['AM', 'PM', 'Night'] as $shift)
                <option value="{{ $shift }}" @selected(request('shift') === $shift)>{{ $shift }}</option>
            @endforeach
        </select>
        <button class="btn-search" type="submit">Filter</button>
        @if(request()->hasAny(['search', 'shift']))
            <a class="btn-secondary" href="{{ route('schedules.index') }}">Reset</a>
        @endif
    </form>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Staff</th>
                <th>Role</th>
                <th>Department</th>
                <th>Ward</th>
                <th>Shift</th>
                <th>Working Days</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staff as $member)
                @php $schedule = $member->schedule; @endphp
                <tr>
                    <td>
                        <strong>{{ $member->full_name }}</strong>
                        <div class="text-muted">{{ $member->email }}</div>
                    </td>
                    <td>{{ $member->role }}</td>
                    <td>{{ $member->department ?? 'Unassigned' }}</td>
                    <td>{{ $member->ward ?? 'Unassigned' }}</td>
                    <td><span class="tbl-status-badge reserved">{{ $member->shift ?? 'Unset' }}</span></td>
                    <td>
                        <div class="sched-days">
                            @foreach(['mon'=>'M','tue'=>'T','wed'=>'W','thu'=>'Th','fri'=>'F','sat'=>'Sa','sun'=>'Su'] as $key => $label)
                                <span class="day-pill {{ $schedule && $schedule->$key ? 'on' : '' }}">{{ $label }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <a class="btn-sm-blue" href="{{ route('staff.index') }}">Manage</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">No schedules found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $staff->links() }}
@endsection
