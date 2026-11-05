@extends('layouts.app')

@section('title', 'Welcome back, '.$tenancy->tenant->first_name)
@section('eyebrow', now()->format('l, j F Y'))

@section('content')
    <div class="dashboard-top">
        <section class="welcome-card">
            <div class="welcome-card-copy">
                <span class="eyebrow eyebrow-light">Your home this month</span>
                <h2>{{ $tenancy->property->name }} <span>· Unit {{ $tenancy->unit->unit_number }}</span></h2>
                <p>{{ $tenancy->property->address_line_1 }}, {{ $tenancy->property->city }} {{ $tenancy->property->postcode }}</p>

                <div class="welcome-facts">
                    <div>
                        <span>Tenancy ends</span>
                        <strong>{{ $tenancy->end_date->format('j M Y') }}</strong>
                    </div>
                    <div>
                        <span>Monthly rent</span>
                        <strong>{{ money($tenancy->rent_amount) }}</strong>
                    </div>
                    <div>
                        <span>Deposit held</span>
                        <strong>{{ money($tenancy->deposit_amount) }}</strong>
                    </div>
                </div>

                <div class="welcome-actions">
                    <a href="{{ route('tenant.tenancy.show') }}" class="button button-light button-sm">View my tenancy</a>
                    <a href="{{ route('tenant.maintenance.create') }}" class="button button-ghost-light button-sm"><x-icon name="plus" :size="15" /> Report an issue</a>
                </div>
            </div>

            <div class="welcome-art" aria-hidden="true">
                <svg viewBox="0 0 420 300" fill="none" preserveAspectRatio="xMidYMax slice">
                    <circle cx="330" cy="72" r="42" fill="#E9B66A" fill-opacity=".55"/>
                    <path d="M0 245h420" stroke="#F5EBD8" stroke-opacity=".18" stroke-width="2"/>
                    <path d="M26 245V166l42-25 42 25v79" fill="#123A31"/>
                    <path d="M38 177h15v14H38zM60 177h15v14H60zM38 200h15v14H38zM60 200h15v14H60zM38 223h15v14H38zM60 223h15v14H60z" fill="#E9B66A" fill-opacity=".4"/>
                    <path d="M128 245V118l58-34 58 34v127" fill="#174238"/>
                    <path d="M144 137h19v19h-19zM173 137h19v19h-19zM202 137h19v19h-19zM144 169h19v19h-19zM173 169h19v19h-19zM202 169h19v19h-19zM144 201h19v19h-19zM173 201h19v19h-19zM202 201h19v19h-19z" fill="#F5EBD8" fill-opacity=".2"/>
                    <path d="M262 245V188l42-24 42 24v57" fill="#1C5144"/>
                    <path d="M276 201h16v16h-16zM303 201h16v16h-16zM276 226h16v16h-16zM303 226h16v16h-16z" fill="#E9B66A" fill-opacity=".45"/>
                    <path d="M0 245h420v55H0z" fill="#0F2F28"/>
                </svg>
            </div>
        </section>

        <aside class="balance-card {{ $balance > 0 ? 'has-balance' : 'is-clear' }}">
            <div class="balance-head">
                <span>{{ $balance > 0 ? 'Outstanding balance' : 'Rent account' }}</span>
                @if ($balance > 0)
                    <span class="badge badge-light"><span class="badge-dot"></span>{{ $unpaidCount }} {{ Str::plural('payment', $unpaidCount) }}</span>
                @else
                    <span class="badge badge-light"><span class="badge-dot"></span>All clear</span>
                @endif
            </div>

            @if ($balance > 0)
                <strong class="balance-amount">{{ money($balance) }}</strong>
                <p class="balance-copy">
                    @if ($nextPayment)
                        {{ $nextPayment->period_label }} rent was due {{ $nextPayment->due_date->format('j F') }}.
                    @endif
                    Settle it securely from your rent account.
                </p>
                <a href="{{ route('tenant.payments.checkout') }}" class="button button-light button-block">Pay rent now <x-icon name="arrow-right" :size="16" /></a>
                <div class="balance-foot">
                    <span><x-icon name="shield" :size="14" /> Secure demo payment</span>
                    <a href="{{ route('tenant.payments.index') }}">View ledger</a>
                </div>
            @else
                <strong class="balance-amount">{{ money($paidThisYear) }}</strong>
                <p class="balance-copy">Paid this year. Your rent account is up to date — nothing to do.</p>
                <a href="{{ route('tenant.payments.index') }}" class="button button-light button-block">View payment history <x-icon name="arrow-right" :size="16" /></a>
                <div class="balance-foot">
                    <span><x-icon name="check-circle" :size="14" /> Next payment scheduled</span>
                    <a href="{{ route('tenant.tenancy.show') }}">Tenancy</a>
                </div>
            @endif
        </aside>
    </div>

    <div class="quick-actions">
        <a href="{{ route('tenant.payments.checkout') }}" class="quick-action">
            <span class="quick-icon"><x-icon name="card" :size="20" /></span>
            <span><strong>Pay rent</strong><small>Card or bank</small></span>
            <x-icon name="chevron-right" :size="16" />
        </a>
        <a href="{{ route('tenant.maintenance.create') }}" class="quick-action">
            <span class="quick-icon quick-icon-clay"><x-icon name="wrench" :size="20" /></span>
            <span><strong>Report an issue</strong><small>Repairs & faults</small></span>
            <x-icon name="chevron-right" :size="16" />
        </a>
        <a href="{{ route('tenant.requests.create') }}" class="quick-action">
            <span class="quick-icon quick-icon-gold"><x-icon name="clipboard" :size="20" /></span>
            <span><strong>Make a request</strong><small>Renewal, pets, details</small></span>
            <x-icon name="chevron-right" :size="16" />
        </a>
        <a href="{{ route('tenant.documents.index') }}" class="quick-action">
            <span class="quick-icon quick-icon-ocean"><x-icon name="folder" :size="20" /></span>
            <span><strong>Find a document</strong><small>Agreements & receipts</small></span>
            <x-icon name="chevron-right" :size="16" />
        </a>
    </div>

    <div class="content-grid">
        <div class="stack">
            <section class="panel tenancy-panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your tenancy</span>
                        <h3>Everything in order</h3>
                    </div>
                    <a href="{{ route('tenant.tenancy.show') }}" class="text-link">Details <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="tenancy-snapshot">
                    <div class="property-thumb property-accent-{{ $tenancy->property->accent }}">
                        <x-icon name="building" :size="28" />
                    </div>
                    <div class="tenancy-snapshot-copy">
                        <strong>{{ $tenancy->property->name }}</strong>
                        <span>Unit {{ $tenancy->unit->unit_number }} · {{ $tenancy->property->bedrooms }} bed · {{ $tenancy->property->bathrooms }} bath</span>
                        <span>{{ $tenancy->property->address_line_1 }}, {{ $tenancy->property->city }} {{ $tenancy->property->postcode }}</span>
                    </div>
                    <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
                </div>

                <div class="tenancy-progress">
                    <div class="progress-head">
                        <span>Tenancy period</span>
                        <span>{{ $tenancy->start_date->format('j M Y') }} — {{ $tenancy->end_date->format('j M Y') }}</span>
                    </div>
                    <div class="progress-track"><span style="width: {{ $tenancy->progress_percent }}%"></span></div>
                    <div class="progress-foot">
                        <span>{{ $tenancy->progress_percent }}% complete</span>
                        <span class="progress-remaining"><x-icon name="calendar" :size="13" /> {{ $tenancy->days_remaining }} days remaining</span>
                    </div>
                </div>

                <div class="tenancy-facts">
                    <div><span>Tenancy ref.</span><strong>{{ $tenancy->reference }}</strong></div>
                    <div><span>Deposit scheme</span><strong>{{ $tenancy->deposit_scheme }}</strong></div>
                    <div><span>Payment frequency</span><strong>{{ ucfirst($tenancy->payment_frequency) }}</strong></div>
                </div>

                @if ($tenancy->is_ending_soon)
                    <div class="inline-note inline-note-amber">
                        <x-icon name="clock" :size="17" />
                        <span>Your tenancy ends in {{ $tenancy->days_remaining }} days. You can ask to renew at any time.</span>
                        <a href="{{ route('tenant.requests.create', ['type' => 'renewal']) }}">Start a renewal</a>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Repairs</span>
                        <h3>Open maintenance</h3>
                    </div>
                    <a href="{{ route('tenant.maintenance.index') }}" class="text-link">All requests <x-icon name="arrow-right" :size="14" /></a>
                </div>

                @forelse ($openMaintenance as $request)
                    <a href="{{ route('tenant.maintenance.show', $request) }}" class="maintenance-row">
                        <span class="maintenance-icon priority-{{ $request->priority }}"><x-icon name="wrench" :size="18" /></span>
                        <div class="maintenance-copy">
                            <div class="maintenance-title-row">
                                <strong>{{ $request->title }}</strong>
                                <x-badge :status="$request->status" :label="$request->status_label" />
                            </div>
                            <span>{{ $request->reference }} · {{ $request->category }}
                                @if ($request->scheduled_for)
                                    · Visit {{ $request->scheduled_for->format('j M, H:i') }}
                                @endif
                            </span>
                        </div>
                        <x-icon name="chevron-right" :size="16" />
                    </a>
                @empty
                    <div class="empty-inline">
                        <span class="empty-icon"><x-icon name="check-circle" :size="22" /></span>
                        <div><strong>No open repairs</strong><span>Everything is working as it should. Report an issue any time.</span></div>
                    </div>
                @endforelse
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Rent</span>
                        <h3>Recent payments</h3>
                    </div>
                    <a href="{{ route('tenant.payments.index') }}" class="text-link">Full ledger <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th class="align-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPayments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->period_label }}</strong><span class="table-sub">{{ $payment->reference }}</span></td>
                                    <td>{{ money($payment->amount) }}</td>
                                    <td>{{ $payment->paid_at?->format('j M Y') }}</td>
                                    <td class="align-right"><a href="{{ route('tenant.payments.receipt', $payment) }}" class="table-link">View <x-icon name="arrow-up-right" :size="13" /></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Library</span>
                        <h3>Recent documents</h3>
                    </div>
                    <a href="{{ route('tenant.documents.index') }}" class="text-link">All files <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="document-list">
                    @foreach ($documents as $document)
                        <a href="{{ route('tenant.documents.download', $document) }}" class="document-row">
                            <span class="file-thumb"><x-icon name="file" :size="17" /></span>
                            <div>
                                <strong>{{ $document->title }}</strong>
                                <span>{{ $document->category_label }} · PDF · {{ $document->human_size }}</span>
                            </div>
                            <x-icon name="download" :size="16" />
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Noticeboard</span>
                        <h3>Latest announcements</h3>
                    </div>
                    <a href="{{ route('tenant.announcements.index') }}" class="text-link">All <x-icon name="arrow-right" :size="14" /></a>
                </div>

                <div class="announcement-list">
                    @foreach ($announcements as $announcement)
                        <a href="{{ route('tenant.announcements.show', $announcement) }}" class="announcement-row">
                            <span class="announcement-date"><strong>{{ $announcement->published_at->format('d') }}</strong>{{ $announcement->published_at->format('M') }}</span>
                            <div>
                                <strong>{{ $announcement->title }}</strong>
                                <span>{{ Str::limit(strip_tags($announcement->body), 74) }}</span>
                            </div>
                            @if ($announcement->pinned)
                                <span class="pin-dot" title="Pinned"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="panel resident-help">
                <span class="support-icon"><x-icon name="lifebuoy" :size="20" /></span>
                <h3>Something on your mind?</h3>
                <p>Your property manager, Sofia, is here Monday to Friday, 9am–5pm.</p>
                <div class="resident-help-actions">
                    <a href="{{ route('tenant.requests.create', ['type' => 'enquiry']) }}" class="button button-dark button-sm">Send a message</a>
                    <a href="tel:02079460120" class="text-link">020 7946 0120</a>
                </div>
            </section>
        </div>
    </div>
@endsection
