@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <div class="auth-heading">
        <span class="eyebrow">Resident portal</span>
        <h1>Welcome back.</h1>
        <p>Sign in to see your home, rent and anything that needs your attention.</p>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <span class="flash-icon"><x-icon name="alert" :size="18" /></span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="form-stack">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="you@example.com">
            @error('email')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="field">
            <div class="field-label-row">
                <label for="password">Password</label>
                <a href="{{ route('password.request') }}" class="field-link">Forgot password?</a>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Your password">
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <label class="checkbox-row">
            <input type="checkbox" name="remember" value="1">
            <span>Keep me signed in on this device</span>
        </label>

        <button type="submit" class="button button-dark button-block">Sign in <x-icon name="arrow-right" :size="17" /></button>
    </form>

    <p class="auth-switch">New to PropNest? <a href="{{ route('register') }}">Create a resident account</a></p>

    <div class="demo-login">
        <div class="demo-login-head">
            <span>Demo accounts</span>
            <span class="demo-password">password</span>
        </div>
        <div class="demo-login-grid">
            <button type="button" class="demo-login-button" data-demo-email="alex.morgan@propnest.test" data-demo-password="password">
                <span class="avatar avatar-xs">AM</span>
                <span><strong>Tenant</strong><small>Alex Morgan</small></span>
            </button>
            <button type="button" class="demo-login-button" data-demo-email="manager@propnest.test" data-demo-password="password">
                <span class="avatar avatar-xs avatar-2">SB</span>
                <span><strong>Manager</strong><small>Sofia Bennett</small></span>
            </button>
            <button type="button" class="demo-login-button" data-demo-email="landlord@propnest.test" data-demo-password="password">
                <span class="avatar avatar-xs avatar-3">EC</span>
                <span><strong>Landlord</strong><small>Edward Clarke</small></span>
            </button>
            <button type="button" class="demo-login-button" data-demo-email="contractor@propnest.test" data-demo-password="password">
                <span class="avatar avatar-xs avatar-4">DW</span>
                <span><strong>Contractor</strong><small>Danny Whitmore</small></span>
            </button>
        </div>
    </div>
@endsection
