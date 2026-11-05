@props(['status', 'label' => null])

@php
    $text = $label ?? str($status)->replace('_', ' ')->headline()->toString();
@endphp

<span {{ $attributes->merge(['class' => 'badge badge-'.status_tone($status)]) }}>
    <span class="badge-dot" aria-hidden="true"></span>{{ $text }}
</span>
