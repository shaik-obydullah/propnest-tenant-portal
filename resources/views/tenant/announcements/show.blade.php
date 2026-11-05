@extends('layouts.app')

@section('title', 'Announcement')
@section('eyebrow', 'Noticeboard')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.announcements.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to announcements</a>
            <h2>{{ $announcement->title }}</h2>
            <p>
                {{ $announcement->property?->name ?? 'All buildings' }}
                · {{ $announcement->published_at->format('j F Y') }}
                @if ($announcement->pinned)
                    · <span class="pin-label"><x-icon name="star" :size="12" /> Pinned</span>
                @endif
            </p>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <article class="panel announcement-body">
            <div class="announcement-body-head">
                <span class="avatar">{{ $announcement->author?->initials ?? 'HV' }}</span>
                <div>
                    <strong>{{ $announcement->author?->name ?? 'Harbour & Vine' }}</strong>
                    <span>{{ $announcement->author?->job_title ?? 'Property team' }}</span>
                </div>
            </div>
            <div class="prose">
                {!! nl2br(e($announcement->body)) !!}
            </div>
        </article>

        <aside class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Details</span>
                        <h3>Announcement</h3>
                    </div>
                </div>
                <div class="detail-list">
                    <div><span>Published</span><strong>{{ $announcement->published_at->format('j M Y, H:i') }}</strong></div>
                    <div><span>Audience</span><strong>{{ Str::headline($announcement->audience) }}</strong></div>
                    <div><span>Property</span><strong>{{ $announcement->property?->name ?? 'All buildings' }}</strong></div>
                </div>
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="message" :size="20" /></span>
                <h3>Questions about this?</h3>
                <p>Send your property team a message and reference this announcement.</p>
                <a href="{{ route('tenant.requests.create', ['type' => 'enquiry']) }}" class="button button-dark button-sm">Contact the team</a>
            </section>
        </aside>
    </div>
@endsection
