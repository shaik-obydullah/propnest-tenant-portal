@extends('layouts.app')

@section('title', 'Your profile')
@section('eyebrow', 'Account settings')

@section('content')
    <div class="page-heading">
        <div>
            <h2>Your details, kept current.</h2>
            <p>Update how your property team can reach you and keep your account secure.</p>
        </div>
        <x-badge status="green" label="Active account" />
    </div>

    <div class="content-grid content-grid-wide">
        <div class="stack">
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Contact</span>
                        <h3>Personal information</h3>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="form-stack">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-grid-2">
                        <div class="field">
                            <label for="email">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label for="phone">Phone number</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="07700 900123">
                            @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button button-dark button-sm">Save changes</button>
                    </div>
                </form>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Security</span>
                        <h3>Change password</h3>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="form-stack">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                        @error('current_password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-grid-2">
                        <div class="field">
                            <label for="password">New password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required>
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirm new password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button button-dark button-sm">Update password</button>
                    </div>
                </form>
            </section>
        </div>

        <aside class="stack">
            <section class="panel profile-card">
                <span class="avatar avatar-xl">{{ $user->initials }}</span>
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
                <x-badge :status="'green'" :label="$user->role->label()" />
                <div class="profile-facts">
                    <div><span>Workspace</span><strong>{{ $user->organization?->name ?? 'PropNest' }}</strong></div>
                    <div><span>Phone</span><strong>{{ $user->phone ?? 'Not set' }}</strong></div>
                    <div><span>Last sign in</span><strong>{{ $user->last_login_at?->diffForHumans() ?? 'This is your first visit' }}</strong></div>
                </div>
            </section>

            <section class="panel panel-tinted">
                <span class="support-icon"><x-icon name="shield" :size="20" /></span>
                <h3>Your data, your control</h3>
                <p>You can request an export of your personal data or ask for your account to be closed by contacting your property team.</p>
                <a href="mailto:hello@harbourvine.test" class="button button-ghost button-sm">Contact support</a>
            </section>
        </aside>
    </div>
@endsection
