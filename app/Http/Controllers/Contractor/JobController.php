<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceUpdate;
use App\Models\PortalNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(): View
    {
        $jobs = MaintenanceRequest::query()
            ->where('assigned_to', auth()->id())
            ->with(['property', 'tenancy.tenant'])
            ->orderByRaw("CASE status
                WHEN 'in_progress' THEN 1
                WHEN 'scheduled' THEN 2
                WHEN 'triaged' THEN 3
                WHEN 'submitted' THEN 4
                WHEN 'completed' THEN 5
                ELSE 6 END")
            ->latest()
            ->get();

        return view('contractor.dashboard', [
            'jobs' => $jobs,
            'openJobs' => $jobs->filter->is_open,
            'stats' => [
                'open' => $jobs->filter->is_open->count(),
                'scheduled' => $jobs->where('status', 'scheduled')->count(),
                'completed' => $jobs->where('status', 'completed')->count(),
                'earned' => (float) $jobs->where('status', 'completed')->sum('cost'),
            ],
        ]);
    }

    public function show(MaintenanceRequest $maintenance): View
    {
        abort_unless($maintenance->assigned_to === auth()->id(), 404);

        return view('contractor.job', [
            'job' => $maintenance->load(['property', 'tenancy.tenant', 'submitter', 'updates.user']),
        ]);
    }

    public function update(Request $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        abort_unless($maintenance->assigned_to === auth()->id(), 404);

        $data = $request->validate([
            'status' => ['required', 'in:scheduled,in_progress,completed'],
            'message' => ['required', 'string', 'min:3', 'max:2000'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        $oldStatus = $maintenance->status;

        $maintenance->update([
            'status' => $data['status'],
            'cost' => $data['cost'] ?? $maintenance->cost,
            'completed_at' => $data['status'] === 'completed' ? now() : null,
        ]);

        MaintenanceUpdate::create([
            'maintenance_request_id' => $maintenance->id,
            'user_id' => auth()->id(),
            'message' => $data['message'],
            'status_from' => $oldStatus,
            'status_to' => $maintenance->status,
        ]);

        PortalNotification::create([
            'user_id' => $maintenance->submitted_by,
            'title' => 'Contractor update',
            'message' => $maintenance->reference.': '.$data['message'],
            'type' => 'maintenance',
            'link' => route('tenant.maintenance.show', $maintenance),
        ]);

        return back()->with('status', 'Job update posted to the tenant.');
    }
}
