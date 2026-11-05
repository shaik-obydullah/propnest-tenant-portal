@extends('layouts.app')

@section('title', 'My tenancy')
@section('eyebrow', 'Your agreement')

@section('content')
    <div class="page-heading">
        <div>
            <h2>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }}</h2>
            <p>{{ $tenancy->property->address }}</p>
        </div>
        <div class="page-heading-actions">
            <x-badge :status="$tenancy->status" label="Active tenancy" />
            <a href="{{ route('tenant.requests.create', ['type' => 'renewal']) }}" class="button button-dark button-sm">Ask about renewal</a>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Agreement</span>
                        <h3>Tenancy details</h3>
                    </div>
                    <span class="panel-ref">{{ $tenancy->reference }}</span>
                </div>

                <div class="detail-grid">
                    <div class="detail-item"><span>Tenant</span><strong>{{ $tenancy->tenant->name }}</strong></div>
                    <div class="detail-item"><span>Property</span><strong>{{ $tenancy->property->name }}</strong></div>
                    <div class="detail-item"><span>Unit</span><strong>{{ $tenancy->unit->unit_number }}</strong></div>
                    <div class="detail-item"><span>Property type</span><strong>{{ Str::headline($tenancy->property->type) }}</strong></div>
                    <div class="detail-item"><span>Start date</span><strong>{{ $tenancy->start_date->format('j F Y') }}</strong></div>
                    <div class="detail-item"><span>End date</span><strong>{{ $tenancy->end_date->format('j F Y') }}</strong></div>
                    <div class="detail-item"><span>Monthly rent</span><strong>{{ money($tenancy->rent_amount) }}</strong></div>
                    <div class="detail-item"><span>Payment schedule</span><strong>{{ ucfirst($tenancy->payment_frequency) }}, in advance</strong></div>
                </div>

                <div class="tenancy-progress tenancy-progress-roomy">
                    <div class="progress-head">
                        <span>Time remaining</span>
                        <span>{{ $tenancy->days_remaining }} days</span>
                    </div>
                    <div class="progress-track"><span style="width: {{ $tenancy->progress_percent }}%"></span></div>
                    <div class="progress-foot">
                        <span>{{ $tenancy->start_date->format('j M Y') }}</span>
                        <span>{{ $tenancy->end_date->format('j M Y') }}</span>
                    </div>
                </div>

                @if ($tenancy->notes)
                    <div class="inline-note">
                        <x-icon name="clipboard" :size="17" />
                        <span>{{ $tenancy->notes }}</span>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Deposit &amp; payments</span>
                        <h3>Money held and due</h3>
                    </div>
                </div>

                <div class="money-cards">
                    <div class="money-card">
                        <span>Deposit protected</span>
                        <strong>{{ money($tenancy->deposit_amount) }}</strong>
                        <small>{{ $tenancy->deposit_scheme }}</small>
                    </div>
                    <div class="money-card">
                        <span>Monthly rent</span>
                        <strong>{{ money($tenancy->rent_amount) }}</strong>
                        <small>Due on the 1st of each month</small>
                    </div>
                    <div class="money-card">
                        <span>Outstanding</span>
                        <strong>{{ money($tenancy->outstandingBalance()) }}</strong>
                        <small><a href="{{ route('tenant.payments.index') }}">View rent ledger</a></small>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your home</span>
                        <h3>About the property</h3>
                    </div>
                </div>

                <p class="body-copy">{{ $tenancy->property->description }}</p>

                <div class="amenity-row">
                    <span><x-icon name="home" :size="16" /> {{ $tenancy->property->bedrooms }} bedrooms</span>
                    <span><x-icon name="key" :size="16" /> {{ $tenancy->property->bathrooms }} bathrooms</span>
                    @if ($tenancy->property->size_sqft)
                        <span><x-icon name="chart" :size="16" /> {{ number_format($tenancy->property->size_sqft) }} sq ft</span>
                    @endif
                    <span><x-icon name="pin" :size="16" /> {{ $tenancy->property->postcode }}</span>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your team</span>
                        <h3>Who to contact</h3>
                    </div>
                </div>

                <div class="contact-card">
                    <span class="avatar">{{ $tenancy->property->manager?->initials ?? 'HV' }}</span>
                    <div>
                        <strong>{{ $tenancy->property->manager?->name ?? 'Harbour & Vine' }}</strong>
                        <span>{{ $tenancy->property->manager?->job_title ?? 'Property manager' }}</span>
                    </div>
                    <a href="mailto:{{ $tenancy->property->manager?->email ?? 'hello@harbourvine.test' }}" class="icon-btn" aria-label="Email your manager"><x-icon name="mail" :size="17" /></a>
                </div>

                <div class="contact-stack">
                    <a href="mailto:{{ $tenancy->property->manager?->email ?? 'hello@harbourvine.test' }}"><x-icon name="mail" :size="16" /> {{ $tenancy->property->manager?->email ?? 'hello@harbourvine.test' }}</a>
                    <a href="tel:02079460120"><x-icon name="phone" :size="16" /> 020 7946 0120</a>
                    <span><x-icon name="clock" :size="16" /> Mon–Fri, 9am–5pm</span>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Paperwork</span>
                        <h3>Tenancy documents</h3>
                    </div>
                </div>

                @forelse ($documents as $document)
                    <a href="{{ route('tenant.documents.download', $document) }}" class="document-row">
                        <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                        <div>
                            <strong>{{ $document->title }}</strong>
                            <span>{{ $document->category_label }} · {{ $document->human_size }}</span>
                        </div>
                        <x-icon name="download" :size="16" />
                    </a>
                @empty
                    <p class="body-copy">No documents have been added yet.</p>
                @endforelse
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="shield" :size="20" /></span>
                <h3>Thinking about what’s next?</h3>
                <p>If you would like to stay, extend or hand back your home, start a request and your manager will talk it through with you.</p>
                <a href="{{ route('tenant.requests.create', ['type' => 'renewal']) }}" class="button button-dark button-sm">Start a renewal request</a>
            </section>
        </div>
    </div>
@endsection
