<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Tenancy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $tenancy = $this->tenancy();

        $documents = Document::query()
            ->where('organization_id', $tenancy->organization_id)
            ->where(function ($query) use ($tenancy) {
                $query->where('tenancy_id', $tenancy->id)
                    ->orWhere('property_id', $tenancy->property_id);
            })
            ->whereIn('visibility', ['tenant', 'all'])
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->latest()
            ->get();

        return view('tenant.documents.index', [
            'tenancy' => $tenancy->load(['property', 'unit']),
            'documents' => $documents,
            'categories' => config('portal.document_categories'),
        ]);
    }

    public function download(Document $document): BinaryFileResponse
    {
        $tenancy = $this->tenancy();

        abort_unless(
            $document->organization_id === $tenancy->organization_id
            && ($document->tenancy_id === $tenancy->id || $document->property_id === $tenancy->property_id)
            && in_array($document->visibility, ['tenant', 'all'], true),
            404
        );

        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'The document file could not be found.');

        return response()->download(
            Storage::disk('public')->path($document->file_path),
            Str::slug($document->title).'.pdf',
            ['Content-Type' => $document->mime_type]
        );
    }

    private function tenancy(): Tenancy
    {
        return auth()->user()->activeTenancy()->firstOrFail();
    }
}
