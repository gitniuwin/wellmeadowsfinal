@extends('layouts.app')
@section('page-title', 'New Bill')

@section('topbar-action')
    <a href="/billing/all" class="back-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Back
    </a>
@endsection

@section('content')
{{-- TAB BAR --}}
@php $path = request()->path(); @endphp
<div class="tab-bar">
    <a href="/billing"       class="tab {{ $path === 'billing'                   ? 'active' : '' }}">Summary</a>
    <a href="/billing/all"   class="tab {{ str_starts_with($path,'billing/all') || str_starts_with($path,'billing/create') ? 'active' : '' }}">All Bills</a>
    <a href="/payments"      class="tab {{ str_starts_with($path,'payment')      ? 'active' : '' }}">Payments</a>
    <a href="/outstanding"   class="tab {{ str_starts_with($path,'outstanding')  ? 'active' : '' }}">Outstanding</a>
    <a href="/reports"       class="tab {{ str_starts_with($path,'report')       ? 'active' : '' }}">Reports</a>
</div>

<div class="form-card">
    <div class="form-title">New Bill</div>

    @if($errors->any())
    <div style="background:#FFF0F0;border:1px solid #f5c5c5;color:#B03030;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $error)
            <div>✗ {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="/billing">
        @csrf
        <div class="form-group">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-input" required>
            <option value="">— Select a patient —</option>
            @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                {{ $patient->full_name }} ({{ $patient->patient_number }})
            </option>
            @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Service Type</label>
                <select name="service_type" class="form-input">
                    <option value="room"      {{ old('service_type') == 'room'      ? 'selected' : '' }}>Room</option>
                    <option value="treatment" {{ old('service_type') == 'treatment' ? 'selected' : '' }}>Treatment</option>
                    <option value="services"  {{ old('service_type') == 'services'  ? 'selected' : '' }}>Services</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" required class="form-input"
                       value="{{ old('due_date') }}">
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Status is set automatically from the due date.</div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Total Amount (₱)</label>
            <input type="number" name="total_amount" step="0.01" min="0.01" required
                   placeholder="e.g. 12500.00" value="{{ old('total_amount') }}" class="form-input">
        </div>
        <div class="btn-group">
            <button type="submit" class="btn-primary">Create Bill</button>
            <a href="/billing" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@endsection