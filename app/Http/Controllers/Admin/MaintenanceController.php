<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceUpdate;
use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $requests = MaintenanceRequest::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['property', 'tenancy.tenant', 'assignee'])
            ->when($request->string('status')->toString() === 'open', fn ($query) => $query->whereNotIn('status', ['completed', 'cancelled']))
            ->when($request->string('status')->toString() === 'closed', fn ($query) => $query->whereIn('status', ['completed', 'cancelled']))
            ->when($request->string('priority')->toString(), fn ($query, string $priority) => $query->where('priority', $priority))
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('property', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.maintenance.index', [
            'requests' => $requests,
            'status' => $request->string('status')->toString(),
            'priority' => $request->string('priority')->toString(),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function show(MaintenanceRequest $maintenance): View
    {
        abort_unless($maintenance->organization_id === auth()->user()->organization_id, 404);

        return view('admin.maintenance.show', [
            'request' => $maintenance->load(['property', 'tenancy.tenant', 'submitter', 'assignee', 'updates.user']),
            'contractors' => User::query()
                ->where('organization_id', auth()->user()->organization_id)
                ->where('role', UserRole::Contractor)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        abort_unless($maintenance->organization_id === auth()->user()->organization_id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:submitted,triaged,scheduled,in_progress,completed,cancelled'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'scheduled_for' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $maintenance->status;

        $maintenance->update([
            'status' => $data['status'],
            'priority' => $data['priority'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'scheduled_for' => $data['scheduled_for'] ?? null,
            'cost' => $data['cost'] ?? null,
            'completed_at' => $data['status'] === 'completed'
                ? ($maintenance->completed_at ?? now())
                : null,
        ]);

        if ($oldStatus !== $maintenance->status || filled($data['message'] ?? null)) {
            MaintenanceUpdate::create([
                'maintenance_request_id' => $maintenance->id,
                'user_id' => auth()->id(),
                'message' => $data['message'] ?: 'Status changed to '.$maintenance->status_label.'.',
                'status_from' => $oldStatus,
                'status_to' => $maintenance->status,
            ]);

            PortalNotification::create([
                'user_id' => $maintenance->submitted_by,
                'title' => 'Maintenance request updated',
                'message' => $maintenance->reference.' is now “'.$maintenance->status_label.'”.',
                'type' => 'maintenance',
                'link' => route('tenant.maintenance.show', $maintenance),
            ]);
        }

        return back()->with('status', 'Maintenance job updated and the tenant has been notified.');
    }
}
