<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::query()
                ->where('organization_id', auth()->user()->organization_id)
                ->with(['property', 'author'])
                ->latest('published_at')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'properties' => Property::query()
                ->where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'body' => ['required', 'string', 'min:20', 'max:5000'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'audience' => ['required', 'in:tenants,landlords,all'],
            'pinned' => ['nullable', 'boolean'],
        ]);

        Announcement::create([
            ...$data,
            'organization_id' => auth()->user()->organization_id,
            'author_id' => auth()->id(),
            'pinned' => $request->boolean('pinned'),
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement published to the portal.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->organization_id === auth()->user()->organization_id, 404);

        $announcement->delete();

        return back()->with('status', 'Announcement removed.');
    }
}
