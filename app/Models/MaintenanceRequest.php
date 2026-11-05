<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'property_id',
        'tenancy_id',
        'submitted_by',
        'assigned_to',
        'reference',
        'title',
        'category',
        'description',
        'priority',
        'status',
        'scheduled_for',
        'completed_at',
        'cost',
        'tenant_feedback',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'completed_at' => 'datetime',
            'cost' => 'decimal:2',
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

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MaintenanceUpdate::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return config('portal.maintenance.statuses.'.$this->status, ucfirst($this->status));
    }

    public function getPriorityLabelAttribute(): string
    {
        return str($this->priority)->headline()->toString();
    }

    public function getIsOpenAttribute(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled'], true);
    }
}
