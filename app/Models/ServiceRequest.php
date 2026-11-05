<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id',
        'user_id',
        'reference',
        'type',
        'subject',
        'details',
        'status',
        'response',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return config('portal.service_requests.'.$this->type, str($this->type)->headline()->toString());
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'in_review' => 'In review',
            'action_required' => 'Action required',
            'approved' => 'Approved',
            'declined' => 'Declined',
            'completed' => 'Completed',
            default => str($this->status)->headline()->toString(),
        };
    }
}
