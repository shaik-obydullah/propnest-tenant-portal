@extends('layouts.app')

@section('title', $request->title)
@section('eyebrow', 'Maintenance · '.$request->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.maintenance.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to maintenance</a>
            <div class="page-heading-title">
                <h2>{{ $request->title }}</h2>
                <x-badge :status="$request->status" :label="$request->status_label" />
            </div>
            <p>{{ $request->reference }} · {{ $request->category }} · logged {{ $request->created_at->format('j F Y') }}</p>
        </div>
        <x-badge :status="$request->priority" :label="$request->priority_label.' priority'" />
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">The request</span>
                        <h3>What you reported</h3>
                    </div>
                </div>

                <p class="body-copy">{{ $request->description }}</p>

                @if ($request->photo_path)
                    <a href="{{ asset('storage/'.$request->photo_path) }}" target="_blank" class="maintenance-photo">
                        <img src="{{ asset('storage/'.$request->photo_path) }}" alt="Photo attached to maintenance request">
                    </a>
                @endif

                <div class="detail-grid detail-grid-compact">
                    <div class="detail-item"><span>Category</span><strong>{{ $request->category }}</strong></div>
                    <div class="detail-item"><span>Priority</span><strong>{{ $request->priority_label }}</strong></div>
                    <div class="detail-item"><span>Logged</span><strong>{{ $request->created_at->format('j M Y, H:i') }}</strong></div>
                    <div class="detail-item"><span>Property</span><strong>{{ $request->property->name }}, {{ $request->tenancy->unit->unit_number }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Activity</span>
                        <h3>Progress &amp; messages</h3>
                    </div>
                    <span class="panel-ref">{{ $request->updates->count() }} updates</span>
                </div>

                <div class="timeline">
                    @foreach ($request->updates->sortBy('created_at') as $update)
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

                @unless (in_array($request->status, ['completed', 'cancelled']))
                    <form method="POST" action="{{ route('tenant.maintenance.updates.store', $request) }}" class="comment-form">
                        @csrf
                        <div class="field">
                            <label for="message">Add a message</label>
                            <textarea id="message" name="message" rows="3" required placeholder="Share an update, ask a question or add access notes…"></textarea>
                        </div>
                        <button type="submit" class="button button-dark button-sm"><x-icon name="send" :size="15" /> Send message</button>
                    </form>
                @endunless
            </section>

            @if ($request->status === 'completed' && ! $request->tenant_feedback)
                <section class="panel">
                    <div class="panel-head">
                        <div>
                            <span class="eyebrow">Feedback</span>
                            <h3>How did the repair go?</h3>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('tenant.maintenance.feedback', $request) }}" class="form-stack">
                        @csrf
                        <div class="field">
                            <label for="tenant_feedback">Leave a comment</label>
                            <textarea id="tenant_feedback" name="tenant_feedback" rows="3" required placeholder="Was the issue resolved? Was the visit straightforward?"></textarea>
                        </div>
                        <button type="submit" class="button button-dark button-sm">Submit feedback</button>
                    </form>
                </section>
            @elseif ($request->tenant_feedback)
                <div class="inline-note inline-note-green">
                    <x-icon name="check-circle" :size="17" />
                    <span><strong>Your feedback:</strong> {{ $request->tenant_feedback }}</span>
                </div>
            @endif
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Visit</span>
                        <h3>Appointment</h3>
                    </div>
                </div>

                @if ($request->scheduled_for)
                    <div class="appointment-card">
                        <span class="appointment-date"><strong>{{ $request->scheduled_for->format('d') }}</strong>{{ $request->scheduled_for->format('M') }}</span>
                        <div>
                            <strong>{{ $request->scheduled_for->format('l, j F') }}</strong>
                            <span>{{ $request->scheduled_for->format('H:i') }} – {{ $request->scheduled_for->copy()->addHour()->format('H:i') }}</span>
                        </div>
                    </div>
                @else
                    <p class="body-copy">A visit has not been booked yet. We will add the date here as soon as it is confirmed.</p>
                @endif

                @if ($request->assignee)
                    <div class="contact-card contact-card-compact">
                        <span class="avatar">{{ $request->assignee->initials }}</span>
                        <div>
                            <strong>{{ $request->assignee->name }}</strong>
                            <span>{{ $request->assignee->job_title }}</span>
                        </div>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Details</span>
                        <h3>Reference</h3>
                    </div>
                </div>
                <div class="detail-list">
                    <div><span>Reference</span><strong>{{ $request->reference }}</strong></div>
                    <div><span>Status</span><strong>{{ $request->status_label }}</strong></div>
                    <div><span>Logged by</span><strong>{{ $request->submitter->name }}</strong></div>
                    <div><span>Last update</span><strong>{{ $request->updated_at->diffForHumans() }}</strong></div>
                    @if ($request->completed_at)
                        <div><span>Completed</span><strong>{{ $request->completed_at->format('j M Y') }}</strong></div>
                    @endif
                    @if ($request->cost)
                        <div><span>Repair cost</span><strong>{{ money($request->cost) }}</strong></div>
                    @endif
                </div>
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="lifebuoy" :size="20" /></span>
                <h3>Need to add something?</h3>
                <p>Use the message box to send access notes, ask a question or flag anything that has changed.</p>
            </section>
        </div>
    </div>
@endsection
