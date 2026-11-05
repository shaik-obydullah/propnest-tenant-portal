<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use App\Models\PortalNotification;
use App\Models\RentPayment;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $tenancy = $user->activeTenancy()
            ?->with(['property', 'unit'])
            ->first();

        abort_unless($tenancy, 404, 'No active tenancy is linked to your account.');

        $payments = $tenancy->rentPayments()->orderByDesc('due_date');
        $unpaid = (clone $payments)->whereIn('status', ['pending', 'overdue']);

        $openMaintenance = $tenancy->maintenanceRequests()
            ->with('assignee')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->get();

        $documents = Document::query()
            ->where('organization_id', $tenancy->organization_id)
            ->where(function ($query) use ($tenancy) {
                $query->where('tenancy_id', $tenancy->id)
                    ->orWhere('property_id', $tenancy->property_id);
            })
            ->whereIn('visibility', ['tenant', 'all'])
            ->latest()
            ->take(4)
            ->get();

        $announcements = Announcement::query()
            ->where('organization_id', $tenancy->organization_id)
            ->where(function ($query) use ($tenancy) {
                $query->whereNull('property_id')
                    ->orWhere('property_id', $tenancy->property_id);
            })
            ->whereIn('audience', ['tenants', 'all'])
            ->where('published_at', '<=', now())
            ->orderByDesc('pinned')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('tenant.dashboard', [
            'tenancy' => $tenancy,
            'balance' => (float) $unpaid->sum('amount'),
            'unpaidCount' => $unpaid->count(),
            'nextPayment' => (clone $unpaid)->orderBy('due_date')->first(),
            'recentPayments' => (clone $payments)->where('status', 'paid')->take(4)->get(),
            'openMaintenance' => $openMaintenance,
            'documents' => $documents,
            'announcements' => $announcements,
            'notifications' => PortalNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->take(4)
                ->get(),
            'paidThisYear' => (float) RentPayment::query()
                ->where('tenancy_id', $tenancy->id)
                ->where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ]);
    }
}
