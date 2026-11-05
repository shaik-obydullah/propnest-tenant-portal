<?php

namespace App\Enums;

enum UserRole: string
{
    case PlatformAdmin = 'platform_admin';
    case AgencyAdmin = 'agency_admin';
    case PropertyManager = 'property_manager';
    case Landlord = 'landlord';
    case Tenant = 'tenant';
    case Contractor = 'contractor';

    public function label(): string
    {
        return match ($this) {
            self::PlatformAdmin => 'Platform administrator',
            self::AgencyAdmin => 'Agency administrator',
            self::PropertyManager => 'Property manager',
            self::Landlord => 'Landlord',
            self::Tenant => 'Tenant',
            self::Contractor => 'Maintenance contractor',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::PlatformAdmin => 'Admin',
            self::AgencyAdmin => 'Agency admin',
            self::PropertyManager => 'Manager',
            self::Landlord => 'Landlord',
            self::Tenant => 'Tenant',
            self::Contractor => 'Contractor',
        };
    }

    public function isStaff(): bool
    {
        return in_array($this, [self::PlatformAdmin, self::AgencyAdmin, self::PropertyManager], true);
    }

    public function dashboardPath(): string
    {
        return match ($this) {
            self::PlatformAdmin, self::AgencyAdmin, self::PropertyManager => '/admin',
            self::Landlord => '/landlord',
            self::Contractor => '/contractor',
            self::Tenant => '/dashboard',
        };
    }
}
