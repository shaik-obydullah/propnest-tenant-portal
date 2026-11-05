<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Tenancy;
use Illuminate\Contracts\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $tenancy = $this->tenancy();

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
            ->get();

        return view('tenant.announcements.index', [
            'tenancy' => $tenancy,
            'announcements' => $announcements,
        ]);
    }

    public function show(Announcement $announcement): View
    {
        $tenancy = $this->tenancy();

        abort_unless(
            $announcement->organization_id === $tenancy->organization_id
            && (is_null($announcement->property_id) || $announcement->property_id === $tenancy->property_id),
            404
        );

        return view('tenant.announcements.show', [
            'announcement' => $announcement->load(['author', 'property']),
            'tenancy' => $tenancy,
        ]);
    }

    private function tenancy(): Tenancy
    {
        return auth()->user()->activeTenancy()->firstOrFail();
    }
}
