@extends('layouts.app')

@section('title', $property->name)
@section('eyebrow', 'Property · '.$property->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.properties.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to properties</a>
            <div class="page-heading-title">
                <h2>{{ $property->name }}</h2>
                <x-badge :status="$property->status" :label="Str::headline($property->status)" />
            </div>
            <p>{{ $property->address }}</p>
        </div>
        <a href="{{ route('admin.properties.edit', $property) }}" class="button button-dark button-sm"><x-icon name="edit" :size="15" /> Edit property</a>
    </div>

    <div class="property-hero property-accent-{{ $property->accent }}">
        <div class="property-hero-visual">
            <span class="property-card-type">{{ Str::headline($property->type) }}</span>
            <x-icon name="building" :size="72" />
            <span class="property-hero-ref">{{ $property->reference }}</span>
        </div>
        <div class="property-hero-copy">
            <div class="property-hero-facts">
                <span><x-icon name="home" :size="15" /> {{ $property->bedrooms }} bedrooms</span>
                <span><x-icon name="key" :size="15" /> {{ $property->bathrooms }} bathrooms</span>
                @if ($property->size_sqft)<span><x-icon name="chart" :size="15" /> {{ number_format($property->size_sqft) }} sq ft</span>@endif
                <span><x-icon name="building" :size="15" /> {{ $property->units->count() }} units</span>
            </div>
            <p>{{ $property->description }}</p>
            <div class="property-hero-people">
                <div><span>Landlord</span><strong>{{ $property->landlord?->name ?? 'Unassigned' }}</strong></div>
                <div><span>Manager</span><strong>{{ $property->manager?->name ?? 'Unassigned' }}</strong></div>
            </div>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Units</span><h3>Units &amp; occupancy</h3></div>
                    <span class="panel-ref">{{ $property->units->count() }} units</span>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Unit</th><th>Layout</th><th>Rent</th><th>Status</th><th>Tenant</th></tr></thead>
                        <tbody>
                            @foreach ($property->units as $unit)
                                @php($tenancy = $unit->tenancies->firstWhere('status', 'active'))
                                <tr>
                                    <td><strong>{{ $unit->unit_number }}</strong><span class="table-sub">{{ $unit->floor }}</span></td>
                                    <td>{{ $unit->bedrooms }} bed · {{ $unit->bathrooms }} bath</td>
                                    <td>{{ money($unit->rent_amount) }}</td>
                                    <td><x-badge :status="$unit->status" :label="Str::headline($unit->status)" /></td>
                                    <td>
                                        @if ($tenancy)
                                            <a href="{{ route('admin.tenancies.show', $tenancy) }}" class="table-link">{{ $tenancy->tenant->name }} <x-icon name="arrow-up-right" :size="12" /></a>
                                        @else
                                            <span class="text-muted">Vacant</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Repairs</span><h3>Maintenance history</h3></div>
                    <a href="{{ route('admin.maintenance.index') }}" class="text-link">Queue <x-icon name="arrow-right" :size="14" /></a>
                </div>
                @forelse ($property->maintenanceRequests as $job)
                    <a href="{{ route('admin.maintenance.show', $job) }}" class="maintenance-row">
                        <span class="maintenance-icon priority-{{ $job->priority }}"><x-icon name="wrench" :size="18" /></span>
                        <div class="maintenance-copy">
                            <div class="maintenance-title-row">
                                <strong>{{ $job->title }}</strong>
                                <x-badge :status="$job->status" :label="$job->status_label" />
                            </div>
                            <span>{{ $job->reference }} · {{ $job->tenancy->tenant->name }} · {{ $job->created_at->format('j M Y') }}</span>
                        </div>
                        <x-icon name="chevron-right" :size="16" />
                    </a>
                @empty
                    <p class="body-copy">No maintenance requests for this property.</p>
                @endforelse
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Overview</span><h3>Property facts</h3></div>
                </div>
                <div class="detail-list">
                    <div><span>Reference</span><strong>{{ $property->reference }}</strong></div>
                    <div><span>Type</span><strong>{{ Str::headline($property->type) }}</strong></div>
                    <div><span>Status</span><strong>{{ Str::headline($property->status) }}</strong></div>
                    <div><span>Postcode</span><strong>{{ $property->postcode }}</strong></div>
                    <div><span>Added</span><strong>{{ $property->created_at->format('j M Y') }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Files</span><h3>Property documents</h3></div>
                </div>
                @forelse ($property->documents as $document)
                    <div class="document-row">
                        <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                        <div><strong>{{ $document->title }}</strong><span>{{ $document->category_label }} · {{ $document->human_size }}</span></div>
                    </div>
                @empty
                    <p class="body-copy">No property documents uploaded.</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
