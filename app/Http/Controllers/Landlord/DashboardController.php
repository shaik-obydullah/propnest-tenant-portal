<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\Tenancy;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $properties = Property::query()
            ->where('landlord_id', auth()->id())
            ->with(['units', 'tenancies.tenant', 'maintenanceRequests'])
            ->get();

        $propertyIds = $properties->pluck('id');
        $tenancies = Tenancy::query()
            ->whereIn('property_id', $propertyIds)
            ->with(['property', 'unit', 'tenant', 'rentPayments'])
            ->get();

        $payments = RentPayment::query()
            ->whereHas('tenancy', fn ($query) => $query->whereIn('property_id', $propertyIds))
            ->where('status', 'paid')
            ->latest('paid_at')
            ->take(6)
            ->with(['tenancy.property', 'tenancy.tenant'])
            ->get();

        return view('landlord.dashboard', [
            'properties' => $properties,
            'tenancies' => $tenancies,
            'payments' => $payments,
            'stats' => [
                'properties' => $properties->count(),
                'units' => $properties->sum(fn (Property $property) => $property->units->count()),
                'occupied' => $properties->sum(fn (Property $property) => $property->units->where('status', 'occupied')->count()),
                'monthlyRent' => $tenancies->where('status', 'active')->sum('rent_amount'),
                'collected' => $payments->sum('amount'),
                'openJobs' => $properties->sum(fn (Property $property) => $property->maintenanceRequests->whereNotIn('status', ['completed', 'cancelled'])->count()),
            ],
        ]);
    }

    public function property(Property $property): View
    {
        abort_unless($property->landlord_id === auth()->id(), 404);

        return view('landlord.property', [
            'property' => $property->load([
                'units.tenancies.tenant',
                'tenancies.tenant',
                'maintenanceRequests.tenancy.tenant',
                'documents',
            ]),
        ]);
    }
}
