<?php

namespace App\Support;

use App\Models\MaintenanceRequest;
use App\Models\ServiceRequest;

class Reference
{
    public static function maintenance(): string
    {
        return sprintf(
            'MR-%s-%04d',
            now()->format('y'),
            (int) MaintenanceRequest::max('id') + 1
        );
    }

    public static function service(): string
    {
        return sprintf(
            'SR-%s-%04d',
            now()->format('y'),
            (int) ServiceRequest::max('id') + 1
        );
    }

    public static function receipt(): string
    {
        return sprintf('RCPT-%s-%05d', now()->format('ym'), random_int(10000, 99999));
    }
}
