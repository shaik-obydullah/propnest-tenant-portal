@extends('layouts.app')

@section('title', $property->name)
@section('eyebrow', 'Your property')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('landlord.dashboard') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to portfolio</a>
            <h2>{{ $property->name }}</h2>
            <p>{{ $property->address }} · {{ $property->reference }}</p>
        </div>
    </div>

    <div class="property-hero property-accent-{{ $property->accent }}">
        <div class="property-hero-visual">
            <span class="property-card-type">{{ Str::headline($property->type) }}</span>
            <x-icon name="building" :size="72" />
        </div>
        <div class="property-hero-copy">
            <div class="property-hero-facts">
                <span><x-icon name="home" :size="15" /> {{ $property->bedrooms }} bed</span>
                <span><x-icon name="key" :size="15" /> {{ $property->bathrooms }} bath</span>
                <span><x-icon name="users" :size="15" /> {{ $property->units->where('status', 'occupied')->count() }}/{{ $property->units->count() }} let</span>
            </div>
            <p>{{ $property->description }}</p>
            <div class="property-hero-people">
                <div><span>Managed by</span><strong>{{ $property->manager?->name ?? 'Harbour & Vine' }}</strong></div>
                <div><span>Monthly rent roll</span><strong>{{ money($property->tenancies->where('status', 'active')->sum('rent_amount')) }}</strong></div>
            </div>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Units</span><h3>Occupancy</h3></div></div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Unit</th><th>Layout</th><th>Rent</th><th>Tenant</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($property->units as $unit)
                                @php($tenancy = $unit->tenancies->firstWhere('status', 'active'))
                                <tr>
                                    <td><strong>{{ $unit->unit_number }}</strong><span class="table-sub">{{ $unit->floor }}</span></td>
                                    <td>{{ $unit->bedrooms }} bed · {{ $unit->bathrooms }} bath</td>
                                    <td>{{ money($unit->rent_amount) }}</td>
                                    <td>{{ $tenancy?->tenant->name ?? 'Vacant' }}</td>
                                    <td><x-badge :status="$unit->status" :label="Str::headline($unit->status)" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Maintenance</span><h3>Works &amp; history</h3></div></div>
                @forelse ($property->maintenanceRequests as $job)
                    <div class="maintenance-row">
                        <span class="maintenance-icon priority-{{ $job->priority }}"><x-icon name="wrench" :size="18" /></span>
                        <div class="maintenance-copy">
                            <div class="maintenance-title-row"><strong>{{ $job->title }}</strong><x-badge :status="$job->status" :label="$job->status_label" /></div>
                            <span>{{ $job->reference }} · {{ $job->created_at->format('j M Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="body-copy">No maintenance activity at this property.</p>
                @endforelse
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Documents</span><h3>Property files</h3></div></div>
                @forelse ($property->documents as $document)
                    <div class="document-row">
                        <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                        <div><strong>{{ $document->title }}</strong><span>{{ $document->category_label }} · {{ $document->human_size }}</span></div>
                    </div>
                @empty
                    <p class="body-copy">No property documents available.</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
