<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentPayment;
use App\Models\Tenancy;
use App\Support\Reference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenancyController extends Controller
{
    public function index(Request $request): View
    {
        $tenancies = Tenancy::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['property', 'unit', 'tenant'])
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('tenant', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('property', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('start_date')
            ->paginate(12)
            ->withQueryString();

        return view('admin.tenancies.index', [
            'tenancies' => $tenancies,
            'status' => $request->string('status')->toString(),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function show(Tenancy $tenancy): View
    {
        abort_unless($tenancy->organization_id === auth()->user()->organization_id, 404);

        return view('admin.tenancies.show', [
            'tenancy' => $tenancy->load([
                'property',
                'unit',
                'tenant',
                'rentPayments',
                'maintenanceRequests',
                'serviceRequests',
                'documents',
            ]),
        ]);
    }

    public function markPaid(RentPayment $payment): RedirectResponse
    {
        abort_unless($payment->tenancy->organization_id === auth()->user()->organization_id, 404);

        if ($payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'method' => 'Manual record',
                'receipt_number' => Reference::receipt(),
            ]);
        }

        return back()->with('status', 'Payment recorded and receipt generated.');
    }
}
