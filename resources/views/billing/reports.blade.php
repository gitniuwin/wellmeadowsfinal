@extends('layouts.app')
@section('page-title', 'Reports & Summaries')

@section('content')
{{-- TAB BAR --}}
@php $path = request()->path(); @endphp
<div class="tab-bar">
    <a href="/billing"       class="tab {{ $path === 'billing' ? 'active' : '' }}">Summary</a>
    <a href="/billing/all"   class="tab {{ str_starts_with($path,'billing/all') || str_starts_with($path,'billing/create') ? 'active' : '' }}">All Bills</a>
    <a href="/payments"      class="tab {{ str_starts_with($path,'payment') ? 'active' : '' }}">Payments</a>
    <a href="/outstanding"   class="tab {{ str_starts_with($path,'outstanding') ? 'active' : '' }}">Outstanding</a>
    <a href="/reports"       class="tab {{ str_starts_with($path,'report') ? 'active' : '' }}">Reports</a>
</div>

{{-- ══════════════════════════════════════
     SECTION 1 — Revenue Stats
════════════════════════════════════════ --}}
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);font-weight:600;margin-bottom:10px;">Revenue Overview</div>
<div class="stat-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">₱{{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="stat-sub">All time billed</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Collected</div>
        <div class="stat-value">₱{{ number_format($stats['paid'], 0) }}</div>
        <div class="stat-sub">{{ $stats['collection_rate'] }}% collection rate</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Bills Generated</div>
        <div class="stat-value">{{ $stats['bills_generated'] }}</div>
        <div class="stat-sub">Total invoices</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Outstanding</div>
        <div class="stat-value">₱{{ number_format($stats['outstanding'], 0) }}</div>
        <div class="stat-sub red">{{ $stats['overdue_count'] }} overdue</div>
    </div>
</div>

{{-- ══════════════════════════════════════
     SECTION 2 — Patient Records Report (Module 1)
════════════════════════════════════════ --}}
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);font-weight:600;margin-bottom:10px;">Patient Records</div>
<div class="stat-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Total Patients</div>
        <div class="stat-value">{{ $totalPatients }}</div>
        <div class="stat-sub">Registered</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Currently Admitted</div>
        <div class="stat-value">{{ $admittedNow }}</div>
        <div class="stat-sub">Inpatients</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Outpatients</div>
        <div class="stat-value">{{ $outpatients }}</div>
        <div class="stat-sub">Not admitted</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Patients with Bills</div>
        <div class="stat-value">{{ $patientsWithBills }}</div>
        <div class="stat-sub">Linked to billing</div>
    </div>
</div>

{{-- ══════════════════════════════════════
     SECTION 3 — Occupancy Rate Report (Module 3)
════════════════════════════════════════ --}}
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);font-weight:600;margin-bottom:10px;">Ward Occupancy Rate</div>
<div class="stat-grid" style="margin-bottom:16px;">
    <div class="stat-card">
        <div class="stat-label">Total Beds</div>
        <div class="stat-value">{{ $totalBeds }}</div>
        <div class="stat-sub">Hospital capacity</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Occupied</div>
        <div class="stat-value" style="color:#B03030;">{{ $occupiedBeds }}</div>
        <div class="stat-sub red">{{ $occupancyRate }}% occupancy</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Vacant</div>
        <div class="stat-value" style="color:#1B7A54;">{{ $vacantBeds }}</div>
        <div class="stat-sub green">Available now</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Active Wards</div>
        <div class="stat-value">{{ $wards->count() }}</div>
        <div class="stat-sub">Operational</div>
    </div>
</div>

{{-- Per-ward occupancy table --}}
<div class="table-card" style="margin-bottom:24px;">
    <div class="table-header">
        <span class="table-title">Occupancy by Ward</span>
        <a href="{{ route('wards.index') }}" class="view-all-link">View Wards →</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ward</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>Occupied</th>
                <th>Occupancy Rate</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($wards as $ward)
            @php
                $occ = $ward->occupancy_percentage;
                $statusColor = $occ >= 100 ? '#B03030' : ($occ >= 80 ? '#A06000' : '#1B7A54');
                $statusLabel = $occ >= 100 ? 'Full' : ($occ >= 80 ? 'Limited' : 'Available');
            @endphp
            <tr>
                <td style="font-weight:500;">{{ $ward->name }}</td>
                <td><span class="svc-badge">{{ $ward->type }}</span></td>
                <td>{{ $ward->capacity }}</td>
                <td>{{ $ward->beds->where('status','occupied')->count() }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex:1;background:#eee;border-radius:20px;height:6px;">
                            <div style="width:{{ min($occ,100) }}%;background:{{ $statusColor }};height:6px;border-radius:20px;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:500;color:{{ $statusColor }};min-width:36px;">{{ $occ }}%</span>
                    </div>
                </td>
                <td><span class="badge" style="background:{{ $occ>=100 ? '#FFF0F0' : ($occ>=80 ? '#FFF8DB' : '#E3F7EF') }};color:{{ $statusColor }};">{{ $statusLabel }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state">No wards found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════
     SECTION 4 — Appointments & Treatments (Module 4)
════════════════════════════════════════ --}}
<div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);font-weight:600;margin-bottom:10px;">Appointments & Treatments</div>
<div class="stat-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Total Appointments</div>
        <div class="stat-value">{{ $totalAppointments }}</div>
        <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-label">Completed</div>
        <div class="stat-value">{{ $completedAppts }}</div>
        <div class="stat-sub green">Finished appointments</div>
    </div>
    <div class="stat-card sky">
        <div class="stat-label">Total Treatments</div>
        <div class="stat-value">{{ $totalTreatments }}</div>
        <div class="stat-sub">Procedures recorded</div>
    </div>
    <div class="stat-card light">
        <div class="stat-label">Appt Completion Rate</div>
        <div class="stat-value" style="color:#1B7A54;">
            {{ $totalAppointments > 0 ? round(($completedAppts/$totalAppointments)*100) : 0 }}%
        </div>
        <div class="stat-sub">Completed vs total</div>
    </div>
</div>

{{-- ══════════════════════════════════════
     SECTION 5 — Revenue by Service Type
════════════════════════════════════════ --}}
<div class="table-card" style="margin-bottom:20px;">
    <div class="table-header">
        <span class="table-title">Revenue by Service Type</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Service Type</th>
                <th>No. of Bills</th>
                <th>Total Billed</th>
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

{{-- ══════════════════════════════════════
     SECTION 6 — Monthly Revenue Trend
════════════════════════════════════════ --}}
<div class="table-card" style="margin-bottom:20px;">
    <div class="table-header">
        <span class="table-title">Monthly Revenue Trend</span>
        <span style="font-size:12px;color:var(--muted);">Last 6 months</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Billed</th>
                <th>Collected</th>
                <th>Collection Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyRevenue as $row)
            @php $rate = $row->total > 0 ? round(($row->collected / $row->total) * 100) : 0; @endphp
            <tr>
                <td style="font-weight:500;">{{ $row->month }}</td>
                <td>₱{{ number_format($row->total, 2) }}</td>
                <td style="color:#1B7A54;font-weight:500;">₱{{ number_format($row->collected, 2) }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex:1;background:#eee;border-radius:20px;height:6px;">
                            <div style="width:{{ $rate }}%;background:#1B7A54;height:6px;border-radius:20px;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:500;color:#1B7A54;min-width:36px;">{{ $rate }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">No monthly data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════
     SECTION 7 — Top Patients by Billing
════════════════════════════════════════ --}}
<div class="table-card" style="margin-bottom:20px;">
    <div class="table-header">
        <span class="table-title">Top Patients by Total Billed</span>
        <a href="{{ route('patients.index') }}" class="view-all-link">View All Patients →</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>No. of Bills</th>
                <th>Total Billed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topPatients as $row)
            <tr>
                <td style="font-weight:500;">{{ $row->patient_name }}</td>
                <td>{{ $row->bill_count }}</td>
                <td style="font-weight:500;">₱{{ number_format($row->total_billed, 2) }}</td>
                <td>
                    @if($row->patient_id)
                        <a href="{{ route('patients.show', $row->patient_id) }}" class="action-btn">View Patient</a>
                    @else
                        <span style="font-size:11px;color:var(--muted);">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state">No billing data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════
     SECTION 8 — Outstanding Bills (Hospital Mgmt Summary)
════════════════════════════════════════ --}}
<div class="table-card">
    <div class="table-header">
        <span class="table-title">Outstanding Bills — Management Summary</span>
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
            <tr><td colspan="6" class="empty-state">No outstanding bills. All caught up! 🎉</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection