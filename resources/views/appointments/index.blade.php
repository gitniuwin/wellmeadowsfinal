@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointment & Treatment')

@section('topbar-action')
    <button class="btn-primary" type="button" onclick="document.getElementById('appointment-modal').classList.add('open')">
        New Appointment
    </button>
@endsection

@push('styles')
<style>
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(20,30,55,.55); z-index:200; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal { background:#fff; width:100%; max-width:560px; border-radius:10px; border:1px solid var(--border); padding:1.25rem; }
    .modal-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
    .modal-title { font-size:16px; font-weight:600; color:var(--text); }
    .modal-close { border:1px solid var(--border); background:var(--white); border-radius:6px; width:30px; height:30px; cursor:pointer; }
    .form-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:1rem; }
    .actions-cell { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
</style>
@endpush

@section('content')
<div class="tabs-bar">
    <a href="{{ route('appointments.index') }}" class="tab active">Appointments</a>
    <a href="{{ route('treatments.index') }}" class="tab">Treatments</a>
    <a href="{{ route('history.index') }}" class="tab">Patient History</a>
</div>

<div class="stat-grid">
    <div class="stat-card stat-total">
        <div class="stat-label">Total Appointments</div>
        <div class="stat-value">{{ $totalAppointments }}</div>
        <div class="stat-sub">All scheduled records</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Scheduled</div>
        <div class="stat-value">{{ $scheduled }}</div>
        <div class="stat-sub">Upcoming care</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed</div>
        <div class="stat-value">{{ $completed }}</div>
        <div class="stat-sub">Finished visits</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Cancelled</div>
        <div class="stat-value">{{ $cancelled }}</div>
        <div class="stat-sub">No longer active</div>
    </div>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                <tr>
                    <td>
                        <strong>{{ $appointment->patient->full_name }}</strong>
                        <div class="text-muted">{{ $appointment->patient->patient_number }}</div>
                    </td>
                    <td>{{ $appointment->doctor->full_name }}</td>
                    <td>{{ $appointment->appointment_date->format('M d, Y g:i A') }}</td>
                    <td>{{ $appointment->type }}</td>
                    <td><span class="tbl-status-badge reserved">{{ ucfirst($appointment->status) }}</span></td>
                    <td>{{ $appointment->notes ?? 'None' }}</td>
                    <td>
                        <div class="actions-cell">
                            @if($appointment->status !== 'completed')
                                <form method="POST" action="{{ route('appointments.status', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button class="btn-sm-green" type="submit">Complete</button>
                                </form>
                            @endif
                            @if($appointment->status !== 'cancelled')
                                <form method="POST" action="{{ route('appointments.status', $appointment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn-sm-gray" type="submit">Cancel</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Delete this appointment?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-sm-red" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">No appointments scheduled yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $appointments->links() }}

<div class="modal-overlay" id="appointment-modal">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-title">Schedule Appointment</div>
            <button class="modal-close" type="button" onclick="document.getElementById('appointment-modal').classList.remove('open')">x</button>
        </div>
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Patient <span class="required">*</span></label>
                    <select class="form-select" name="patient_id" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Doctor <span class="required">*</span></label>
                    <select class="form-select" name="doctor_id" required>
                        <option value="">Select doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date and Time <span class="required">*</span></label>
                    <input class="form-input" type="datetime-local" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type <span class="required">*</span></label>
                    <select class="form-select" name="type" required>
                        <option>Consultation</option>
                        <option>Follow-up</option>
                        <option>Emergency</option>
                        <option>Routine Check</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-textarea" name="notes" rows="3"></textarea>
            </div>
            <div class="form-actions">
                <button class="btn-secondary" type="button" onclick="document.getElementById('appointment-modal').classList.remove('open')">Cancel</button>
                <button class="btn-primary" type="submit">Save Appointment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) modal.classList.remove('open');
        });
    });

    @if($errors->any())
        document.getElementById('appointment-modal').classList.add('open');
    @endif
</script>
@endpush
