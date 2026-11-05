<?php

return [
    'name' => 'PropNest',
    'currency' => 'GBP',
    'currency_symbol' => '£',

    'demo' => [
        'tenant' => ['email' => 'alex.morgan@propnest.test', 'password' => 'password'],
        'manager' => ['email' => 'manager@propnest.test', 'password' => 'password'],
        'landlord' => ['email' => 'landlord@propnest.test', 'password' => 'password'],
        'contractor' => ['email' => 'contractor@propnest.test', 'password' => 'password'],
    ],

    'maintenance' => [
        'categories' => [
            'Plumbing' => 'Plumbing & water',
            'Electrical' => 'Electrical',
            'Heating' => 'Heating & boiler',
            'Appliance' => 'Appliance',
            'Structure' => 'Walls, roof & structure',
            'Damp' => 'Damp & mould',
            'Security' => 'Locks & security',
            'Garden' => 'Garden & outside',
            'Other' => 'Something else',
        ],
        'priorities' => [
            'low' => 'Low — convenient to fix',
            'normal' => 'Normal — needs attention',
            'high' => 'High — affecting daily life',
            'urgent' => 'Urgent — safety risk',
        ],
        'statuses' => [
            'submitted' => 'Submitted',
            'triaged' => 'Triaged',
            'scheduled' => 'Visit scheduled',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'service_requests' => [
        'renewal' => 'Tenancy renewal',
        'notice' => 'Notice to vacate',
        'details' => 'Change my details',
        'occupant' => 'Additional occupant',
        'pet' => 'Pet permission',
        'concern' => 'Report a concern',
        'enquiry' => 'General enquiry',
    ],

    'document_categories' => [
        'tenancy' => 'Tenancy',
        'compliance' => 'Compliance',
        'financial' => 'Financial',
        'property' => 'Property',
        'other' => 'Other',
    ],
];
