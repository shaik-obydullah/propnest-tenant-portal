@extends('layouts.app')

@section('title', 'Announcements')
@section('eyebrow', 'Noticeboard')

@section('content')
    <div class="page-heading">
        <div>
            <h2>What’s happening at home.</h2>
            <p>Building notices, planned works and useful reminders from your property team.</p>
        </div>
    </div>

    <div class="announcement-page-list">
        @foreach ($announcements as $announcement)
            <a href="{{ route('tenant.announcements.show', $announcement) }}" class="announcement-card">
                <span class="announcement-date announcement-date-lg"><strong>{{ $announcement->published_at->format('d') }}</strong>{{ $announcement->published_at->format('M') }}</span>
                <div class="announcement-card-copy">
                    <div class="announcement-card-meta">
                        @if ($announcement->pinned)
                            <span class="pin-label"><x-icon name="star" :size="13" /> Pinned</span>
                        @endif
                        <span>{{ $announcement->property?->name ?? 'All buildings' }}</span>
                        <span>{{ $announcement->published_at->diffForHumans() }}</span>
                    </div>
                    <h3>{{ $announcement->title }}</h3>
                    <p>{{ Str::limit(strip_tags($announcement->body), 190) }}</p>
                    <span class="table-link">Read announcement <x-icon name="arrow-right" :size="14" /></span>
                </div>
                <span class="announcement-arrow"><x-icon name="arrow-up-right" :size="18" /></span>
            </a>
        @endforeach
    </div>
@endsection
