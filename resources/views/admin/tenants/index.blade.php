@extends('layouts.app')

@section('title', 'Tenants')
@section('eyebrow', 'Residents')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Your residents.</h2>
            <p>{{ $tenantCount }} tenant accounts across the portfolio, with their current homes and rent health.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.tenants.index') }}" class="toolbar">
        <div class="search-field">
            <x-icon name="search" :size="17" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Search by tenant name, email or property…">
        </div>
        <button type="submit" class="button button-ghost button-sm">Search</button>
        @if ($search)
            <a href="{{ route('admin.tenants.index') }}" class="text-link">Clear</a>
        @endif
        <span class="toolbar-count">{{ $tenancies->total() }} active tenancies</span>
    </form>

    <div class="tenant-grid">
        @foreach ($tenancies as $tenancy)
            <a href="{{ route('admin.tenants.show', $tenancy) }}" class="tenant-card">
                <div class="tenant-card-head">
                    <span class="avatar">{{ $tenancy->tenant->initials }}</span>
                    <div>
                        <strong>{{ $tenancy->tenant->name }}</strong>
                        <span>{{ $tenancy->tenant->email }}</span>
                    </div>
                    <x-badge :status="$tenancy->status" :label="Str::headline($tenancy->status)" />
                </div>
                <div class="tenant-card-property">
                    <span class="property-thumb property-accent-{{ $tenancy->property->accent }}"><x-icon name="building" :size="18" /></span>
                    <div><strong>{{ $tenancy->property->name }}</strong><span>Unit {{ $tenancy->unit->unit_number }}</span></div>
                </div>
                <div class="tenant-card-facts">
                    <span><x-icon name="card" :size="14" /> {{ money($tenancy->rent_amount) }}/mo</span>
                    <span><x-icon name="calendar" :size="14" /> Ends {{ $tenancy->end_date->format('M Y') }}</span>
                    <span class="{{ $tenancy->outstandingBalance() > 0 ? 'text-clay' : 'text-green' }}"><x-icon name="pound" :size="14" /> {{ money($tenancy->outstandingBalance()) }} due</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="pagination-wrap">{{ $tenancies->links() }}</div>
@endsection
