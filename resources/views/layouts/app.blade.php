@php
    $user = auth()->user();
    $role = $user->role;
    $unreadCount = $user->unreadNotifications()->count();

    $navigation = match ($role) {
        \App\Enums\UserRole::Tenant => [
            ['label' => 'Overview', 'route' => 'tenant.dashboard', 'icon' => 'grid', 'active' => 'tenant.dashboard'],
            ['label' => 'My tenancy', 'route' => 'tenant.tenancy.show', 'icon' => 'key', 'active' => 'tenant.tenancy.*'],
            ['label' => 'Rent & payments', 'route' => 'tenant.payments.index', 'icon' => 'card', 'active' => 'tenant.payments.*'],
            ['label' => 'Maintenance', 'route' => 'tenant.maintenance.index', 'icon' => 'wrench', 'active' => 'tenant.maintenance.*'],
            ['label' => 'Documents', 'route' => 'tenant.documents.index', 'icon' => 'folder', 'active' => 'tenant.documents.*'],
            ['label' => 'Requests', 'route' => 'tenant.requests.index', 'icon' => 'message', 'active' => 'tenant.requests.*'],
            ['label' => 'Announcements', 'route' => 'tenant.announcements.index', 'icon' => 'bell', 'active' => 'tenant.announcements.*'],
        ],
        \App\Enums\UserRole::Landlord => [
            ['label' => 'Portfolio', 'route' => 'landlord.dashboard', 'icon' => 'building', 'active' => 'landlord.*'],
        ],
        \App\Enums\UserRole::Contractor => [
            ['label' => 'My jobs', 'route' => 'contractor.dashboard', 'icon' => 'tool', 'active' => 'contractor.*'],
        ],
        default => [
            ['label' => 'Overview', 'route' => 'admin.dashboard', 'icon' => 'grid', 'active' => 'admin.dashboard'],
            ['label' => 'Properties', 'route' => 'admin.properties.index', 'icon' => 'building', 'active' => 'admin.properties.*'],
            ['label' => 'Tenancies', 'route' => 'admin.tenancies.index', 'icon' => 'key', 'active' => 'admin.tenancies.*'],
            ['label' => 'Maintenance', 'route' => 'admin.maintenance.index', 'icon' => 'wrench', 'active' => 'admin.maintenance.*'],
            ['label' => 'Tenants', 'route' => 'admin.tenants.index', 'icon' => 'users', 'active' => 'admin.tenants.*'],
            ['label' => 'Announcements', 'route' => 'admin.announcements.index', 'icon' => 'bell', 'active' => 'admin.announcements.*'],
        ],
    };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overview') · PropNest</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-top">
                <x-logo tone="light" />
                <button class="sidebar-close" type="button" data-sidebar-close aria-label="Close menu">
                    <x-icon name="x" :size="20" />
                </button>
            </div>

            <div class="org-chip">
                <span class="org-monogram">{{ str($user->organization?->name ?? 'PropNest')->explode(' ')->take(2)->map(fn ($word) => str($word)->substr(0, 1))->implode('') }}</span>
                <div>
                    <span class="org-label">Workspace</span>
                    <strong>{{ $user->organization?->name ?? 'PropNest' }}</strong>
                </div>
            </div>

            <nav class="side-nav" aria-label="Main navigation">
                <span class="side-nav-label">Menu</span>
                @foreach ($navigation as $item)
                    <a href="{{ route($item['route']) }}" class="side-link {{ request()->routeIs($item['active']) ? 'is-active' : '' }}">
                        <x-icon :name="$item['icon']" :size="19" />
                        <span>{{ $item['label'] }}</span>
                        @if ($item['label'] === 'Announcements' && $unreadCount > 0)
                            <span class="side-count">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="sidebar-footer">
                <div class="support-card">
                    <span class="support-icon"><x-icon name="lifebuoy" :size="18" /></span>
                    <div>
                        <strong>Need a hand?</strong>
                        <p>Your property team is here Mon–Fri, 9–5.</p>
                        <a href="mailto:hello@harbourvine.test">Contact support <x-icon name="arrow-up-right" :size="13" /></a>
                    </div>
                </div>

                <div class="user-chip">
                    <span class="avatar avatar-sm">{{ $user->initials }}</span>
                    <div class="user-chip-copy">
                        <strong>{{ $user->name }}</strong>
                        <span>{{ $role->shortLabel() }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-btn icon-btn-quiet" title="Sign out" aria-label="Sign out">
                            <x-icon name="logout" :size="18" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="sidebar-scrim" data-sidebar-close></div>

        <main class="main">
            <header class="topbar">
                <div class="topbar-leading">
                    <button class="mobile-menu" type="button" data-sidebar-open aria-label="Open menu">
                        <x-icon name="menu" :size="22" />
                    </button>
                    <div>
                        <span class="eyebrow">@yield('eyebrow', $role->isStaff() ? 'Agency workspace' : 'Resident portal')</span>
                        <h1>@yield('title', 'Overview')</h1>
                    </div>
                </div>

                <div class="topbar-actions">
                    @unless ($role->isStaff() || $role->value === 'landlord' || $role->value === 'contractor')
                        <a href="{{ route('tenant.maintenance.create') }}" class="button button-ghost button-sm hide-mobile">
                            <x-icon name="plus" :size="16" /> Report an issue
                        </a>
                    @endunless
                    <a href="{{ route('notifications.index') }}" class="icon-btn notification-button" aria-label="Notifications">
                        <x-icon name="bell" :size="20" />
                        @if ($unreadCount > 0)
                            <span class="notification-dot">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" class="avatar avatar-topbar" title="Your profile">{{ $user->initials }}</a>
                </div>
            </header>

            <div class="page">
                @if (session('status'))
                    <div class="flash flash-success" data-flash>
                        <span class="flash-icon"><x-icon name="check-circle" :size="18" /></span>
                        <span>{{ session('status') }}</span>
                        <button type="button" class="flash-close" data-flash-close aria-label="Dismiss">
                            <x-icon name="x" :size="15" />
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="flash flash-error" data-flash>
                        <span class="flash-icon"><x-icon name="alert" :size="18" /></span>
                        <span>{{ $errors->first() }}</span>
                        <button type="button" class="flash-close" data-flash-close aria-label="Dismiss">
                            <x-icon name="x" :size="15" />
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="app-footer">
                <span>© {{ now()->year }} PropNest · Harbour & Vine Property Group</span>
                <span class="app-footer-links">
                    <a href="#">Privacy</a>
                    <a href="#">Tenant handbook</a>
                    <span>v1.0</span>
                </span>
            </footer>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
