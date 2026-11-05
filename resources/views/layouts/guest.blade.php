@php($user = auth()->user())

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') · PropNest</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="guest-body">
    <div class="guest-shell">
        <aside class="guest-visual">
            <div class="guest-visual-top">
                <x-logo tone="light" />
                <span class="guest-visual-tag">Tenant portal</span>
            </div>

            <div class="guest-visual-copy">
                <span class="eyebrow eyebrow-light">A calmer way to rent</span>
                <h2>Your home,<br>all in one place.</h2>
                <p>Rent, repairs, documents and conversations — gathered into one considered space for residents.</p>
            </div>

            <div class="guest-skyline" aria-hidden="true">
                <svg viewBox="0 0 720 360" fill="none" preserveAspectRatio="xMidYMax slice">
                    <circle cx="565" cy="84" r="46" fill="#E9B66A" fill-opacity=".55"/>
                    <path d="M0 310h720" stroke="#F7F2E6" stroke-opacity=".2" stroke-width="2"/>
                    <path d="M35 310V197l56-34 56 34v113" fill="#123A31"/>
                    <path d="M47 207h26v18H47zM79 207h26v18H79zM47 238h26v18H47zM79 238h26v18H79zM47 269h26v18H47zM79 269h26v18H79z" fill="#E9B66A" fill-opacity=".35"/>
                    <path d="M164 310V151l72-41 72 41v159" fill="#16443A"/>
                    <path d="M181 170h25v24h-25zM224 170h25v24h-25zM267 170h25v24h-25zM181 210h25v24h-25zM224 210h25v24h-25zM267 210h25v24h-25zM181 250h25v24h-25zM224 250h25v24h-25zM267 250h25v24h-25z" fill="#F7F2E6" fill-opacity=".18"/>
                    <path d="M325 310V230l52-31 52 31v80" fill="#1B5044"/>
                    <path d="M342 245h20v20h-20zM378 245h20v20h-20zM342 276h20v20h-20zM378 276h20v20h-20z" fill="#E9B66A" fill-opacity=".4"/>
                    <path d="M446 310V181l67-38 67 38v129" fill="#153F36"/>
                    <path d="M463 199h22v22h-22zM503 199h22v22h-22zM543 199h22v22h-22zM463 234h22v22h-22zM503 234h22v22h-22zM543 234h22v22h-22zM463 269h22v22h-22zM503 269h22v22h-22zM543 269h22v22h-22z" fill="#F7F2E6" fill-opacity=".16"/>
                    <path d="M596 310V256l43-26 43 26v54" fill="#1D584A"/>
                    <path d="M612 267h18v18h-18zM645 267h18v18h-18z" fill="#E9B66A" fill-opacity=".4"/>
                    <path d="M0 310h720v50H0z" fill="#0F2F28"/>
                </svg>
            </div>

            <blockquote class="guest-quote">
                <x-icon name="sparkle" :size="18" />
                <p>“Everything I need to look after my home, without the chasing.”</p>
                <footer><span class="avatar avatar-xs">AM</span> Alex M. · Willow Court</footer>
            </blockquote>
        </aside>

        <main class="guest-main">
            <div class="guest-mobile-brand">
                <x-logo tone="dark" />
            </div>

            <div class="guest-form-wrap">
                @if (session('status'))
                    <div class="flash flash-success">
                        <span class="flash-icon"><x-icon name="check-circle" :size="18" /></span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="guest-footer">
                <span>© {{ now()->year }} PropNest</span>
                <span>
                    <a href="#">Privacy</a>
                    <a href="#">Support</a>
                </span>
            </footer>
        </main>
    </div>
</body>
</html>
