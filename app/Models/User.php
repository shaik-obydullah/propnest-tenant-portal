<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'job_title',
        'status',
        'last_login_at',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'last_login_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class, 'tenant_id');
    }

    public function activeTenancy(): HasOne
    {
        return $this->hasOne(Tenancy::class, 'tenant_id')
            ->where('status', 'active')
            ->latestOfMany('end_date');
    }

    public function ownedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function managedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'submitted_by');
    }

    public function assignedMaintenance(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function portalNotifications(): HasMany
    {
        return $this->hasMany(PortalNotification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->portalNotifications()->whereNull('read_at');
    }

    public function getFirstNameAttribute(): string
    {
        return str($this->name)->before(' ')->toString();
    }

    public function getInitialsAttribute(): string
    {
        return str($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => str($part)->substr(0, 1)->upper())
            ->implode('');
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $values = array_map(
            fn (UserRole|string $role) => $role instanceof UserRole ? $role->value : $role,
            $roles
        );

        return in_array($this->role->value, $values, true);
    }
}
