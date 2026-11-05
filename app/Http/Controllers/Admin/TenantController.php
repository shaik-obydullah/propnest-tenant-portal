<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Tenancy;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $tenancies = Tenancy::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['tenant', 'property', 'unit', 'rentPayments'])
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('tenant', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('property', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tenants.index', [
            'tenancies' => $tenancies,
            'search' => $request->string('q')->toString(),
            'tenantCount' => User::query()
                ->where('organization_id', auth()->user()->organization_id)
                ->where('role', UserRole::Tenant)
                ->count(),
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
}
