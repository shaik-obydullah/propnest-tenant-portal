<?php

if (! function_exists('money')) {
    function money(float|int|string|null $amount, bool $decimals = true): string
    {
        return config('portal.currency_symbol', '£').number_format(
            (float) $amount,
            $decimals ? 2 : 0
        );
    }
}

if (! function_exists('money_compact')) {
    function money_compact(float|int|string|null $amount): string
    {
        $value = (float) $amount;

        return config('portal.currency_symbol', '£').match (true) {
            abs($value) >= 1_000_000 => number_format($value / 1_000_000, 1).'m',
            abs($value) >= 1_000 => number_format($value / 1_000, 1).'k',
            default => number_format($value, 0),
        };
    }
}

if (! function_exists('status_tone')) {
    function status_tone(string $status): string
    {
        return match ($status) {
            'paid', 'completed', 'approved', 'active', 'occupied' => 'green',
            'pending', 'submitted', 'in_review', 'triaged', 'scheduled' => 'amber',
            'overdue', 'urgent', 'declined', 'cancelled', 'suspended' => 'red',
            'in_progress', 'action_required' => 'blue',
            default => 'neutral',
        };
    }
}
