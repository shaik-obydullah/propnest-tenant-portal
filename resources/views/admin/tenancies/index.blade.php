@extends('layouts.app')

@section('title', 'Tenancies')
@section('eyebrow', 'Agreements')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Every tenancy, in view.</h2>
            <p>Agreement dates, rent, tenants and the health of each let.</p>
        </div>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('admin.tenancies.index') }}" class="{{ $status ? '' : 'is-active' }}">All tenancies</a>
        <a href="{{ route('admin.tenancies.index', ['status' => 'active']) }}" class="{{ $status === 'active' ? 'is-active' : '' }}">Active</a>
        <a href="{{ route('admin.tenancies.index', ['status' => 'ended']) }}" class="{{ $status === 'ended' ? 'is-active' : '' }}">Ended</a>
        <a href="{{ route('admin.tenancies.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'is-active' : '' }}">Pending</a>
    </div>

    <form method="GET" action="{{ route('admin.tenancies.index') }}" class="toolbar">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div class="search-field">
            <x-icon name="search" :size="17" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Search tenant, property or reference…">
        </div>
        <button type="submit" class="button button-ghost button-sm">Search</button>
        @if ($search)
            <a href="{{ route('admin.tenancies.index') }}" class="text-link">Clear</a>
        @endif
        <span class="toolbar-count">{{ $tenancies->total() }} tenancies</span>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table class="table table-roomy">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property</th>
                        <th>Term</th>
                        <th>Rent</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th class="align-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tenancies as $tenancy)
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <span class="avatar avatar-xs">{{ $tenancy->tenant->initials }}</span>
                                    <div><strong>{{ $tenancy->tenant->name }}</strong><span class="table-sub">{{ $tenancy->tenant->email }}</span></div>
                                </div>
                            </td>
                            <td><strong>{{ $tenancy->property->name }}</strong><span class="table-sub">Unit {{ $tenancy->unit->unit_number }}</span></td>
                            <td>{{ $tenancy->start_date->format('j M Y') }}<span class="table-sub">to {{ $tenancy->end_date->format('j M Y') }}</span></td>
                            <td>{{ money($tenancy->rent_amount) }}<span class="table-sub">per month</span></td>
                            <td class="{{ $tenancy->outstandingBalance() > 0 ? 'text-clay' : '' }}">{{ money($tenancy->outstandingBalance()) }}</td>
                            <td><x-badge :status="$tenancy->status" :label="Str::headline($tenancy->status)" /></td>
                            <td class="align-right"><a href="{{ route('admin.tenancies.show', $tenancy) }}" class="table-link">Open <x-icon name="arrow-up-right" :size="13" /></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="table-empty">No tenancies match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="pagination-wrap">{{ $tenancies->links() }}</div>
@endsection
