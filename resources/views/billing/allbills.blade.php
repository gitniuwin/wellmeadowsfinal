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

@if(session('success'))
<div style="background:#E3F7EF;border:1px solid #9FE1CB;color:#1B7A54;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;">
    ✓ {{ session('success') }}
</div>
@endif

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
        <div class="stat-label">Payments Received</div>
        <div class="stat-value">₱{{ number_format($stats['paid'], 0) }}</div>
        <div class="stat-sub">Collected</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Outstanding</div>
        <div class="stat-value">₱{{ number_format($stats['outstanding'], 0) }}</div>
        <div class="stat-sub red">Unpaid balance</div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <span class="table-title">All Bills</span>
        <span class="table-count">{{ $bills->count() }} records</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bills as $bill)
            <tr>
                <td style="color:var(--muted);font-size:12px;">{{ $bill->id }}</td>
                <td style="font-weight:500;">{{ $bill->patient_name }}</td>
                <td><span class="svc-badge">{{ ucfirst($bill->service_type) }}</span></td>
                <td style="font-weight:500;">₱{{ number_format($bill->total_amount, 2) }}</td>
                <td style="color:#1B7A54;font-weight:500;">₱{{ number_format($bill->amount_paid, 2) }}</td>
                <td style="font-weight:500;{{ $bill->remaining_balance > 0 ? 'color:#B03030;' : 'color:#1B7A54;' }}">
                    ₱{{ number_format($bill->remaining_balance, 2) }}
                </td>
                <td style="color:var(--muted);">{{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</td>
                <td>
                    @if($bill->status === 'paid')
                        <span class="badge badge-paid">Paid</span>
                    @elseif($bill->status === 'overdue')
                        <span class="badge badge-overdue">Overdue</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        @if($bill->status !== 'paid')
                            <a href="/payments?bill_id={{ $bill->id }}" class="action-btn pay">Pay</a>
                        @endif
                        <form method="POST" action="/billing/{{ $bill->id }}" onsubmit="return confirm('Delete this bill?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn del">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="empty-state">No bills yet. <a href="/billing/create">Create the first one →</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection