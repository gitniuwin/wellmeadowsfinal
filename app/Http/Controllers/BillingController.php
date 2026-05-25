<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    // Auto-marks overdue bills and returns stats array
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

    public function index(){
    $stats = $this->getStats();

    $monthly = Bill::selectRaw(
        "strftime('%m', created_at) as month_num,
         strftime('%Y', created_at) as year,
         COUNT(*) as count,
         SUM(total_amount) as total,
         SUM(CASE WHEN status='paid' THEN total_amount ELSE 0 END) as collected"
    )
    ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
    ->orderByRaw("strftime('%Y', created_at) DESC, strftime('%m', created_at) DESC")
    ->get()
    ->map(function($row) {
        $months = [
            '01'=>'January','02'=>'February','03'=>'March','04'=>'April',
            '05'=>'May','06'=>'June','07'=>'July','08'=>'August',
            '09'=>'September','10'=>'October','11'=>'November','12'=>'December'
        ];
        $row->month = ($months[$row->month_num] ?? $row->month_num) . ' ' . $row->year;
        return $row;
    });

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
        return view('billing.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'service_type' => 'required|in:room,treatment,services',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date'     => 'required|date',
        ]);

        // Auto-set status based on due date — never trust user input for this
        $status = Carbon::parse($request->due_date)->isPast() ? 'overdue' : 'pending';

        Bill::create([
            'patient_name' => $request->patient_name,
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

        // Only show bills that still have an outstanding balance
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

        // Guard: already fully paid
        if ($bill->status === 'paid') {
            return redirect('/payments')
                ->withErrors(['amount' => 'This bill has already been paid in full.'])
                ->withInput();
        }

        $remaining = $bill->remaining_balance;

        // Guard: payment exceeds remaining balance
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

        // Re-check total paid after inserting
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
