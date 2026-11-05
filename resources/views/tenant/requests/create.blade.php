@extends('layouts.app')

@section('title', 'Make a request')
@section('eyebrow', 'Tenant services')

@section('content')
    <div class="page-heading">
        <div>
            <a href="{{ route('tenant.requests.index') }}" class="back-link"><x-icon name="arrow-left" :size="15" /> Back to requests</a>
            <h2>What can we help with?</h2>
            <p>Choose the closest option, add the detail and your property team will take it from there.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.requests.store') }}" class="form-layout">
        @csrf

        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Step 1</span>
                        <h3>Choose a request type</h3>
                    </div>
                </div>

                <div class="request-type-grid">
                    @foreach ($types as $value => $label)
                        <label class="request-type-option">
                            <input type="radio" name="type" value="{{ $value }}" @checked(old('type', $selectedType ?: 'enquiry') === $value)>
                            <span class="request-type-icon"><x-icon name="{{ match ($value) { 'renewal' => 'refresh', 'notice' => 'logout', 'details' => 'user', 'occupant' => 'users', 'pet' => 'star', 'concern' => 'alert', default => 'message' } }}" :size="18" /></span>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('type')<span class="field-error">{{ $message }}</span>@enderror
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Step 2</span>
                        <h3>Tell us a little more</h3>
                    </div>
                </div>

                <div class="field">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" maxlength="140" required placeholder="A short summary of your request">
                    @error('subject')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="field">
                    <label for="details">Details</label>
                    <textarea id="details" name="details" rows="7" required placeholder="Give us the context we need to help — dates, names, anything relevant.">{{ old('details') }}</textarea>
                    @error('details')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </section>
        </div>

        <aside class="stack">
            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="clock" :size="20" /></span>
                <h3>What to expect</h3>
                <ul class="check-list">
                    <li><x-icon name="check" :size="15" /> A reference as soon as you submit</li>
                    <li><x-icon name="check" :size="15" /> Acknowledgement within one working day</li>
                    <li><x-icon name="check" :size="15" /> A response or next step from your manager</li>
                    <li><x-icon name="check" :size="15" /> The full conversation kept in your portal</li>
                </ul>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Good to know</span>
                        <h3>Urgent issues</h3>
                    </div>
                </div>
                <p class="body-copy">Requests are reviewed during office hours. For an emergency repair, please call <a href="tel:08000123456">0800 012 3456</a> rather than using this form.</p>
            </section>

            <button type="submit" class="button button-dark button-block">Submit request <x-icon name="arrow-right" :size="16" /></button>
            <p class="form-note">You can add more information at any time from the request page.</p>
        </aside>
    </form>
@endsection
