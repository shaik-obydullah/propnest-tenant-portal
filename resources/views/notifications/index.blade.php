@extends('layouts.app')

@section('title', 'Notifications')
@section('eyebrow', 'Your updates')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Everything worth knowing.</h2>
            <p>Payment reminders, maintenance updates, new documents and building notices.</p>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="button button-ghost button-sm"><x-icon name="check" :size="15" /> Mark all as read</button>
        </form>
    </div>

    <section class="panel">
        @forelse ($notifications as $notification)
            <div class="notification-row {{ $notification->read_at ? '' : 'is-unread' }}">
                <span class="notification-type notification-type-{{ $notification->type }}">
                    <x-icon name="{{ match ($notification->type) { 'payment' => 'pound', 'maintenance' => 'wrench', 'document' => 'file', 'request' => 'clipboard', default => 'bell' } }}" :size="18" />
                </span>
                <div class="notification-copy">
                    <div class="notification-title-row">
                        <strong>{{ $notification->title }}</strong>
                        @unless ($notification->read_at)
                            <span class="unread-pill">New</span>
                        @endunless
                    </div>
                    <p>{{ $notification->message }}</p>
                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                    @csrf
                    <button type="submit" class="button button-ghost button-sm">Open <x-icon name="arrow-right" :size="14" /></button>
                </form>
            </div>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="bell" :size="28" /></span>
                <h3>Nothing new right now</h3>
                <p>We’ll let you know when there is an update on your home.</p>
            </div>
        @endforelse
    </section>

    <div class="pagination-wrap">{{ $notifications->links() }}</div>
@endsection
