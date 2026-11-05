@extends('layouts.app')

@section('title', $tenancy->tenant->name)
@section('eyebrow', 'Tenancy · '.$tenancy->reference)

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('admin.tenancies.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to tenancies</a>
            <div class="page-heading-title">
                <h2>{{ $tenancy->tenant->name }}</h2>
                <x-badge :status="$tenancy->status" :label="Str::headline($tenancy->status)" />
            </div>
            <p>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }} · {{ $tenancy->reference }}</p>
        </div>
        <a href="mailto:{{ $tenancy->tenant->email }}" class="button button-ghost button-sm"><x-icon name="mail" :size="15" /> Email tenant</a>
    </div>

    <div class="stat-strip">
        <div class="stat-card">
            <span class="stat-label">Monthly rent</span>
            <strong class="stat-value">{{ money($tenancy->rent_amount) }}</strong>
            <span class="stat-note">Due on the 1st</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Outstanding</span>
            <strong class="stat-value {{ $tenancy->outstandingBalance() > 0 ? 'text-clay' : '' }}">{{ money($tenancy->outstandingBalance()) }}</strong>
            <span class="stat-note">{{ $tenancy->rentPayments->whereIn('status', ['pending', 'overdue'])->count() }} unpaid</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Deposit</span>
            <strong class="stat-value">{{ money($tenancy->deposit_amount) }}</strong>
            <span class="stat-note">{{ $tenancy->deposit_scheme }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Days remaining</span>
            <strong class="stat-value">{{ $tenancy->days_remaining }}</strong>
            <span class="stat-note">Ends {{ $tenancy->end_date->format('j M Y') }}</span>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Ledger</span><h3>Rent payments</h3></div>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Period</th><th>Due</th><th>Amount</th><th>Status</th><th class="align-right">Action</th></tr></thead>
                        <tbody>
                            @foreach ($tenancy->rentPayments->sortByDesc('due_date') as $payment)
                                <tr>
                                    <td><strong>{{ $payment->period_label }}</strong><span class="table-sub">{{ $payment->reference }}</span></td>
                                    <td>{{ $payment->due_date->format('j M Y') }}</td>
                                    <td>{{ money($payment->amount) }}</td>
                                    <td>
                                        <x-badge :status="$payment->is_overdue ? 'overdue' : $payment->status" :label="$payment->is_overdue ? 'Overdue' : ucfirst($payment->status)" />
                                        @if ($payment->receipt_number)<span class="table-sub">{{ $payment->receipt_number }}</span>@endif
                                    </td>
                                    <td class="align-right">
                                        @if ($payment->status === 'paid')
                                            <span class="text-muted">Recorded</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}">
                                                @csrf
                                                <button type="submit" class="button button-ghost button-xs">Mark paid</button>
                                            </form>
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
                    <div><span class="eyebrow">Repairs</span><h3>Maintenance requests</h3></div>
                    <a href="{{ route('admin.maintenance.index') }}" class="text-link">All jobs <x-icon name="arrow-right" :size="14" /></a>
                </div>
                @forelse ($tenancy->maintenanceRequests as $job)
                    <a href="{{ route('admin.maintenance.show', $job) }}" class="maintenance-row">
                        <span class="maintenance-icon priority-{{ $job->priority }}"><x-icon name="wrench" :size="18" /></span>
                        <div class="maintenance-copy">
                            <div class="maintenance-title-row"><strong>{{ $job->title }}</strong><x-badge :status="$job->status" :label="$job->status_label" /></div>
                            <span>{{ $job->reference }} · {{ $job->created_at->format('j M Y') }}</span>
                        </div>
                        <x-icon name="chevron-right" :size="16" />
                    </a>
                @empty
                    <p class="body-copy">No maintenance requests from this tenant.</p>
                @endforelse
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Services</span><h3>Tenant requests</h3></div>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Request</th><th>Type</th><th>Status</th><th>Submitted</th></tr></thead>
                        <tbody>
                            @forelse ($tenancy->serviceRequests as $serviceRequest)
                                <tr>
                                    <td><strong>{{ $serviceRequest->subject }}</strong><span class="table-sub">{{ $serviceRequest->reference }}</span></td>
                                    <td>{{ $serviceRequest->type_label }}</td>
                                    <td><x-badge :status="$serviceRequest->status" :label="$serviceRequest->status_label" /></td>
                                    <td>{{ $serviceRequest->created_at->format('j M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="table-empty">No service requests.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Tenant</span><h3>Contact &amp; agreement</h3></div></div>
                <div class="contact-card">
                    <span class="avatar">{{ $tenancy->tenant->initials }}</span>
                    <div><strong>{{ $tenancy->tenant->name }}</strong><span>{{ $tenancy->tenant->job_title }}</span></div>
                </div>
                <div class="detail-list">
                    <div><span>Email</span><strong>{{ $tenancy->tenant->email }}</strong></div>
                    <div><span>Phone</span><strong>{{ $tenancy->tenant->phone }}</strong></div>
                    <div><span>Start</span><strong>{{ $tenancy->start_date->format('j M Y') }}</strong></div>
                    <div><span>End</span><strong>{{ $tenancy->end_date->format('j M Y') }}</strong></div>
                    <div><span>Deposit scheme</span><strong>{{ $tenancy->deposit_scheme }}</strong></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><div><span class="eyebrow">Files</span><h3>Documents</h3></div></div>
                @forelse ($tenancy->documents as $document)
                    <div class="document-row">
                        <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                        <div><strong>{{ $document->title }}</strong><span>{{ $document->human_size }}</span></div>
                    </div>
                @empty
                    <p class="body-copy">No documents attached to this tenancy.</p>
                @endforelse
            </section>

            @if ($tenancy->notes)
                <section class="panel panel-tinted">
                    <span class="support-icon"><x-icon name="clipboard" :size="20" /></span>
                    <h3>Internal note</h3>
                    <p>{{ $tenancy->notes }}</p>
                </section>
            @endif
        </div>
    </div>
@endsection
