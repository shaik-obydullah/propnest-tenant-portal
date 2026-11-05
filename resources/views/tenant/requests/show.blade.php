@extends('layouts.app')

@section('title', $request->subject)
@section('eyebrow', 'Request · '.$request->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.requests.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to requests</a>
            <div class="page-heading-title">
                <h2>{{ $request->subject }}</h2>
                <x-badge :status="$request->status" :label="$request->status_label" />
            </div>
            <p>{{ $request->type_label }} · {{ $request->reference }} · submitted {{ $request->created_at->format('j F Y') }}</p>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your request</span>
                        <h3>The details</h3>
                    </div>
                </div>
                <p class="body-copy">{{ $request->details }}</p>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Conversation</span>
                        <h3>Responses from your team</h3>
                    </div>
                </div>

                @if ($request->response)
                    <div class="response-card">
                        <span class="avatar">{{ $request->tenancy->property->manager?->initials ?? 'HV' }}</span>
                        <div>
                            <div class="response-head">
                                <strong>{{ $request->tenancy->property->manager?->name ?? 'Harbour & Vine' }}</strong>
                                <span>Property manager</span>
                            </div>
                            <p>{{ $request->response }}</p>
                            @if ($request->resolved_at)
                                <span class="response-time">Resolved {{ $request->resolved_at->format('j M Y') }}</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="empty-inline">
                        <span class="empty-icon"><x-icon name="clock" :size="22" /></span>
                        <div><strong>Waiting for a response</strong><span>Your request is with the property team. We usually reply within one working day.</span></div>
                    </div>
                @endif

                <div class="inline-note inline-note-small">
                    <x-icon name="message" :size="16" />
                    <span>Need to add something? Send a general enquiry and reference {{ $request->reference }}.</span>
                    <a href="{{ route('tenant.requests.create', ['type' => 'enquiry']) }}">Add a note</a>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Summary</span>
                        <h3>Request details</h3>
                    </div>
                </div>
                <div class="detail-list">
                    <div><span>Type</span><strong>{{ $request->type_label }}</strong></div>
                    <div><span>Reference</span><strong>{{ $request->reference }}</strong></div>
                    <div><span>Status</span><strong>{{ $request->status_label }}</strong></div>
                    <div><span>Submitted</span><strong>{{ $request->created_at->format('j M Y, H:i') }}</strong></div>
                    @if ($request->resolved_at)
                        <div><span>Resolved</span><strong>{{ $request->resolved_at->format('j M Y') }}</strong></div>
                    @endif
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Property</span>
                        <h3>Linked tenancy</h3>
                    </div>
                </div>
                <div class="contact-card contact-card-compact">
                    <span class="property-thumb property-accent-{{ $request->tenancy->property->accent }}"><x-icon name="building" :size="20" /></span>
                    <div>
                        <strong>{{ $request->tenancy->property->name }}</strong>
                        <span>Unit {{ $request->tenancy->unit->unit_number }} · {{ $request->tenancy->reference }}</span>
                    </div>
                </div>
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="lifebuoy" :size="20" /></span>
                <h3>Need to talk it through?</h3>
                <p>Call your property manager on <a href="tel:02079460120">020 7946 0120</a> or email <a href="mailto:hello@harbourvine.test">hello@harbourvine.test</a>.</p>
            </section>
        </div>
    </div>
@endsection
