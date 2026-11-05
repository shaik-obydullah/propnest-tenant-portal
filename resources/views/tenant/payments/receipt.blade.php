@extends('layouts.app')

@section('title', 'Payment receipt')
@section('eyebrow', 'Rent account')

@section('content')
    <div class="page-heading no-print">
        <div>
            <a href="{{ route('tenant.payments.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to rent account</a>
            <h2>Receipt {{ $payment->receipt_number }}</h2>
            <p>Keep this for your records. It is also stored in your document library.</p>
        </div>
        <div class="page-heading-actions">
            <button type="button" class="button button-ghost" data-print><x-icon name="download" :size="16" /> Print / save PDF</button>
        </div>
    </div>

    <div class="receipt-wrap">
        <article class="receipt">
            <header class="receipt-head">
                <div>
                    <x-logo />
                    <p>Harbour &amp; Vine Property Group<br>18 Cursitor Street, London EC4A 1LT</p>
                </div>
                <div class="receipt-meta">
                    <span class="receipt-label">Receipt</span>
                    <strong>{{ $payment->receipt_number }}</strong>
                    <span>Issued {{ $payment->paid_at?->format('j F Y') ?? now()->format('j F Y') }}</span>
                </div>
            </header>

            <div class="receipt-rule"></div>

            <section class="receipt-parties">
                <div>
                    <span class="receipt-label">Received from</span>
                    <strong>{{ $tenancy->tenant->name }}</strong>
                    <span>{{ $tenancy->tenant->email }}</span>
                </div>
                <div>
                    <span class="receipt-label">Property</span>
                    <strong>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }}</strong>
                    <span>{{ $tenancy->property->address }}</span>
                </div>
            </section>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Period</th>
                        <th class="align-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Rent payment<br><span>Tenancy {{ $tenancy->reference }}</span></td>
                        <td>{{ $payment->period_start->format('j M') }} – {{ $payment->period_end->format('j M Y') }}</td>
                        <td class="align-right">{{ money($payment->amount) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Total paid</td>
                        <td class="align-right">{{ money($payment->amount) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="receipt-details">
                <div><span>Payment date</span><strong>{{ $payment->paid_at?->format('j F Y, H:i') }}</strong></div>
                <div><span>Method</span><strong>{{ $payment->method }}</strong></div>
                <div><span>Reference</span><strong>{{ $payment->reference }}</strong></div>
                <div><span>Status</span><strong>Paid in full</strong></div>
            </div>

            <div class="receipt-note">
                <x-icon name="check-circle" :size="18" />
                <p>This receipt confirms the payment above has been applied to your rent account. No signature is required.</p>
            </div>

            <footer class="receipt-foot">
                <span>PropNest · Resident portal</span>
                <span>Questions? hello@harbourvine.test · 020 7946 0182</span>
            </footer>
        </article>
    </div>
@endsection
