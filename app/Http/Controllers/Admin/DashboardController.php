<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\Tenancy;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $organizationId = auth()->user()->organization_id;

        $properties = Property::query()
            ->where('organization_id', $organizationId)
            ->with(['units', 'tenancies.tenant'])
            ->get();

        $tenancies = Tenancy::query()
            ->where('organization_id', $organizationId)
            ->with(['property', 'unit', 'tenant'])
            ->get();

        $openMaintenance = MaintenanceRequest::query()
            ->where('organization_id', $organizationId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['property', 'tenancy.tenant', 'assignee'])
            ->latest()
            ->get();

        $unpaid = RentPayment::query()
            ->whereHas('tenancy', fn ($query) => $query->where('organization_id', $organizationId))
            ->whereIn('status', ['pending', 'overdue'])
            ->with('tenancy.tenant')
            ->orderBy('due_date')
            ->get();

        $months = collect(range(5, 0))->map(function (int $offset) use ($organizationId) {
            $start = now()->subMonths($offset)->startOfMonth();
            $end = now()->subMonths($offset)->endOfMonth();

            return [
                'label' => $start->format('M'),
                'amount' => (float) RentPayment::query()
                    ->whereHas('tenancy', fn ($query) => $query->where('organization_id', $organizationId))
                    ->where('status', 'paid')
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('amount'),
            ];
        });

        $maxMonth = max(1, (float) $months->max('amount'));

        return view('admin.dashboard', [
            'properties' => $properties,
            'tenancies' => $tenancies,
            'openMaintenance' => $openMaintenance,
            'unpaid' => $unpaid,
            'months' => $months,
            'maxMonth' => $maxMonth,
            'stats' => [
                'properties' => $properties->count(),
                'units' => $properties->sum(fn (Property $property) => $property->units->count()),
                'occupancy' => $properties->sum(fn (Property $property) => $property->units->where('status', 'occupied')->count()),
                'activeTenancies' => $tenancies->where('status', 'active')->count(),
                'arrears' => (float) $unpaid->sum('amount'),
                'collectedThisMonth' => (float) RentPayment::query()
                    ->whereHas('tenancy', fn ($query) => $query->where('organization_id', $organizationId))
                    ->where('status', 'paid')
                    ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('amount'),
                'openMaintenance' => $openMaintenance->count(),
                'expiringSoon' => $tenancies
                    ->where('status', 'active')
                    ->filter(fn (Tenancy $tenancy) => $tenancy->end_date->lessThanOrEqualTo(now()->addDays(90)))
                    ->count(),
            ],
            'upcomingExpiries' => $tenancies
                ->where('status', 'active')
                ->sortBy('end_date')
                ->take(4),
            'recentPayments' => RentPayment::query()
                ->whereHas('tenancy', fn ($query) => $query->where('organization_id', $organizationId))
                ->where('status', 'paid')
                ->with(['tenancy.tenant', 'tenancy.property'])
                ->latest('paid_at')
                ->take(5)
                ->get(),
        ]);
    }
}
