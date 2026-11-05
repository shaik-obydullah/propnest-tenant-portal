@extends('layouts.app')

@section('title', 'Good morning, '.auth()->user()->first_name)
@section('eyebrow', now()->format('l, j F Y').' · Agency overview')

@section('content')
    <div class="page-heading">
        <div>
            <h2>The portfolio, at a glance.</h2>
            <p>{{ $properties->count() }} properties · {{ $stats['activeTenancies'] }} active tenancies · {{ $stats['openMaintenance'] }} open jobs</p>
        </div>
        <div class="page-heading-actions">
            <a href="{{ route('admin.properties.create') }}" class="button button-dark button-sm"><x-icon name="plus" :size="15" /> Add property</a>
            <a href="{{ route('admin.maintenance.index') }}" class="button button-ghost button-sm">Maintenance queue</a>
        </div>
    </div>

    <div class="stat-strip">
        <div class="stat-card">
            <span class="stat-label">Rent collected this month</span>
            <strong class="stat-value">{{ money($stats['collectedThisMonth']) }}</strong>
            <span class="stat-note stat-up"><x-icon name="arrow-up-right" :size="13" /> On track</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Outstanding arrears</span>
            <strong class="stat-value {{ $stats['arrears'] > 0 ? 'text-clay' : '' }}">{{ money($stats['arrears']) }}</strong>
            <span class="stat-note">{{ $unpaid->count() }} payments to chase</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Occupancy</span>
            <strong class="stat-value">{{ $stats['units'] > 0 ? round(($stats['occupancy'] / $stats['units']) * 100) : 0 }}%</strong>
            <span class="stat-note">{{ $stats['occupancy'] }} of {{ $stats['units'] }} units let</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Expiring in 90 days</span>
            <strong class="stat-value">{{ $stats['expiringSoon'] }}</strong>
            <span class="stat-note">Renewal conversations</span>
        </div>
    </div>

    <div class="content-grid">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Cashflow</span>
                        <h3>Rent collected</h3>
                    </div>
                    <a href="{{ route('admin.tenancies.index') }}" class="text-link">Tenancies <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="chart">
                    @foreach ($months as $month)
                        <div class="chart-column">
                            <span class="chart-value">{{ money_compact($month['amount']) }}</span>
                            <div class="chart-bar"><span style="height: {{ max(6, round(($month['amount'] / $maxMonth) * 100)) }}%"></span></div>
                            <span class="chart-label">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Operations</span>
                        <h3>Maintenance queue</h3>
                    </div>
                    <a href="{{ route('admin.maintenance.index') }}" class="text-link">Open queue <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Request</th>
                                <th>Property</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="align-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($openMaintenance->take(6) as $job)
                                <tr>
                                    <td>
                                        <strong>{{ $job->title }}</strong>
                                        <span class="table-sub">{{ $job->reference }} · {{ $job->category }}</span>
                                    </td>
                                    <td>{{ $job->property->name }}<span class="table-sub">{{ $job->tenancy->tenant->name }}</span></td>
                                    <td><x-badge :status="$job->priority" :label="$job->priority_label" /></td>
                                    <td><x-badge :status="$job->status" :label="$job->status_label" /></td>
                                    <td class="align-right"><a href="{{ route('admin.maintenance.show', $job) }}" class="table-link">Open <x-icon name="arrow-up-right" :size="13" /></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="table-empty">No open jobs. The queue is clear.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Collections</span>
                        <h3>Recent payments</h3>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Tenant</th><th>Property</th><th>Amount</th><th>Paid</th><th>Method</th></tr></thead>
                        <tbody>
                            @foreach ($recentPayments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->tenancy->tenant->name }}</strong><span class="table-sub">{{ $payment->reference }}</span></td>
                                    <td>{{ $payment->tenancy->property->name }}</td>
                                    <td>{{ money($payment->amount) }}</td>
                                    <td>{{ $payment->paid_at?->format('j M Y') }}</td>
                                    <td>{{ $payment->method }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel panel-dark">
                <span class="eyebrow eyebrow-light">Attention</span>
                <h3>{{ $unpaid->count() ? 'Payments to follow up' : 'Rent is on track' }}</h3>
                <p class="panel-dark-copy">{{ $unpaid->count() ? money($stats['arrears']).' is outstanding across '.$unpaid->count().' payments.' : 'No arrears across the portfolio this month.' }}</p>
                @foreach ($unpaid->take(4) as $payment)
                    <div class="mini-list-row">
                        <span class="avatar avatar-xs">{{ $payment->tenancy->tenant->initials }}</span>
                        <div>
                            <strong>{{ $payment->tenancy->tenant->name }}</strong>
                            <span>{{ $payment->period_label }} · {{ $payment->due_date->format('j M') }}</span>
                        </div>
                        <span>{{ money($payment->amount) }}</span>
                    </div>
                @endforeach
                <a href="{{ route('admin.tenancies.index', ['status' => 'active']) }}" class="button button-light button-block">Review tenancies</a>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Portfolio</span>
                        <h3>Properties</h3>
                    </div>
                    <a href="{{ route('admin.properties.index') }}" class="text-link">All <x-icon name="arrow-right" :size="14" /></a>
                </div>
                @foreach ($properties as $property)
                    <a href="{{ route('admin.properties.show', $property) }}" class="property-list-row">
                        <span class="property-thumb property-accent-{{ $property->accent }}"><x-icon name="building" :size="18" /></span>
                        <div>
                            <strong>{{ $property->name }}</strong>
                            <span>{{ $property->units->count() }} units · {{ $property->postcode }}</span>
                        </div>
                        <span class="property-occupancy">{{ $property->units->where('status', 'occupied')->count() }}/{{ $property->units->count() }}</span>
                    </a>
                @endforeach
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Coming up</span>
                        <h3>Tenancy expiries</h3>
                    </div>
                </div>
                @foreach ($upcomingExpiries as $tenancy)
                    <a href="{{ route('admin.tenancies.show', $tenancy) }}" class="expiry-row">
                        <span class="expiry-date"><strong>{{ $tenancy->end_date->format('d') }}</strong>{{ $tenancy->end_date->format('M') }}</span>
                        <div>
                            <strong>{{ $tenancy->tenant->name }}</strong>
                            <span>{{ $tenancy->property->name }} · {{ $tenancy->days_remaining }} days</span>
                        </div>
                        <x-icon name="chevron-right" :size="15" />
                    </a>
                @endforeach
            </section>
        </div>
    </div>
@endsection
