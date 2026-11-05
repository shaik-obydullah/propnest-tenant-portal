<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'unit_number',
        'floor',
        'bedrooms',
        'bathrooms',
        'rent_amount',
        'deposit_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rent_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class);
    }
}
