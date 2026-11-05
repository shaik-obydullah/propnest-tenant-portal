@extends('layouts.app')

@section('title', 'My jobs')
@section('eyebrow', now()->format('l, j F Y').' · Contractor board')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Today’s work, in one place.</h2>
            <p>{{ $stats['open'] }} open {{ Str::plural('job', $stats['open']) }} · {{ $stats['scheduled'] }} scheduled · {{ $stats['completed'] }} completed</p>
        </div>
        <div class="page-heading-actions">
            <span class="chip"><x-icon name="pound" :size="15" /> {{ money($stats['earned']) }} earned</span>
        </div>
    </div>

    <div class="stat-strip">
        <div class="stat-card">
            <span class="stat-label">Open jobs</span>
            <strong class="stat-value">{{ $stats['open'] }}</strong>
            <span class="stat-note">Assigned to you</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Visits scheduled</span>
            <strong class="stat-value">{{ $stats['scheduled'] }}</strong>
            <span class="stat-note">Confirmed appointments</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Completed</span>
            <strong class="stat-value">{{ $stats['completed'] }}</strong>
            <span class="stat-note">Signed off jobs</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Recorded earnings</span>
            <strong class="stat-value">{{ money($stats['earned']) }}</strong>
            <span class="stat-note">Across completed jobs</span>
        </div>
    </div>

    <div class="content-grid">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Active board</span>
                        <h3>Open jobs</h3>
                    </div>
                    <span class="panel-ref">{{ $openJobs->count() }} {{ Str::plural('job', $openJobs->count()) }}</span>
                </div>

                <div class="job-list">
                    @forelse ($openJobs as $job)
                        <a href="{{ route('contractor.jobs.show', $job) }}" class="job-card">
                            <span class="maintenance-icon priority-{{ $job->priority }}"><x-icon name="wrench" :size="18" /></span>
                            <div class="job-card-body">
                                <div class="job-card-top">
                                    <strong>{{ $job->title }}</strong>
                                    <x-badge :status="$job->status" :label="$job->status_label" />
                                </div>
                                <span class="job-card-meta">
                                    {{ $job->property->name }}
                                    @if ($job->tenancy)
                                        · Unit {{ $job->tenancy->unit->unit_number }}
                                    @endif
                                    · {{ $job->reference }}
                                </span>
                                <div class="job-card-foot">
                                    <span><x-icon name="pin" :size="13" /> {{ $job->property->postcode }}</span>
                                    <span><x-icon name="calendar" :size="13" /> {{ $job->scheduled_for?->format('j M, H:i') ?? 'Not scheduled' }}</span>
                                    <span class="job-priority text-{{ status_tone($job->priority) }}">{{ $job->priority_label }}</span>
                                </div>
                            </div>
                            <x-icon name="chevron-right" :size="16" />
                        </a>
                    @empty
                        <div class="empty-state">
                            <span class="empty-icon"><x-icon name="check-circle" :size="26" /></span>
                            <h3>All clear</h3>
                            <p>You have no open jobs. New assignments will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Archive</span>
                        <h3>Completed jobs</h3>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Property</th>
                                <th>Completed</th>
                                <th class="align-right">Cost</th>
                                <th class="align-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jobs->where('status', 'completed') as $job)
                                <tr>
                                    <td>
                                        <strong>{{ $job->title }}</strong>
                                        <span class="table-sub">{{ $job->reference }} · {{ $job->category }}</span>
                                    </td>
                                    <td>{{ $job->property->name }}<span class="table-sub">{{ $job->tenancy?->tenant?->name }}</span></td>
                                    <td>{{ $job->completed_at?->format('j M Y') ?? '—' }}</td>
                                    <td class="align-right">{{ $job->cost ? money($job->cost) : '—' }}</td>
                                    <td class="align-right"><a href="{{ route('contractor.jobs.show', $job) }}" class="table-link">View <x-icon name="arrow-up-right" :size="13" /></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="table-empty">No completed jobs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel panel-dark">
                <span class="eyebrow eyebrow-light">Working with PropNest</span>
                <h3>Keep tenants in the loop.</h3>
                <p class="panel-dark-copy">Every status change you post is visible to the tenant and their property manager. Add clear notes before and after each visit.</p>
                <div class="panel-dark-list">
                    <span><x-icon name="clock" :size="16" /> Update the status as soon as you arrive</span>
                    <span><x-icon name="pound" :size="16" /> Record costs before marking a job complete</span>
                    <span><x-icon name="message" :size="16" /> Leave access notes for the next visit</span>
                </div>
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="lifebuoy" :size="20" /></span>
                <h3>Need support?</h3>
                <p>Contact the property team if a job needs extra parts, a specialist or a follow-up visit.</p>
                <a href="mailto:maintenance@harbourvine.test" class="button button-ghost button-sm">Email the team <x-icon name="mail" :size="15" /></a>
            </section>
        </div>
    </div>
@endsection
