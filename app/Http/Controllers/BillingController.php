<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    private function getStats(): array
    {
        Bill::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $totalRevenue = (float) Bill::sum('total_amount');
        $totalPaid    = (float) Payment::sum('amount');
        $outstanding  = (float) Bill::whereIn('status', ['pending', 'overdue'])->sum('total_amount');

        return [
            'total_revenue'   => $totalRevenue,
            'bills_generated' => Bill::count(),
            'paid'            => $totalPaid,
            'outstanding'     => $outstanding,
            'overdue_count'   => Bill::where('status', 'overdue')->count(),
            'collection_rate' => $totalRevenue > 0
                                    ? round(($totalPaid / $totalRevenue) * 100)
                                    : 0,
        ];
    }

    public function index()
    {
        $stats = $this->getStats();

        $monthly = Bill::latest()
            ->get()
            ->groupBy(fn ($bill) => $bill->created_at->format('Y-m'))
            ->map(function ($bills) {
                $month = $bills->first()->created_at;
                return (object) [
                    'month_num' => $month->format('m'),
                    'year'      => $month->format('Y'),
                    'month'     => $month->format('F Y'),
                    'count'     => $bills->count(),
                    'total'     => $bills->sum('total_amount'),
                    'collected' => $bills->where('status', 'paid')->sum('total_amount'),
                ];
            })
            ->values();

        $recent = Bill::with('payments')->latest()->take(5)->get();

        return view('billing.index', compact('stats', 'monthly', 'recent'));
    }

    public function allBills()
    {
        $stats = $this->getStats();
        $bills = Bill::with('payments')->latest()->get();

        return view('billing.allbills', compact('bills', 'stats'));
    }

    public function outstanding()
    {
        $this->getStats();
        $bills = Bill::with('payments')
                     ->whereIn('status', ['pending', 'overdue'])
                     ->latest()
                     ->get();

        return view('billing.outstanding', compact('bills'));
    }

    public function create()
    {
        $patients = \App\Models\Patient::orderBy('last_name')->get();
        return view('billing.create', compact('patients'));
    }

    public function store(Request $request)
    {
    $request->validate([
        'patient_id'   => 'required|exists:patients,id',
        'service_type' => 'required|in:room,treatment,services',
        'total_amount' => 'required|numeric|min:0.01',
        'due_date'     => 'required|date',
    ]);

        $status = Carbon::parse($request->due_date)->isPast() ? 'overdue' : 'pending';
        $patient = \App\Models\Patient::findOrFail($request->patient_id);

        Bill::create([
        'patient_id'   => $patient->id,
        'patient_name' => $patient->full_name,
        'service_type' => $request->service_type,
        'total_amount' => $request->total_amount,
        'due_date'     => $request->due_date,
        'status'       => $status,
    ]);

        return redirect('/billing')->with('success', 'Bill created successfully.');
    }

    public function destroy($id)
    {
        Bill::destroy($id);
        return redirect('/billing')->with('success', 'Bill deleted.');
    }

    public function payments()
    {
        $this->getStats();

        $payments = Payment::with('bill')->latest()->get();

        $bills = Bill::with('payments')
                     ->whereIn('status', ['pending', 'overdue'])
                     ->get()
                     ->filter(fn($b) => $b->remaining_balance > 0)
                     ->values();

        return view('billing.payments', compact('payments', 'bills'));
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'bill_id'      => 'required|exists:bills,id',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'required|in:cash,card,philhealth',
            'processed_by' => 'required|string|max:255',
        ]);

        $bill = Bill::with('payments')->findOrFail($request->bill_id);

        if ($bill->status === 'paid') {
            return redirect('/payments')
                ->withErrors(['amount' => 'This bill has already been paid in full.'])
                ->withInput();
        }

        $remaining = $bill->remaining_balance;

        if ((float) $request->amount > $remaining) {
            return redirect('/payments')
                ->withErrors(['amount' => 'Payment of ₱' . number_format($request->amount, 2) . ' exceeds the remaining balance of ₱' . number_format($remaining, 2) . '.'])
                ->withInput();
        }

        Payment::create([
            'bill_id'      => $bill->id,
            'amount'       => $request->amount,
            'method'       => $request->method,
            'processed_by' => $request->processed_by,
        ]);

        $totalPaid = (float) $bill->payments()->sum('amount') + (float) $request->amount;

        if ($totalPaid >= (float) $bill->total_amount) {
            $bill->update(['status' => 'paid']);
        }

        return redirect('/payments')->with('success', 'Payment recorded successfully.');
    }

    public function reports()
    {
        $stats = $this->getStats();

        $byService = Bill::selectRaw(
            'service_type, COUNT(*) as count, SUM(total_amount) as total'
        )->groupBy('service_type')->get();

        $outstanding = Bill::with('payments')
                           ->whereIn('status', ['pending', 'overdue'])
                           ->latest()
                           ->get();

        return view('billing.reports', compact('stats', 'byService', 'outstanding'));
    }
}