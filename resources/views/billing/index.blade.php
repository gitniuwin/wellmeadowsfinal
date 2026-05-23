@extends('layouts.app')
@section('page-title', 'Billing & Reporting')

@section('topbar-action')
    <a href="/billing/create" class="add-btn">
        <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Bill
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

{{-- STAT CARDS --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">₱{{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="stat-sub">All time collected</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Bills Generated</div>
        <div class="stat-value">{{ $stats['bills_generated'] }}</div>
        <div class="stat-sub">Total invoices</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Payments Received</div>
        <div class="stat-value">₱{{ number_format($stats['paid'], 0) }}</div>
        <div class="stat-sub green">Fully settled</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Outstanding</div>
        <div class="stat-value">₱{{ number_format($stats['outstanding'], 0) }}</div>
        <div class="stat-sub red">Pending &amp; overdue</div>
    </div>
</div>

{{-- MONTHLY OVERVIEW --}}
<div class="table-card">
    <div class="table-header">
        <span class="table-title">Monthly Overview</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Bills</th>
                <th>Revenue</th>
                <th>Collected</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthly as $row)
            <tr>
                <td>{{ $row->month }}</td>
                <td>{{ $row->count }}</td>
                <td style="font-weight:500;">₱{{ number_format($row->total, 2) }}</td>
                <td><span style="color:#1B7A54;font-weight:500;">₱{{ number_format($row->collected, 2) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">No monthly data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- RECENT ACTIVITIES --}}
<div class="table-card">
    <div class="table-header">
        <span class="table-title">Recent Activities</span>
        <a href="/billing/all" class="view-all-link">View all →</a>
    </div>
    @forelse($recent as $bill)
    <div class="activity-row">
        <div>
            <div class="activity-name">{{ $bill->patient_name }}</div>
            <div class="activity-sub">{{ ucfirst($bill->service_type) }} · {{ $bill->created_at->format('M d, Y') }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;">
            <div class="activity-amount">₱{{ number_format($bill->total_amount, 2) }}</div>
            @if($bill->status === 'paid')
                <span class="badge badge-paid">Paid</span>
            @elseif($bill->status === 'pending')
                <span class="badge badge-pending">Pending</span>
            @else
                <span class="badge badge-overdue">Overdue</span>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">No recent activity.</div>
    @endforelse
</div>

@endsection
