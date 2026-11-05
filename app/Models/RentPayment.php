<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id',
        'reference',
        'period_start',
        'period_end',
        'amount',
        'due_date',
        'paid_at',
        'method',
        'status',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->format('F Y');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'overdue'
            || ($this->status === 'pending' && $this->due_date->isPast());
    }
}
