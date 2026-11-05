@extends('layouts.app')

@section('title', 'Maintenance')
@section('eyebrow', 'Repairs & requests')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Repairs, tracked properly.</h2>
            <p>Report an issue, add photos and follow every update from submission to completion.</p>
        </div>
        <a href="{{ route('tenant.maintenance.create') }}" class="button button-dark"><x-icon name="plus" :size="16" /> Report an issue</a>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('tenant.maintenance.index') }}" class="{{ request('status') ? '' : 'is-active' }}">All requests <span>{{ $requests->count() }}</span></a>
        <a href="{{ route('tenant.maintenance.index', ['status' => 'open']) }}" class="{{ request('status') === 'open' ? 'is-active' : '' }}">Open <span>{{ $openCount }}</span></a>
        <a href="{{ route('tenant.maintenance.index', ['status' => 'closed']) }}" class="{{ request('status') === 'closed' ? 'is-active' : '' }}">Completed</a>
    </div>

    <div class="stack stack-roomy">
        @forelse ($requests as $maintenance)
            <a href="{{ route('tenant.maintenance.show', $maintenance) }}" class="maintenance-card">
                <div class="maintenance-card-main">
                    <span class="maintenance-icon maintenance-icon-lg priority-{{ $maintenance->priority }}"><x-icon name="wrench" :size="21" /></span>
                    <div class="maintenance-card-copy">
                        <div class="maintenance-card-title">
                            <h3>{{ $maintenance->title }}</h3>
                            <x-badge :status="$maintenance->status" :label="$maintenance->status_label" />
                        </div>
                        <p>{{ Str::limit($maintenance->description, 150) }}</p>
                        <div class="maintenance-meta">
                            <span><x-icon name="clipboard" :size="14" /> {{ $maintenance->reference }}</span>
                            <span><x-icon name="wrench" :size="14" /> {{ $maintenance->category }}</span>
                            <span><x-icon name="alert" :size="14" /> {{ $maintenance->priority_label }} priority</span>
                            @if ($maintenance->scheduled_for)
                                <span><x-icon name="calendar" :size="14" /> Visit {{ $maintenance->scheduled_for->format('j M, H:i') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="maintenance-card-side">
                    <span class="maintenance-updated">Updated {{ $maintenance->updated_at->diffForHumans() }}</span>
                    @if ($maintenance->assignee)
                        <div class="assignee-chip">
                            <span class="avatar avatar-xs">{{ $maintenance->assignee->initials }}</span>
                            <span>{{ $maintenance->assignee->name }}</span>
                        </div>
                    @endif
                    <span class="table-link">View request <x-icon name="arrow-right" :size="14" /></span>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="wrench" :size="28" /></span>
                <h3>No maintenance requests here</h3>
                <p>When something needs fixing, report it here and the property team will take it from there.</p>
                <a href="{{ route('tenant.maintenance.create') }}" class="button button-dark button-sm">Report an issue</a>
            </div>
        @endforelse
    </div>

    <div class="info-strip">
        <span class="info-strip-icon"><x-icon name="alert" :size="19" /></span>
        <div>
            <strong>Emergency?</strong>
            <p>For gas leaks, flooding or anything that puts someone at risk, call the emergency line on <a href="tel:08000123456">0800 012 3456</a> before logging a request.</p>
        </div>
    </div>
@endsection
