<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'landlord_id',
        'manager_id',
        'name',
        'reference',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'postcode',
        'description',
        'bedrooms',
        'bathrooms',
        'size_sqft',
        'status',
        'accent',
    ];

    protected function casts(): array
    {
        return [
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'size_sqft' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function getAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->postcode,
        ])->filter()->implode(', ');
    }

    public function getOccupiedUnitsCountAttribute(): int
    {
        return $this->units->where('status', 'occupied')->count();
    }
}
