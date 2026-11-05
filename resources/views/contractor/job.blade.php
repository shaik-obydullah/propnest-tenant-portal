@extends('layouts.app')

@section('title', $job->title)
@section('eyebrow', 'Job · '.$job->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('contractor.dashboard') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to my jobs</a>
            <div class="page-heading-title">
                <h2>{{ $job->title }}</h2>
                <x-badge :status="$job->status" :label="$job->status_label" />
            </div>
            <p>{{ $job->property->name }}@if ($job->tenancy), Unit {{ $job->tenancy->unit->unit_number }}@endif · {{ $job->reference }}</p>
        </div>
        <x-badge :status="$job->priority" :label="$job->priority_label.' priority'" />
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">The issue</span>
                        <h3>What the tenant reported</h3>
                    </div>
                    <span class="panel-ref">Logged {{ $job->created_at->format('j M Y') }}</span>
                </div>

                <p class="body-copy">{{ $job->description }}</p>

                @if ($job->photo_path)
                    <a href="{{ asset('storage/'.$job->photo_path) }}" target="_blank" class="maintenance-photo">
                        <img src="{{ asset('storage/'.$job->photo_path) }}" alt="Photo attached to maintenance request">
                    </a>
                @endif

                <div class="detail-grid detail-grid-compact">
                    <div class="detail-item"><span>Category</span><strong>{{ $job->category }}</strong></div>
                    <div class="detail-item"><span>Priority</span><strong>{{ $job->priority_label }}</strong></div>
                    <div class="detail-item"><span>Visit</span><strong>{{ $job->scheduled_for?->format('j M Y, H:i') ?? 'Not booked' }}</strong></div>
                    <div class="detail-item"><span>Recorded cost</span><strong>{{ $job->cost ? money($job->cost) : 'Not recorded' }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Activity</span>
                        <h3>Job history</h3>
                    </div>
                    <span class="panel-ref">{{ $job->updates->count() }} updates</span>
                </div>

                <div class="timeline">
                    @foreach ($job->updates->sortBy('created_at') as $update)
                        <div class="timeline-item">
                            <span class="timeline-dot {{ $update->status_to ? 'is-status' : '' }}"></span>
                            <div class="timeline-body">
                                <div class="timeline-head">
                                    <strong>{{ $update->user?->name ?? 'PropNest' }}</strong>
                                    <span>{{ $update->created_at->format('j M Y, H:i') }}</span>
                                </div>
                                <p>{{ $update->message }}</p>
                                @if ($update->status_to && $update->status_from !== $update->status_to)
                                    <span class="timeline-status">Status changed to {{ config('portal.maintenance.statuses.'.$update->status_to, $update->status_to) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @unless ($job->status === 'cancelled')
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Update</span>
                            <h3>Post a job update</h3>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('contractor.jobs.update', $job) }}" class="form-stack">
                        @csrf
                        @method('PATCH')

                        <div class="form-grid-2">
                            <div class="field">
                                <label for="status">Status</label>
                                <select id="status" name="status" required>
                                    @foreach (['scheduled' => 'Visit scheduled', 'in_progress' => 'In progress', 'completed' => 'Completed'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $job->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="cost">Repair cost</label>
                                <input id="cost" name="cost" type="number" step="0.01" min="0" value="{{ old('cost', $job->cost) }}" placeholder="0.00">
                                <span class="field-hint">Record parts and labour before completing.</span>
                            </div>
                        </div>

                        <div class="field">
                            <label for="message">Update for the tenant &amp; manager</label>
                            <textarea id="message" name="message" rows="3" required placeholder="Describe what you found, what you did or what happens next…">{{ old('message') }}</textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="button button-dark button-sm">Post update <x-icon name="send" :size="15" /></button>
                        </div>
                    </form>
                </section>
            @endunless
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Tenant</span><h3>Who to contact</h3></div></div>
                <div class="contact-card">
                    <span class="avatar">{{ $job->tenancy?->tenant?->initials ?? 'PN' }}</span>
                    <div>
                        <strong>{{ $job->tenancy?->tenant?->name ?? 'No tenant linked' }}</strong>
                        <span>{{ $job->tenancy?->tenant?->phone ?? 'Contact the manager' }}</span>
                    </div>
                    @if ($job->tenancy?->tenant?->email)
                        <a href="mailto:{{ $job->tenancy->tenant->email }}" class="icon-btn"><x-icon name="mail" :size="17" /></a>
                    @endif
                </div>
                <div class="detail-list">
                    <div><span>Property</span><strong>{{ $job->property->name }}</strong></div>
                    <div><span>Address</span><strong>{{ $job->property->address_line_1 }}, {{ $job->property->postcode }}</strong></div>
                    @if ($job->tenancy)
                        <div><span>Unit</span><strong>{{ $job->tenancy->unit->unit_number }}</strong></div>
                    @endif
                    <div><span>Reported by</span><strong>{{ $job->submitter?->name ?? 'PropNest' }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Appointment</span><h3>Scheduled visit</h3></div></div>
                @if ($job->scheduled_for)
                    <div class="appointment-card">
                        <span class="appointment-date"><strong>{{ $job->scheduled_for->format('d') }}</strong>{{ $job->scheduled_for->format('M') }}</span>
                        <div>
                            <strong>{{ $job->scheduled_for->format('l, j F') }}</strong>
                            <span>{{ $job->scheduled_for->format('H:i') }} – {{ $job->scheduled_for->copy()->addHour()->format('H:i') }}</span>
                        </div>
                    </div>
                @else
                    <p class="body-copy">No visit has been booked yet. The property manager will confirm a time with the tenant.</p>
                @endif
                <div class="detail-list">
                    <div><span>Status</span><strong>{{ $job->status_label }}</strong></div>
                    <div><span>Priority</span><strong>{{ $job->priority_label }}</strong></div>
                    <div><span>Reference</span><strong>{{ $job->reference }}</strong></div>
                </div>
            </section>

            @if ($job->tenant_feedback)
                <section class="panel panel-tinted">
                    <span class="support-icon"><x-icon name="star" :size="20" /></span>
                    <h3>Tenant feedback</h3>
                    <p>“{{ $job->tenant_feedback }}”</p>
                </section>
            @endif
        </div>
    </div>
@endsection
