@props(['name', 'size' => 20, 'stroke' => 1.8])

<svg {{ $attributes->merge(['class' => 'icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('home')
            <path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9.5 21v-6h5v6"/>
            @break
        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
            @break
        @case('building')
            <path d="M4 21V5.5A1.5 1.5 0 0 1 5.5 4h7A1.5 1.5 0 0 1 14 5.5V21"/><path d="M14 10h4.5A1.5 1.5 0 0 1 20 11.5V21"/><path d="M2 21h20"/><path d="M7 8h4M7 12h4M7 16h4M17 14h0M17 18h0"/>
            @break
        @case('key')
            <circle cx="8" cy="8" r="4"/><path d="m11 11 9 9"/><path d="m16 16 2-2M19 19l2-2"/>
            @break
        @case('card')
            <rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 9.5h19"/><path d="M6 15h4"/>
            @break
        @case('wrench')
            <path d="M14.5 6.5a4.5 4.5 0 0 0-5.9 5.9L3 18l3 3 5.6-5.6a4.5 4.5 0 0 0 5.9-5.9l-2.6 2.6-3-3 2.6-2.6Z"/>
            @break
        @case('folder')
            <path d="M3 7.5A1.5 1.5 0 0 1 4.5 6h5l2 2h8A1.5 1.5 0 0 1 21 9.5v8A1.5 1.5 0 0 1 19.5 19h-15A1.5 1.5 0 0 1 3 17.5z"/>
            @break
        @case('message')
            <path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-3.7-.9L3 21l1.9-5.8a8.5 8.5 0 1 1 16.1-3.7Z"/>
            @break
        @case('bell')
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.2a3.5 3.5 0 0 1 0 6.6"/><path d="M18 14.4A6.5 6.5 0 0 1 21.5 20"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6v-.2h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>
            @break
        @case('logout')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>
            @break
        @case('arrow-right')
            <path d="M4 12h16"/><path d="m14 6 6 6-6 6"/>
            @break
        @case('arrow-up-right')
            <path d="M7 17 17 7"/><path d="M8 7h9v9"/>
            @break
        @case('chevron-right')
            <path d="m9 18 6-6-6-6"/>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>
            @break
        @case('download')
            <path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>
            @break
        @case('check')
            <path d="m5 12 4 4L19 6"/>
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            @break
        @case('alert')
            <path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/>
            @break
        @case('shield')
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('pound')
            <path d="M18 7.5A5.5 5.5 0 0 0 7.5 11c0 4.5-1.5 6.5-3 8.5h13"/><path d="M7 14h8"/>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"/>
            @break
        @case('x')
            <path d="m6 6 12 12M18 6 6 18"/>
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>
            @break
        @case('phone')
            <path d="M21 16.9v2.6a2 2 0 0 1-2.2 2 19.3 19.3 0 0 1-8.4-3 19 19 0 0 1-5.9-5.9 19.3 19.3 0 0 1-3-8.5A2 2 0 0 1 3.5 2H6a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L7.1 9.9a15.5 15.5 0 0 0 6 6l1.3-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 1.9Z"/>
            @break
        @case('pin')
            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>
            @break
        @case('eye')
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
            @break
        @case('paperclip')
            <path d="m21 11.5-8.8 8.8a5.5 5.5 0 0 1-7.8-7.8l9-9a3.8 3.8 0 0 1 5.4 5.4l-9 9a2 2 0 0 1-2.8-2.9l8.2-8.2"/>
            @break
        @case('send')
            <path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/>
            @break
        @case('briefcase')
            <rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/>
            @break
        @case('clipboard')
            <rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="M9 10h6M9 14h6M9 18h4"/>
            @break
        @case('tool')
            <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2 2-2.8-2.8 2-2Z"/>
            @break
        @case('file')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/>
            @break
        @case('sparkle')
            <path d="m12 3 1.7 5.3L19 10l-5.3 1.7L12 17l-1.7-5.3L5 10l5.3-1.7L12 3Z"/><path d="m19 16 .7 2.3L22 19l-2.3.7L19 22l-.7-2.3L16 19l2.3-.7L19 16Z"/>
            @break
        @case('refresh')
            <path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M3 21v-5h5M21 3v5h-5"/>
            @break
        @case('star')
            <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>
            @break
        @case('filter')
            <path d="M3 5h18l-7 8v6l-4 2v-8L3 5Z"/>
            @break
        @case('trash')
            <path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v5M14 11v5"/>
            @break
        @case('edit')
            <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
            @break
        @case('lifebuoy')
            <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.5"/><path d="m5.6 5.6 3.8 3.8M14.6 14.6l3.8 3.8M18.4 5.6l-3.8 3.8M9.4 14.6l-3.8 3.8"/>
            @break
        @case('lock')
            <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
            @break
        @case('chart')
            <path d="M3 3v18h18"/><path d="m7 15 4-4 4 3 5-6"/>
            @break
        @case('arrow-left')
            <path d="M20 12H4"/><path d="m10 18-6-6 6-6"/>
            @break
        @case('inbox')
            <path d="M4 4h16v16H4z"/><path d="M4 14h5l2 3h2l2-3h5"/>
            @break
        @default
            <circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/>
    @endswitch
</svg>
