@extends('layouts.app')
@section('page-title', 'Payments')

@section('topbar-action')
    <a href="/billing" class="back-btn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Bills
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

@if($errors->any())
<div style="background:#FFF0F0;border:1px solid #f5c5c5;color:#B03030;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;">
    ✗ {{ $errors->first() }}
</div>
@endif

<div class="form-card">
    <div class="form-title">Record a Payment</div>

    @if($bills->isEmpty())
        <p style="font-size:13px;color:var(--muted);padding:8px 0;">
            No outstanding bills to pay. All bills have been settled.
        </p>
    @else
    <form method="POST" action="/payments" id="payment-form">
        @csrf
        <div class="form-group">
            <label class="form-label">Bill</label>
            <select name="bill_id" class="form-input" id="bill-select" onchange="updateBalance()" required>
                @foreach($bills as $bill)
                    <option value="{{ $bill->id }}"
                        data-remaining="{{ $bill->remaining_balance }}"
                        data-total="{{ $bill->total_amount }}"
                        data-paid="{{ $bill->amount_paid }}"
                        {{ old('bill_id', request('bill_id')) == $bill->id ? 'selected' : '' }}>
                        #{{ $bill->id }} – {{ $bill->patient_name }}
                        (₱{{ number_format($bill->total_amount, 2) }}
                        @if($bill->amount_paid > 0)
                            · ₱{{ number_format($bill->remaining_balance, 2) }} remaining
                        @endif)
                    </option>
                @endforeach
            </select>
        </div>

        <div id="balance-strip" style="background:var(--off-white);border:1px solid var(--border);border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;display:flex;gap:20px;">
            <span>Total: <strong id="strip-total">—</strong></span>
            <span>Paid: <strong id="strip-paid" style="color:#1B7A54;">—</strong></span>
            <span>Remaining: <strong id="strip-remaining" style="color:#B03030;">—</strong></span>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Amount to Pay (₱)</label>
                <input type="number" name="amount" id="amount-input" step="0.01" min="0.01"
                       required placeholder="e.g. 5000.00"
                       value="{{ old('amount') }}"
                       class="form-input">
                <div id="amount-hint" style="font-size:11px;color:var(--muted);margin-top:4px;"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <select name="method" class="form-input">
                    <option value="cash"       {{ old('method') == 'cash'       ? 'selected' : '' }}>Cash</option>
                    <option value="card"       {{ old('method') == 'card'       ? 'selected' : '' }}>Card</option>
                    <option value="philhealth" {{ old('method') == 'philhealth' ? 'selected' : '' }}>PhilHealth</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Processed By</label>
            <input type="text" name="processed_by" required placeholder="e.g. N. Reyes, RN"
                   value="{{ old('processed_by') }}" class="form-input">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-primary">Record Payment</button>
            <a href="/billing" class="btn-cancel">Cancel</a>
        </div>
    </form>

    <script>
    function fmt(n) {
        return '₱' + parseFloat(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function updateBalance() {
        const sel = document.getElementById('bill-select');
        const opt = sel.options[sel.selectedIndex];
        const remaining = parseFloat(opt.dataset.remaining || 0);
        const total     = parseFloat(opt.dataset.total     || 0);
        const paid      = parseFloat(opt.dataset.paid      || 0);

        document.getElementById('strip-total').textContent     = fmt(total);
        document.getElementById('strip-paid').textContent      = fmt(paid);
        document.getElementById('strip-remaining').textContent = fmt(remaining);

        document.getElementById('amount-input').max = remaining;
        document.getElementById('amount-hint').textContent = 'Max payable: ' + fmt(remaining);
    }

    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const sel = document.getElementById('bill-select');
        const opt = sel.options[sel.selectedIndex];
        const remaining = parseFloat(opt.dataset.remaining || 0);
        const amount    = parseFloat(document.getElementById('amount-input').value || 0);
        if (amount > remaining) {
            e.preventDefault();
            alert('Payment of ₱' + amount.toFixed(2) + ' exceeds the remaining balance of ₱' + remaining.toFixed(2));
        }
    });

    document.addEventListener('DOMContentLoaded', updateBalance);
    </script>
    @endif
</div>

<div class="table-card">
    <div class="table-header">
        <span class="table-title">Payment Records</span>
        <span class="table-count">{{ $payments->count() }} entries</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Amount Paid</th>
                <th>Method</th>
                <th>Date</th>
                <th>Processed By</th>
                <th>Bill Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td style="color:var(--muted);font-size:12px;">{{ $payment->id }}</td>
                <td style="font-weight:500;">{{ $payment->bill->patient_name }}</td>
                <td style="font-weight:500;color:#1B7A54;">₱{{ number_format($payment->amount, 2) }}</td>
                <td><span class="method-badge">{{ ucfirst($payment->method) }}</span></td>
                <td style="color:var(--muted);">{{ $payment->created_at->format('M d, Y') }}</td>
                <td style="color:var(--muted);">{{ $payment->processed_by }}</td>
                <td>
                    @php $s = $payment->bill->status; @endphp
                    <span class="badge {{ $s === 'paid' ? 'badge-paid' : ($s === 'overdue' ? 'badge-overdue' : 'badge-pending') }}">
                        {{ ucfirst($s) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="empty-state">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection