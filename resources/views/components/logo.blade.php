@props(['compact' => false, 'tone' => 'dark'])

<a {{ $attributes->merge(['class' => 'brand brand-'.$tone]) }} href="{{ route('home') }}" aria-label="PropNest home">
    <span class="brand-mark" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
            <path d="M4 12.2 13 4l9 8.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5 11.3V21h13v-9.7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.5 21v-5.2a2.5 2.5 0 0 1 5 0V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
    @unless($compact)
        <span class="brand-word">Prop<span>Nest</span></span>
    @endunless
</a>
