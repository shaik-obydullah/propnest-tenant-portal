@extends('layouts.app')

@section('title', 'Pay rent')
@section('eyebrow', 'Secure demo checkout')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.payments.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to rent account</a>
            <h2>Make a rent payment.</h2>
            <p>{{ $tenancy->property->name }}, Unit {{ $tenancy->unit->unit_number }}</p>
        </div>
        <x-badge status="amber" label="Demo payment" />
    </div>

    <div class="checkout-grid">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Step 1</span>
                        <h3>What would you like to pay?</h3>
                    </div>
                    <strong>{{ money($balance) }} outstanding</strong>
                </div>

                @forelse ($outstanding as $payment)
                    <form method="POST" action="{{ route('tenant.payments.pay', $payment) }}" class="payment-option">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <span class="payment-option-icon"><x-icon name="pound" :size="19" /></span>
                        <div class="payment-option-copy">
                            <strong>{{ $payment->period_label }} rent</strong>
                            <span>Due {{ $payment->due_date->format('j F Y') }} · {{ $payment->reference }}</span>
                        </div>
                        <div class="payment-option-amount">
                            <strong>{{ money($payment->amount) }}</strong>
                            <x-badge :status="$payment->is_overdue ? 'overdue' : $payment->status" :label="$payment->is_overdue ? 'Overdue' : ucfirst($payment->status)" />
                        </div>
                        <button type="submit" class="button button-dark button-sm">Pay this</button>
                    </form>
                @empty
                    <div class="empty-inline">
                        <span class="empty-icon"><x-icon name="check-circle" :size="22" /></span>
                        <div><strong>Nothing to pay</strong><span>Your rent account is fully up to date.</span></div>
                    </div>
                @endforelse
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Step 2</span>
                        <h3>Payment method</h3>
                    </div>
                </div>

                <div class="card-preview">
                    <div class="card-preview-top">
                        <span>PropNest secure demo</span>
                        <x-icon name="lock" :size="16" />
                    </div>
                    <span class="card-preview-number">•••• •••• •••• 4242</span>
                    <div class="card-preview-bottom">
                        <span>ALEX MORGAN</span>
                        <span>12 / 29</span>
                    </div>
                </div>

                <div class="card-form-grid">
                    <div class="field field-span-2">
                        <label for="card_number">Card number</label>
                        <input id="card_number" type="text" value="4242 4242 4242 4242" disabled>
                    </div>
                    <div class="field">
                        <label for="expiry">Expiry</label>
                        <input id="expiry" type="text" value="12 / 29" disabled>
                    </div>
                    <div class="field">
                        <label for="cvc">CVC</label>
                        <input id="cvc" type="text" value="123" disabled>
                    </div>
                </div>

                <p class="form-note"><x-icon name="shield" :size="15" /> This is a demonstration checkout. No card details are captured, stored or charged. Pressing “Pay this” simply marks the demo payment as settled and generates a receipt.</p>
            </section>
        </div>

        <aside class="stack">
            <section class="panel panel-dark checkout-summary">
                <span class="eyebrow eyebrow-light">Summary</span>
                <h3>Payment summary</h3>
                <div class="summary-row"><span>Outstanding rent</span><strong>{{ money($balance) }}</strong></div>
                <div class="summary-row"><span>Demo processing fee</span><strong>£0.00</strong></div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total"><span>Total to pay</span><strong>{{ money($balance) }}</strong></div>
                <p class="panel-dark-copy">Choose a payment on the left to settle it. Your receipt appears immediately afterwards.</p>
                <div class="panel-dark-foot"><x-icon name="lock" :size="14" /> Encrypted in transit</div>
            </section>

            <section class="panel">
                <span class="support-icon"><x-icon name="lifebuoy" :size="20" /></span>
                <h3>Need help with rent?</h3>
                <p>If a payment is difficult this month, please talk to us before it becomes a problem. We can discuss a realistic arrangement.</p>
                <a href="{{ route('tenant.requests.create', ['type' => 'enquiry']) }}" class="button button-ghost button-sm">Contact the team</a>
            </section>
        </aside>
    </div>
@endsection
