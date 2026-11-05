@extends('layouts.app')

@section('title', 'Rent & payments')
@section('eyebrow', 'Your rent account')

@section('content')
    <div class="page-heading">
        <div>
            <h2>A clear view of every payment.</h2>
            <p>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }} · {{ $tenancy->reference }}</p>
        </div>
        <a href="{{ route('tenant.payments.checkout') }}" class="button button-dark">Make a payment <x-icon name="arrow-right" :size="16" /></a>
    </div>

    <div class="stat-strip">
        <div class="stat-card">
            <span class="stat-label">Outstanding balance</span>
            <strong class="stat-value {{ $balance > 0 ? 'text-clay' : '' }}">{{ money($balance) }}</strong>
            <span class="stat-note">{{ $balance > 0 ? 'Payment needed' : 'You are up to date' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Monthly rent</span>
            <strong class="stat-value">{{ money($tenancy->rent_amount) }}</strong>
            <span class="stat-note">Due on the 1st</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Paid this tenancy</span>
            <strong class="stat-value">{{ money($paidTotal) }}</strong>
            <span class="stat-note">{{ $payments->where('status', 'paid')->count() }} payments</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Next due</span>
            <strong class="stat-value">{{ $nextPayment ? $nextPayment->due_date->format('j M') : '—' }}</strong>
            <span class="stat-note">{{ $nextPayment ? money($nextPayment->amount) : 'All settled' }}</span>
        </div>
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Ledger</span>
                        <h3>Payment history</h3>
                    </div>
                    <span class="panel-ref">{{ $payments->count() }} entries</span>
                </div>

                <div class="table-wrap">
                    <table class="table table-roomy">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Due date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="align-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->period_label }}</strong>
                                        <span class="table-sub">{{ $payment->reference }}</span>
                                    </td>
                                    <td>{{ $payment->due_date->format('j M Y') }}</td>
                                    <td>{{ money($payment->amount) }}</td>
                                    <td>
                                        <x-badge :status="$payment->is_overdue ? 'overdue' : $payment->status" :label="$payment->is_overdue ? 'Overdue' : ucfirst($payment->status)" />
                                        @if ($payment->paid_at)
                                            <span class="table-sub">{{ $payment->paid_at->format('j M Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="align-right">
                                        @if ($payment->status === 'paid')
                                            <a href="{{ route('tenant.payments.receipt', $payment) }}" class="table-link">Receipt <x-icon name="arrow-up-right" :size="13" /></a>
                                        @else
                                            <a href="{{ route('tenant.payments.checkout') }}" class="table-link">Pay now <x-icon name="arrow-right" :size="13" /></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel panel-dark">
                <span class="eyebrow eyebrow-light">Balance</span>
                <h3>{{ $balance > 0 ? 'One payment to settle.' : 'All settled.' }}</h3>
                <p class="panel-dark-copy">
                    @if ($balance > 0)
                        Your outstanding rent is {{ money($balance) }}. Pay securely in the demo checkout and your receipt will be generated instantly.
                    @else
                        There is nothing outstanding on your rent account. Your next statement will appear here when it is issued.
                    @endif
                </p>
                @if ($balance > 0)
                    <a href="{{ route('tenant.payments.checkout') }}" class="button button-light button-block">Pay {{ money($balance) }}</a>
                @else
                    <a href="{{ route('tenant.documents.index') }}" class="button button-light button-block">View statements</a>
                @endif
                <div class="panel-dark-foot"><x-icon name="shield" :size="14" /> Payments are recorded against your tenancy</div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">How you pay</span>
                        <h3>Payment details</h3>
                    </div>
                </div>
                <div class="detail-list">
                    <div><span>Frequency</span><strong>{{ ucfirst($tenancy->payment_frequency) }}</strong></div>
                    <div><span>Due day</span><strong>1st of the month</strong></div>
                    <div><span>Last method</span><strong>Direct debit</strong></div>
                    <div><span>Receipts</span><strong>Available instantly</strong></div>
                </div>
                <div class="inline-note inline-note-small">
                    <x-icon name="alert" :size="16" />
                    <span>Struggling to pay? Speak to your manager early — support is available.</span>
                </div>
            </section>
        </div>
    </div>
@endsection
