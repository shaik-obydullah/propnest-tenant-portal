<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PortalNotification;
use App\Models\ServiceRequest;
use App\Models\Tenancy;
use App\Support\Reference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(): View
    {
        $tenancy = $this->tenancy();

        return view('tenant.requests.index', [
            'tenancy' => $tenancy,
            'requests' => $tenancy->serviceRequests()->latest()->get(),
            'types' => config('portal.service_requests'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('tenant.requests.create', [
            'tenancy' => $this->tenancy()->load(['property', 'unit']),
            'types' => config('portal.service_requests'),
            'selectedType' => $request->string('type')->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenancy = $this->tenancy();

        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(config('portal.service_requests')))],
            'subject' => ['required', 'string', 'max:140'],
            'details' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $serviceRequest = ServiceRequest::create([
            'tenancy_id' => $tenancy->id,
            'user_id' => auth()->id(),
            'reference' => Reference::service(),
            'type' => $data['type'],
            'subject' => $data['subject'],
            'details' => $data['details'],
            'status' => 'submitted',
        ]);

        PortalNotification::create([
            'user_id' => auth()->id(),
            'title' => 'Request received',
            'message' => $serviceRequest->reference.' — your property team will review this shortly.',
            'type' => 'request',
            'link' => route('tenant.requests.show', $serviceRequest),
        ]);

        return redirect()
            ->route('tenant.requests.show', $serviceRequest)
            ->with('status', 'Your request has been submitted to the property team.');
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        abort_unless($serviceRequest->user_id === auth()->id(), 404);

        return view('tenant.requests.show', [
            'request' => $serviceRequest->load(['tenancy.property', 'tenancy.unit']),
        ]);
    }

    private function tenancy(): Tenancy
    {
        return auth()->user()->activeTenancy()->with(['property', 'unit'])->firstOrFail();
    }
}
