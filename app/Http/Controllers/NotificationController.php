<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('notifications.index', [
            'notifications' => auth()->user()->portalNotifications()->latest()->paginate(12),
        ]);
    }

    public function read(PortalNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === auth()->id(), 404);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $notification->link
            ? redirect($notification->link)
            : back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }
}
