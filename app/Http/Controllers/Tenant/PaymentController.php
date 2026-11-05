<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PortalNotification;
use App\Models\RentPayment;
use App\Models\Tenancy;
use App\Support\Reference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(): View
    {
        $tenancy = $this->tenancy();
        $payments = $tenancy->rentPayments()->orderByDesc('due_date')->get();

        return view('tenant.payments.index', [
            'tenancy' => $tenancy->load(['property', 'unit']),
            'payments' => $payments,
            'balance' => (float) $payments->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'paidTotal' => (float) $payments->where('status', 'paid')->sum('amount'),
            'nextPayment' => $payments->whereIn('status', ['pending', 'overdue'])->sortBy('due_date')->first(),
        ]);
    }

    public function checkout(): View
    {
        $tenancy = $this->tenancy();
        $outstanding = $tenancy->rentPayments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();

        return view('tenant.payments.checkout', [
            'tenancy' => $tenancy->load(['property', 'unit']),
            'outstanding' => $outstanding,
            'balance' => (float) $outstanding->sum('amount'),
        ]);
    }

    public function pay(Request $request, RentPayment $payment): RedirectResponse
    {
        $tenancy = $this->tenancy();
        abort_unless($payment->tenancy_id === $tenancy->id, 404);

        if ($payment->status === 'paid') {
            return back()->with('status', 'That payment has already been settled.');
        }

        $request->validate([
            'confirm' => ['accepted'],
        ]);

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'method' => 'Demo card payment',
                'receipt_number' => Reference::receipt(),
            ]);

            PortalNotification::create([
                'user_id' => $payment->tenancy->tenant_id,
                'title' => 'Rent payment received',
                'message' => 'We have received '.money($payment->amount).' for '.$payment->period_label.'. Your receipt is ready.',
                'type' => 'payment',
                'link' => route('tenant.payments.index'),
            ]);
        });

        return redirect()
            ->route('tenant.payments.receipt', $payment)
            ->with('status', 'Payment received. Your receipt is ready to download.');
    }

    public function receipt(RentPayment $payment): View
    {
        $tenancy = $this->tenancy();
        abort_unless($payment->tenancy_id === $tenancy->id, 404);

        return view('tenant.payments.receipt', [
            'payment' => $payment,
            'tenancy' => $tenancy->load(['property', 'unit', 'tenant', 'organization']),
        ]);
    }

    private function tenancy(): Tenancy
    {
        return auth()->user()->activeTenancy()->firstOrFail();
    }
}
