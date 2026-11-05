@extends('layouts.app')

@section('title', $request->title)
@section('eyebrow', 'Maintenance · '.$request->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.maintenance.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to queue</a>
            <div class="page-heading-title">
                <h2>{{ $request->title }}</h2>
                <x-badge :status="$request->status" :label="$request->status_label" />
            </div>
            <p>{{ $request->property->name }}, Unit {{ $request->tenancy->unit->unit_number }} · {{ $request->reference }}</p>
        </div>
        <x-badge :status="$request->priority" :label="$request->priority_label.' priority'" />
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Reported issue</span><h3>Tenant description</h3></div>
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
                    <div class="detail-item"><span>Submitted</span><strong>{{ $request->created_at->format('j M Y, H:i') }}</strong></div>
                    <div class="detail-item"><span>Tenant</span><strong>{{ $request->tenancy->tenant->name }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Activity</span><h3>History</h3></div>
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
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Update</span><h3>Manage this job</h3></div>
                </div>

                <form method="POST" action="{{ route('admin.maintenance.update', $request) }}" class="form-stack">
                    @csrf
                    @method('PATCH')

                    <div class="form-grid-3">
                        <div class="field">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                @foreach (config('portal.maintenance.statuses') as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $request->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="priority">Priority</label>
                            <select id="priority" name="priority" required>
                                @foreach (config('portal.maintenance.priorities') as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', $request->priority) === $value)>{{ Str::headline($value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="assigned_to">Contractor</label>
                            <select id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach ($contractors as $contractor)
                                    <option value="{{ $contractor->id }}" @selected(old('assigned_to', $request->assigned_to) == $contractor->id)>{{ $contractor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="field">
                            <label for="scheduled_for">Visit date &amp; time</label>
                            <input id="scheduled_for" name="scheduled_for" type="datetime-local" value="{{ old('scheduled_for', $request->scheduled_for?->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="field">
                            <label for="cost">Repair cost</label>
                            <input id="cost" name="cost" type="number" step="0.01" min="0" value="{{ old('cost', $request->cost) }}" placeholder="0.00">
                        </div>
                    </div>

                    <div class="field">
                        <label for="message">Update for the tenant</label>
                        <textarea id="message" name="message" rows="3" placeholder="Explain what has changed or what happens next…"></textarea>
                        <span class="field-hint">This is posted to the tenant’s request timeline and triggers a notification.</span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button button-dark button-sm">Save update <x-icon name="check" :size="15" /></button>
                    </div>
                </form>
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Tenant</span><h3>Who reported it</h3></div></div>
                <div class="contact-card">
                    <span class="avatar">{{ $request->tenancy->tenant->initials }}</span>
                    <div><strong>{{ $request->tenancy->tenant->name }}</strong><span>{{ $request->tenancy->tenant->email }}</span></div>
                    <a href="mailto:{{ $request->tenancy->tenant->email }}" class="icon-btn"><x-icon name="mail" :size="17" /></a>
                </div>
                <div class="detail-list">
                    <div><span>Phone</span><strong>{{ $request->tenancy->tenant->phone }}</strong></div>
                    <div><span>Tenancy</span><strong>{{ $request->tenancy->reference }}</strong></div>
                    <div><span>Property</span><strong>{{ $request->property->name }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Assignment</span><h3>Current state</h3></div></div>
                <div class="detail-list">
                    <div><span>Status</span><strong>{{ $request->status_label }}</strong></div>
                    <div><span>Priority</span><strong>{{ $request->priority_label }}</strong></div>
                    <div><span>Contractor</span><strong>{{ $request->assignee?->name ?? 'Unassigned' }}</strong></div>
                    <div><span>Visit</span><strong>{{ $request->scheduled_for?->format('j M Y, H:i') ?? 'Not booked' }}</strong></div>
                    <div><span>Cost</span><strong>{{ $request->cost ? money($request->cost) : 'Not recorded' }}</strong></div>
                </div>
            </section>

            @if ($request->tenant_feedback)
                <section class="panel panel-tinted">
                    <span class="support-icon"><x-icon name="star" :size="20" /></span>
                    <h3>Tenant feedback</h3>
                    <p>“{{ $request->tenant_feedback }}”</p>
                </section>
            @endif
        </div>
    </div>
@endsection
