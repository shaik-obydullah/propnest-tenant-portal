<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'property_id',
        'unit_id',
        'tenant_id',
        'reference',
        'start_date',
        'end_date',
        'rent_amount',
        'payment_frequency',
        'deposit_amount',
        'deposit_scheme',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'rent_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function rentPayments(): HasMany
    {
        return $this->hasMany(RentPayment::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function outstandingBalance(): float
    {
        return (float) $this->rentPayments()
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('amount');
    }

    public function nextPayment(): ?RentPayment
    {
        return $this->rentPayments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->first();
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->startOfDay()->diffInDays($this->end_date, false));
    }

    public function getIsEndingSoonAttribute(): bool
    {
        return $this->status === 'active' && $this->days_remaining <= 90;
    }

    public function getProgressPercentAttribute(): int
    {
        $total = max(1, $this->start_date->diffInDays($this->end_date));
        $elapsed = $this->start_date->diffInDays(now());

        return (int) max(0, min(100, round(($elapsed / $total) * 100)));
    }
}
