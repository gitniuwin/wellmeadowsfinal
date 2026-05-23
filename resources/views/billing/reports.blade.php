@extends('layouts.app')
@section('page-title', 'Reports')

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

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">₱{{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Bills Generated</div>
        <div class="stat-value">{{ $stats['bills_generated'] }}</div>
        <div class="stat-sub">Total invoices</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Collected</div>
        <div class="stat-value">₱{{ number_format($stats['paid'], 0) }}</div>
        <div class="stat-sub green">{{ $stats['collection_rate'] }}% collection rate</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Outstanding</div>
        <div class="stat-value">₱{{ number_format($stats['outstanding'], 0) }}</div>
        <div class="stat-sub red">{{ $stats['overdue_count'] }} overdue</div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <span class="table-title">Bills by Service Type</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Service Type</th>
                <th>No. of Bills</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byService as $row)
            <tr>
                <td><span class="svc-badge">{{ ucfirst($row->service_type) }}</span></td>
                <td>{{ $row->count }}</td>
                <td style="font-weight:500;">₱{{ number_format($row->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="empty-state">No data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="table-card">
    <div class="table-header">
        <span class="table-title">Outstanding Bills</span>
        <span class="table-count">{{ $outstanding->count() }} unpaid</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outstanding as $bill)
            <tr>
                <td style="font-weight:500;">{{ $bill->patient_name }}</td>
                <td><span class="svc-badge">{{ ucfirst($bill->service_type) }}</span></td>
                <td style="font-weight:500;">₱{{ number_format($bill->total_amount, 2) }}</td>
                <td style="color:var(--muted);">{{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</td>
                <td>
                    @if($bill->status === 'overdue')
                        <span class="badge badge-overdue">Overdue</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </td>
                <td>
                    <a href="/payments?bill_id={{ $bill->id }}" class="action-btn pay">Record Payment</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state">No outstanding bills.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
