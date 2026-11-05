@extends('layouts.guest')

@section('title', 'Reset password')

@section('content')
    <div class="auth-heading">
        <span class="eyebrow">Password help</span>
        <h1>Let’s get you back in.</h1>
        <p>Enter the email linked to your tenancy and we’ll send a secure reset link.</p>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <span class="flash-icon"><x-icon name="alert" :size="18" /></span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="form-stack">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="you@example.com">
        </div>

        <button type="submit" class="button button-dark button-block">Send reset link <x-icon name="send" :size="16" /></button>
    </form>

    <p class="auth-switch"><a href="{{ route('login') }}">← Back to sign in</a></p>

    <p class="auth-note">In this demo the email is written to <code>storage/logs/laravel.log</code> rather than sent.</p>
@endsection
