@extends('layouts.app')

@section('title', 'Properties')
@section('eyebrow', 'Portfolio')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Your managed portfolio.</h2>
            <p>Buildings, units, landlords and the people living in them.</p>
        </div>
        <a href="{{ route('admin.properties.create') }}" class="button button-dark button-sm"><x-icon name="plus" :size="15" /> Add property</a>
    </div>

    <form method="GET" action="{{ route('admin.properties.index') }}" class="toolbar">
        <div class="search-field">
            <x-icon name="search" :size="17" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by name, reference or postcode…">
        </div>
        <button type="submit" class="button button-ghost button-sm">Search</button>
        @if ($search)
            <a href="{{ route('admin.properties.index') }}" class="text-link">Clear</a>
        @endif
        <span class="toolbar-count">{{ $properties->total() }} properties</span>
    </form>

    <div class="property-grid">
        @forelse ($properties as $property)
            <a href="{{ route('admin.properties.show', $property) }}" class="property-card">
                <div class="property-card-visual property-accent-{{ $property->accent }}">
                    <span class="property-card-type">{{ Str::headline($property->type) }}</span>
                    <span class="badge {{ $property->status === 'occupied' ? 'badge-green' : 'badge-amber' }}"><span class="badge-dot"></span>{{ Str::headline($property->status) }}</span>
                    <x-icon name="building" :size="54" />
                </div>
                <div class="property-card-body">
                    <span class="panel-ref">{{ $property->reference }}</span>
                    <h3>{{ $property->name }}</h3>
                    <p>{{ $property->address }}</p>
                    <div class="property-card-facts">
                        <span><x-icon name="home" :size="14" /> {{ $property->bedrooms }} bed</span>
                        <span><x-icon name="key" :size="14" /> {{ $property->bathrooms }} bath</span>
                        <span><x-icon name="users" :size="14" /> {{ $property->units->where('status', 'occupied')->count() }}/{{ $property->units->count() }} let</span>
                    </div>
                    <div class="property-card-foot">
                        <span>{{ $property->landlord?->name ?? 'No landlord' }}</span>
                        <span class="table-link">View <x-icon name="arrow-right" :size="13" /></span>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="building" :size="28" /></span>
                <h3>No properties found</h3>
                <p>Try a different search, or add your first property to the portfolio.</p>
                <a href="{{ route('admin.properties.create') }}" class="button button-dark button-sm">Add property</a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">{{ $properties->links() }}</div>
@endsection
