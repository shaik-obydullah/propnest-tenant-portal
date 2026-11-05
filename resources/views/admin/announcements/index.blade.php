@extends('layouts.app')

@section('title', 'Announcements')
@section('eyebrow', 'Communications')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Keep everyone in the loop.</h2>
            <p>Publish building notices and portfolio-wide updates to the resident portal.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="button button-dark button-sm"><x-icon name="plus" :size="15" /> New announcement</a>
    </div>

    <div class="stack stack-roomy">
        @forelse ($announcements as $announcement)
            <article class="announcement-admin-row">
                <div class="announcement-admin-copy">
                    <div class="announcement-card-meta">
                        @if ($announcement->pinned)<span class="pin-label"><x-icon name="star" :size="13" /> Pinned</span>@endif
                        <span>{{ $announcement->property?->name ?? 'All buildings' }}</span>
                        <span>{{ Str::headline($announcement->audience) }}</span>
                        <span>{{ $announcement->published_at->format('j M Y') }}</span>
                    </div>
                    <h3>{{ $announcement->title }}</h3>
                    <p>{{ Str::limit($announcement->body, 180) }}</p>
                    <span class="table-sub">Published by {{ $announcement->author?->name ?? 'PropNest' }}</span>
                </div>
                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Remove this announcement?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn icon-btn-danger" aria-label="Delete announcement"><x-icon name="trash" :size="17" /></button>
                </form>
            </article>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="bell" :size="28" /></span>
                <h3>No announcements yet</h3>
                <p>Publish your first notice to keep residents informed.</p>
                <a href="{{ route('admin.announcements.create') }}" class="button button-dark button-sm">New announcement</a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">{{ $announcements->links() }}</div>
@endsection
