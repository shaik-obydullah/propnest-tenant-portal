<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Contracts\View\View;

class TenancyController extends Controller
{
    public function show(): View
    {
        $tenancy = auth()->user()->activeTenancy()
            ->with(['property', 'unit', 'organization', 'tenant'])
            ->firstOrFail();

        $documents = Document::query()
            ->where('tenancy_id', $tenancy->id)
            ->whereIn('visibility', ['tenant', 'all'])
            ->latest()
            ->get();

        return view('tenant.tenancy', [
            'tenancy' => $tenancy,
            'documents' => $documents,
        ]);
    }
}
