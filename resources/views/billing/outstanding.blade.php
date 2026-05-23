@extends('layouts.app')
@section('page-title', 'Outstanding Bills')

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

<div class="table-card">
    <div class="table-header">
        <span class="table-title">Outstanding Balances</span>
        <span class="table-count">{{ $bills->count() }} unpaid</span>
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
            @forelse($bills as $bill)
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
            <tr><td colspan="6" class="empty-state">No outstanding bills. All caught up!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
