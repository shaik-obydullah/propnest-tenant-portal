@extends('layouts.guest')

@section('title', 'Choose a new password')

@section('content')
    <div class="auth-heading">
        <span class="eyebrow">Almost there</span>
        <h1>Choose a new password.</h1>
        <p>Pick something memorable and secure. You’ll use it the next time you sign in.</p>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <span class="flash-icon"><x-icon name="alert" :size="18" /></span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="form-stack">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required>
        </div>

        <div class="field">
            <label for="password">New password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>

        <button type="submit" class="button button-dark button-block">Reset password <x-icon name="arrow-right" :size="17" /></button>
    </form>
@endsection
