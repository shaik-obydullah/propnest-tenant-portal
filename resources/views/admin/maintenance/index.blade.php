@extends('layouts.app')

@section('title', 'Maintenance queue')
@section('eyebrow', 'Operations')

@section('content')
    <div class="page-heading">
        <div>
            <h2>The repair queue.</h2>
            <p>Prioritise, assign and keep tenants informed from one place.</p>
        </div>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('admin.maintenance.index') }}" class="{{ $status ? '' : 'is-active' }}">All requests</a>
        <a href="{{ route('admin.maintenance.index', ['status' => 'open']) }}" class="{{ $status === 'open' ? 'is-active' : '' }}">Open</a>
        <a href="{{ route('admin.maintenance.index', ['status' => 'closed']) }}" class="{{ $status === 'closed' ? 'is-active' : '' }}">Closed</a>
    </div>

    <form method="GET" action="{{ route('admin.maintenance.index') }}" class="toolbar">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <div class="search-field">
            <x-icon name="search" :size="17" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Search title, reference or property…">
        </div>
        <select name="priority" class="toolbar-select">
            <option value="">Any priority</option>
            @foreach (config('portal.maintenance.priorities') as $value => $label)
                <option value="{{ $value }}" @selected($priority === $value)>{{ Str::headline($value) }}</option>
            @endforeach
        </select>
        <button type="submit" class="button button-ghost button-sm">Filter</button>
        @if ($search || $priority)
            <a href="{{ route('admin.maintenance.index') }}" class="text-link">Clear</a>
        @endif
        <span class="toolbar-count">{{ $requests->total() }} requests</span>
    </form>

    <div class="stack stack-roomy">
        @forelse ($requests as $job)
            <a href="{{ route('admin.maintenance.show', $job) }}" class="maintenance-card">
                <div class="maintenance-card-main">
                    <span class="maintenance-icon maintenance-icon-lg priority-{{ $job->priority }}"><x-icon name="wrench" :size="21" /></span>
                    <div class="maintenance-card-copy">
                        <div class="maintenance-card-title">
                            <h3>{{ $job->title }}</h3>
                            <x-badge :status="$job->status" :label="$job->status_label" />
                        </div>
                        <p>{{ Str::limit($job->description, 130) }}</p>
                        <div class="maintenance-meta">
                            <span><x-icon name="clipboard" :size="14" /> {{ $job->reference }}</span>
                            <span><x-icon name="building" :size="14" /> {{ $job->property->name }}</span>
                            <span><x-icon name="user" :size="14" /> {{ $job->tenancy->tenant->name }}</span>
                            <span><x-icon name="alert" :size="14" /> {{ $job->priority_label }}</span>
                        </div>
                    </div>
                </div>
                <div class="maintenance-card-side">
                    <span class="maintenance-updated">{{ $job->updated_at->diffForHumans() }}</span>
                    @if ($job->assignee)
                        <div class="assignee-chip"><span class="avatar avatar-xs">{{ $job->assignee->initials }}</span><span>{{ $job->assignee->name }}</span></div>
                    @else
                        <span class="text-muted">Unassigned</span>
                    @endif
                    <span class="table-link">Manage <x-icon name="arrow-right" :size="14" /></span>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="check-circle" :size="28" /></span>
                <h3>Queue is clear</h3>
                <p>No maintenance requests match these filters.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">{{ $requests->links() }}</div>
@endsection
