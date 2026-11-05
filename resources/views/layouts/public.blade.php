<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PropNest') · Property management, considered</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="public-body">
    <header class="public-header" data-header>
        <div class="container public-nav">
            <x-logo />
            <nav class="public-links" aria-label="Primary navigation">
                <a href="#features">Features</a>
                <a href="#how">How it works</a>
                <a href="#roles">For every role</a>
            </nav>
            <div class="public-actions">
                @auth
                    <a href="{{ auth()->user()->role->dashboardPath() }}" class="button button-dark button-sm">Go to dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-link">Sign in</a>
                    <a href="{{ route('register') }}" class="button button-dark button-sm">Create account</a>
                @endauth
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="public-footer">
        <div class="container public-footer-grid">
            <div class="public-footer-brand">
                <x-logo />
                <p>The considered operating system for property agencies, landlords and the people who call their buildings home.</p>
            </div>
            <div>
                <span class="footer-heading">Product</span>
                <a href="#features">Features</a>
                <a href="#how">How it works</a>
                <a href="#roles">Portals</a>
            </div>
            <div>
                <span class="footer-heading">Company</span>
                <a href="#">About</a>
                <a href="#">Security</a>
                <a href="#">Contact</a>
            </div>
            <div>
                <span class="footer-heading">Demo</span>
                <a href="{{ route('login') }}">Tenant sign in</a>
                <a href="{{ route('login') }}">Manager sign in</a>
                <a href="mailto:hello@propnest.test">hello@propnest.test</a>
            </div>
        </div>
        <div class="container public-footer-bottom">
            <span>© {{ now()->year }} PropNest. A demonstration property platform.</span>
            <span>Built with Laravel, MySQL & a little care.</span>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
