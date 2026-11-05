<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceUpdate;
use App\Models\PortalNotification;
use App\Models\Tenancy;
use App\Support\Reference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $tenancy = $this->tenancy();

        $requests = $tenancy->maintenanceRequests()
            ->with('assignee')
            ->when($request->string('status')->toString() === 'open', fn ($query) => $query->whereNotIn('status', ['completed', 'cancelled']))
            ->when($request->string('status')->toString() === 'closed', fn ($query) => $query->whereIn('status', ['completed', 'cancelled']))
            ->latest()
            ->get();

        return view('tenant.maintenance.index', [
            'tenancy' => $tenancy,
            'requests' => $requests,
            'openCount' => $requests->filter->is_open->count(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.maintenance.create', [
            'tenancy' => $this->tenancy()->load(['property', 'unit']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenancy = $this->tenancy();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(config('portal.maintenance.categories')))],
            'priority' => ['required', 'string', 'in:'.implode(',', array_keys(config('portal.maintenance.priorities')))],
            'description' => ['required', 'string', 'min:15', 'max:3000'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('maintenance', 'public');
        }

        $maintenance = MaintenanceRequest::create([
            'organization_id' => $tenancy->organization_id,
            'property_id' => $tenancy->property_id,
            'tenancy_id' => $tenancy->id,
            'submitted_by' => auth()->id(),
            'reference' => Reference::maintenance(),
            'title' => $data['title'],
            'category' => $data['category'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => 'submitted',
            'photo_path' => $photoPath,
        ]);

        MaintenanceUpdate::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => auth()->id(),
            'message' => 'Maintenance request submitted and added to the property manager queue.',
            'status_to' => 'submitted',
        ]);

        PortalNotification::create([
            'user_id' => auth()->id(),
            'title' => 'Maintenance request logged',
            'message' => $maintenance->reference.' — we have passed your request to the property team.',
            'type' => 'maintenance',
            'link' => route('tenant.maintenance.show', $maintenance),
        ]);

        return redirect()
            ->route('tenant.maintenance.show', $maintenance)
            ->with('status', 'Your maintenance request has been logged.');
    }

    public function show(MaintenanceRequest $maintenance): View
    {
        $this->authorizeRequest($maintenance);

        return view('tenant.maintenance.show', [
            'request' => $maintenance->load(['property', 'tenancy', 'assignee', 'updates.user']),
        ]);
    }

    public function addUpdate(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        $this->authorizeRequest($maintenance);

        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        MaintenanceUpdate::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => auth()->id(),
            'message' => $data['message'],
        ]);

        return back()->with('status', 'Your message has been added to the request.');
    }

    public function feedback(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        $this->authorizeRequest($maintenance);
        abort_unless($maintenance->status === 'completed', 403);

        $data = $request->validate([
            'tenant_feedback' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $maintenance->update($data);

        return back()->with('status', 'Thank you — your feedback has been recorded.');
    }

    private function tenancy(): Tenancy
    {
        return auth()->user()->activeTenancy()->with(['property', 'unit'])->firstOrFail();
    }

    private function authorizeRequest(MaintenanceRequest $maintenance): void
    {
        abort_unless($maintenance->submitted_by === auth()->id(), 404);
    }
}
