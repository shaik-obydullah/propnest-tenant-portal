@extends('layouts.guest')

@section('title', 'Create account')

@section('content')
    <div class="auth-heading">
        <span class="eyebrow">Join PropNest</span>
        <h1>Make home admin feel lighter.</h1>
        <p>Create a resident account to keep your tenancy, rent and repairs in one place.</p>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <span class="flash-icon"><x-icon name="alert" :size="18" /></span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="form-stack">
        @csrf

        <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Alex Morgan">
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-grid-2">
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required placeholder="you@example.com">
                @error('email')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="field">
                <label for="phone">Phone <span class="label-optional">Optional</span></label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" autocomplete="tel" placeholder="07700 900123">
                @error('phone')<span class="field-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="At least 8 characters">
            <span class="field-hint">Use at least 8 characters, including a letter and a number.</span>
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Repeat your password">
        </div>

        <button type="submit" class="button button-dark button-block">Create my account <x-icon name="arrow-right" :size="17" /></button>
    </form>

    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>

    <p class="auth-note">Your account will be linked to your tenancy by your property team. If you have just moved in, allow a little time for the invitation to arrive.</p>
@endsection
