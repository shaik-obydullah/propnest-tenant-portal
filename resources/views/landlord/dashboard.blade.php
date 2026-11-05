@extends('layouts.app')

@section('title', 'Your portfolio')
@section('eyebrow', 'Landlord view')

@section('content')
    <div class="page-heading">
        <div>
            <h2>A quiet view of the numbers.</h2>
            <p>{{ $stats['properties'] }} properties · {{ $stats['units'] }} units · {{ $stats['occupied'] }} occupied</p>
        </div>
        <x-badge status="green" label="Portfolio healthy" />
    </div>

    <div class="stat-strip">
        <div class="stat-card">
            <span class="stat-label">Monthly rent roll</span>
            <strong class="stat-value">{{ money($stats['monthlyRent']) }}</strong>
            <span class="stat-note">Across active tenancies</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Recent collections</span>
            <strong class="stat-value">{{ money($stats['collected']) }}</strong>
            <span class="stat-note">{{ $payments->count() }} latest payments</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Occupancy</span>
            <strong class="stat-value">{{ $stats['units'] > 0 ? round(($stats['occupied'] / $stats['units']) * 100) : 0 }}%</strong>
            <span class="stat-note">{{ $stats['occupied'] }} of {{ $stats['units'] }} units</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Open maintenance</span>
            <strong class="stat-value">{{ $stats['openJobs'] }}</strong>
            <span class="stat-note">Across your buildings</span>
        </div>
    </div>

    <div class="content-grid">
        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Properties</span><h3>Your buildings</h3></div></div>
                <div class="property-grid property-grid-compact">
                    @foreach ($properties as $property)
                        <a href="{{ route('landlord.properties.show', $property) }}" class="property-card">
                            <div class="property-card-visual property-accent-{{ $property->accent }}">
                                <span class="property-card-type">{{ Str::headline($property->type) }}</span>
                                <x-icon name="building" :size="46" />
                            </div>
                            <div class="property-card-body">
                                <h3>{{ $property->name }}</h3>
                                <p>{{ $property->postcode }}</p>
                                <div class="property-card-facts">
                                    <span><x-icon name="home" :size="14" /> {{ $property->bedrooms }} bed</span>
                                    <span><x-icon name="users" :size="14" /> {{ $property->units->where('status', 'occupied')->count() }}/{{ $property->units->count() }} let</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Agreements</span><h3>Tenancies</h3></div></div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Tenant</th><th>Property</th><th>Rent</th><th>Term</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($tenancies as $tenancy)
                                <tr>
                                    <td><div class="person-cell"><span class="avatar avatar-xs">{{ $tenancy->tenant->initials }}</span><strong>{{ $tenancy->tenant->name }}</strong></div></td>
                                    <td>{{ $tenancy->property->name }}<span class="table-sub">Unit {{ $tenancy->unit->unit_number }}</span></td>
                                    <td>{{ money($tenancy->rent_amount) }}</td>
                                    <td>{{ $tenancy->start_date->format('M Y') }} – {{ $tenancy->end_date->format('M Y') }}</td>
                                    <td><x-badge :status="$tenancy->status" :label="Str::headline($tenancy->status)" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel panel-dark">
                <span class="eyebrow eyebrow-light">Collections</span>
                <h3>Recent rent received</h3>
                <p class="panel-dark-copy">A snapshot of the latest payments recorded against your portfolio.</p>
                @foreach ($payments as $payment)
                    <div class="mini-list-row">
                        <span class="avatar avatar-xs">{{ $payment->tenancy->tenant->initials }}</span>
                        <div><strong>{{ $payment->tenancy->tenant->name }}</strong><span>{{ $payment->tenancy->property->name }} · {{ $payment->period_label }}</span></div>
                        <span>{{ money($payment->amount) }}</span>
                    </div>
                @endforeach
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Maintenance</span><h3>Works in progress</h3></div></div>
                @forelse ($properties->flatMap->maintenanceRequests->whereNotIn('status', ['completed', 'cancelled']) as $job)
                    <div class="maintenance-row">
                        <span class="maintenance-icon priority-{{ $job->priority }}"><x-icon name="wrench" :size="18" /></span>
                        <div class="maintenance-copy">
                            <div class="maintenance-title-row"><strong>{{ $job->title }}</strong><x-badge :status="$job->status" :label="$job->status_label" /></div>
                            <span>{{ $job->property->name }} · {{ $job->reference }}</span>
                        </div>
                    </div>
                @empty
                    <p class="body-copy">No open works across your portfolio.</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
