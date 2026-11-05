<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceUpdate;
use App\Models\Organization;
use App\Models\PortalNotification;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\ServiceRequest;
use App\Models\Tenancy;
use App\Models\Unit;
use App\Models\User;
use App\Support\SimplePdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::updateOrCreate(
            ['slug' => 'harbour-vine'],
            [
                'name' => 'Harbour & Vine Property Group',
                'email' => 'hello@harbourvine.test',
                'phone' => '020 7946 0182',
                'address' => '18 Cursitor Street, London EC4A 1LT',
                'plan' => 'growth',
                'status' => 'active',
            ]
        );

        $users = $this->seedUsers($organization);
        [$properties, $units, $tenancies] = $this->seedPortfolio($organization, $users);
        $tenancy = $tenancies['alex'];

        $this->seedPayments($tenancy);
        $this->seedDocuments($organization, $properties, $tenancy, $users);
        $this->seedAnnouncements($organization, $properties, $users);
        $this->seedMaintenance($organization, $tenancy, $users);
        $this->seedServiceRequests($tenancy);
        $this->seedNotifications($users);

        $this->command?->info('PropNest demo data is ready.');
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(Organization $organization): array
    {
        $definitions = [
            'admin' => [
                'name' => 'Imogen Hart',
                'email' => 'admin@propnest.test',
                'role' => UserRole::PlatformAdmin,
                'job_title' => 'Platform lead',
                'phone' => '020 7946 0101',
            ],
            'agency' => [
                'name' => 'Marcus Reid',
                'email' => 'agency@propnest.test',
                'role' => UserRole::AgencyAdmin,
                'job_title' => 'Agency director',
                'phone' => '020 7946 0110',
            ],
            'manager' => [
                'name' => 'Sofia Bennett',
                'email' => 'manager@propnest.test',
                'role' => UserRole::PropertyManager,
                'job_title' => 'Senior property manager',
                'phone' => '020 7946 0120',
            ],
            'landlord' => [
                'name' => 'Edward Clarke',
                'email' => 'landlord@propnest.test',
                'role' => UserRole::Landlord,
                'job_title' => 'Portfolio landlord',
                'phone' => '07700 900123',
            ],
            'tenant' => [
                'name' => 'Alex Morgan',
                'email' => 'alex.morgan@propnest.test',
                'role' => UserRole::Tenant,
                'job_title' => 'Product designer',
                'phone' => '07700 900456',
            ],
            'tenant_priya' => [
                'name' => 'Priya Shah',
                'email' => 'priya.shah@propnest.test',
                'role' => UserRole::Tenant,
                'job_title' => 'Doctor',
                'phone' => '07700 900457',
            ],
            'tenant_daniel' => [
                'name' => 'Daniel Okafor',
                'email' => 'daniel.okafor@propnest.test',
                'role' => UserRole::Tenant,
                'job_title' => 'Software engineer',
                'phone' => '07700 900458',
            ],
            'tenant_chloe' => [
                'name' => 'Chloe Bennett',
                'email' => 'chloe.bennett@propnest.test',
                'role' => UserRole::Tenant,
                'job_title' => 'Account director',
                'phone' => '07700 900459',
            ],
            'contractor' => [
                'name' => 'Danny Whitmore',
                'email' => 'contractor@propnest.test',
                'role' => UserRole::Contractor,
                'job_title' => 'Gas Safe engineer',
                'phone' => '07700 900789',
            ],
        ];

        $users = [];

        foreach ($definitions as $key => $definition) {
            $users[$key] = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'organization_id' => $organization->id,
                    'name' => $definition['name'],
                    'role' => $definition['role'],
                    'job_title' => $definition['job_title'],
                    'phone' => $definition['phone'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array{0: array<string, Property>, 1: array<string, Unit>, 2: array<string, Tenancy>}
     */
    private function seedPortfolio(Organization $organization, array $users): array
    {
        $propertyDefinitions = [
            'willow' => [
                'name' => '14 Willow Court',
                'reference' => 'PN-WILLOW-14',
                'type' => 'apartment',
                'address_line_1' => '14 Willow Court',
                'address_line_2' => 'Wharf Road',
                'city' => 'London',
                'postcode' => 'N1 7UX',
                'description' => 'A calm, light-filled apartment moments from the Regent’s Canal. Period proportions meet a considered modern finish, with a private balcony overlooking the communal gardens.',
                'bedrooms' => 2,
                'bathrooms' => 1,
                'size_sqft' => 780,
                'status' => 'occupied',
                'accent' => 'forest',
            ],
            'kingsway' => [
                'name' => '27 Kingsway Mews',
                'reference' => 'PN-KINGSWAY-27',
                'type' => 'house',
                'address_line_1' => '27 Kingsway Mews',
                'city' => 'London',
                'postcode' => 'WC2B 6UN',
                'description' => 'A three-storey mews house tucked behind the bustle of Covent Garden. Original brickwork, a walled courtyard and a generous top-floor study.',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'size_sqft' => 1240,
                'status' => 'occupied',
                'accent' => 'clay',
            ],
            'foundry' => [
                'name' => 'The Foundry',
                'reference' => 'PN-FOUNDRY-01',
                'type' => 'apartment',
                'address_line_1' => 'The Foundry',
                'address_line_2' => '42 Orsmond Street',
                'city' => 'London',
                'postcode' => 'SE1 6LN',
                'description' => 'A converted print works in the heart of Shoreditch. Exposed steel, full-height windows and a shared roof terrace with skyline views.',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'size_sqft' => 560,
                'status' => 'occupied',
                'accent' => 'ocean',
            ],
        ];

        $properties = [];
        foreach ($propertyDefinitions as $key => $definition) {
            $properties[$key] = Property::updateOrCreate(
                ['reference' => $definition['reference']],
                [
                    ...$definition,
                    'organization_id' => $organization->id,
                    'landlord_id' => $users['landlord']->id,
                    'manager_id' => $users['manager']->id,
                ]
            );
        }

        $unitDefinitions = [
            'willow_4b' => ['property' => 'willow', 'unit_number' => '4B', 'floor' => 'Fourth floor', 'bedrooms' => 2, 'bathrooms' => 1, 'rent_amount' => 1850, 'deposit_amount' => 2135, 'status' => 'occupied'],
            'willow_2a' => ['property' => 'willow', 'unit_number' => '2A', 'floor' => 'Second floor', 'bedrooms' => 1, 'bathrooms' => 1, 'rent_amount' => 1695, 'deposit_amount' => 1955, 'status' => 'occupied'],
            'kingsway_a' => ['property' => 'kingsway', 'unit_number' => 'A', 'floor' => 'Ground and upper', 'bedrooms' => 3, 'bathrooms' => 2, 'rent_amount' => 3200, 'deposit_amount' => 3690, 'status' => 'occupied'],
            'foundry_12' => ['property' => 'foundry', 'unit_number' => '12', 'floor' => 'Third floor', 'bedrooms' => 1, 'bathrooms' => 1, 'rent_amount' => 1650, 'deposit_amount' => 1905, 'status' => 'occupied'],
        ];

        $units = [];
        foreach ($unitDefinitions as $key => $definition) {
            $property = $properties[$definition['property']];
            unset($definition['property']);

            $units[$key] = Unit::updateOrCreate(
                ['property_id' => $property->id, 'unit_number' => $definition['unit_number']],
                $definition
            );
        }

        $start = now()->subMonthsNoOverflow(8)->startOfMonth()->addDays(4);
        $end = $start->copy()->addMonthsNoOverflow(12)->subDay();

        $tenancyDefinitions = [
            'alex' => [
                'property' => 'willow',
                'unit' => 'willow_4b',
                'tenant' => 'tenant',
                'reference' => 'TEN-WILLOW-4B-26',
                'start_date' => $start,
                'end_date' => $end,
                'rent_amount' => 1850,
                'deposit_amount' => 2135,
                'deposit_scheme' => 'TDS — Tenancy Deposit Scheme',
                'notes' => 'Renewal conversation to begin three months before the tenancy end date.',
            ],
            'priya' => [
                'property' => 'kingsway',
                'unit' => 'kingsway_a',
                'tenant' => 'tenant_priya',
                'reference' => 'TEN-KINGSWAY-A-25',
                'start_date' => now()->subMonthsNoOverflow(14)->startOfMonth()->addDays(1),
                'end_date' => now()->addMonthsNoOverflow(10)->endOfMonth(),
                'rent_amount' => 3200,
                'deposit_amount' => 3690,
                'deposit_scheme' => 'DPS — Deposit Protection Service',
                'notes' => null,
            ],
            'daniel' => [
                'property' => 'foundry',
                'unit' => 'foundry_12',
                'tenant' => 'tenant_daniel',
                'reference' => 'TEN-FOUNDRY-12-26',
                'start_date' => now()->subMonthsNoOverflow(5)->startOfMonth()->addDays(12),
                'end_date' => now()->addMonthsNoOverflow(7)->endOfMonth(),
                'rent_amount' => 1650,
                'deposit_amount' => 1905,
                'deposit_scheme' => 'TDS — Tenancy Deposit Scheme',
                'notes' => null,
            ],
            'chloe' => [
                'property' => 'willow',
                'unit' => 'willow_2a',
                'tenant' => 'tenant_chloe',
                'reference' => 'TEN-WILLOW-2A-25',
                'start_date' => now()->subMonthsNoOverflow(16)->startOfMonth()->addDays(8),
                'end_date' => now()->addMonthsNoOverflow(8)->endOfMonth(),
                'rent_amount' => 1695,
                'deposit_amount' => 1955,
                'deposit_scheme' => 'DPS — Deposit Protection Service',
                'notes' => null,
            ],
        ];

        $tenancies = [];
        foreach ($tenancyDefinitions as $key => $definition) {
            $property = $properties[$definition['property']];
            $unit = $units[$definition['unit']];

            $tenancies[$key] = Tenancy::updateOrCreate(
                ['reference' => $definition['reference']],
                [
                    'organization_id' => $organization->id,
                    'property_id' => $property->id,
                    'unit_id' => $unit->id,
                    'tenant_id' => $users[$definition['tenant']]->id,
                    'start_date' => $definition['start_date'],
                    'end_date' => $definition['end_date'],
                    'rent_amount' => $definition['rent_amount'],
                    'payment_frequency' => 'monthly',
                    'deposit_amount' => $definition['deposit_amount'],
                    'deposit_scheme' => $definition['deposit_scheme'],
                    'status' => 'active',
                    'notes' => $definition['notes'],
                ]
            );
        }

        return [$properties, $units, $tenancies];
    }

    private function seedPayments(Tenancy $tenancy): void
    {
        $cursor = $tenancy->start_date->copy()->startOfMonth();
        $currentMonth = now()->startOfMonth();
        $index = 1;

        while ($cursor->lt($currentMonth)) {
            RentPayment::updateOrCreate(
                ['reference' => 'RP-'.$tenancy->reference.'-'.$cursor->format('Ym')],
                [
                    'tenancy_id' => $tenancy->id,
                    'period_start' => $cursor->copy()->startOfMonth(),
                    'period_end' => $cursor->copy()->endOfMonth(),
                    'amount' => $tenancy->rent_amount,
                    'due_date' => $cursor->copy()->startOfMonth(),
                    'paid_at' => $cursor->copy()->startOfMonth()->addHours(9),
                    'method' => 'Direct debit',
                    'status' => 'paid',
                    'receipt_number' => 'RCPT-'.$cursor->format('ym').'-'.str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                ]
            );

            $cursor->addMonthNoOverflow();
            $index++;
        }

        RentPayment::updateOrCreate(
            ['reference' => 'RP-'.$tenancy->reference.'-'.$currentMonth->format('Ym')],
            [
                'tenancy_id' => $tenancy->id,
                'period_start' => $currentMonth->copy()->startOfMonth(),
                'period_end' => $currentMonth->copy()->endOfMonth(),
                'amount' => $tenancy->rent_amount,
                'due_date' => $currentMonth->copy(),
                'paid_at' => null,
                'method' => null,
                'status' => 'overdue',
                'receipt_number' => null,
                'notes' => 'Awaiting payment. A reminder was emailed to the tenant.',
            ]
        );
    }

    /**
     * @param  array<string, Property>  $properties
     * @param  array<string, User>  $users
     */
    private function seedDocuments(Organization $organization, array $properties, Tenancy $tenancy, array $users): void
    {
        $documents = [
            [
                'title' => 'Assured Shorthold Tenancy Agreement',
                'category' => 'tenancy',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => $tenancy->id,
                'lines' => [
                    'This agreement is made between Harbour & Vine Property Group (the agent), Edward Clarke (the landlord) and Alex Morgan (the tenant).',
                    '',
                    'Property: 14 Willow Court, Wharf Road, London N1 7UX — Unit 4B.',
                    'Term: '.$tenancy->start_date->format('j F Y').' to '.$tenancy->end_date->format('j F Y').'.',
                    'Rent: £1,850 per calendar month, payable in advance on the first day of each month.',
                    'Deposit: £2,135 registered with the Tenancy Deposit Scheme.',
                    '',
                    'The tenant agrees to keep the property in good condition, report maintenance issues promptly and observe the building rules.',
                    'The landlord agrees to maintain the structure, exterior and installations in safe working order.',
                    '',
                    'Signed digitally by all parties. Reference: '.$tenancy->reference.'.',
                ],
            ],
            [
                'title' => 'Deposit Protection Certificate',
                'category' => 'tenancy',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => $tenancy->id,
                'lines' => [
                    'Tenancy Deposit Scheme — certificate of deposit registration.',
                    'Tenant: Alex Morgan. Landlord: Edward Clarke. Agent: Harbour & Vine Property Group.',
                    'Deposit amount: £2,135.00. Scheme: Tenancy Deposit Scheme (TDS).',
                    'Protected date: '.$tenancy->start_date->format('j F Y').'.',
                    '',
                    'The deposit is held in accordance with the Housing Act 2004. A copy of the scheme terms is available on request.',
                ],
            ],
            [
                'title' => 'Gas Safety Certificate (CP12)',
                'category' => 'compliance',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => null,
                'lines' => [
                    'Landlord gas safety record issued under the Gas Safety (Installation and Use) Regulations 1998.',
                    'Property: 14 Willow Court, Wharf Road, London N1 7UX.',
                    'Engineer: Danny Whitmore, Gas Safe registration 512345.',
                    'Inspection date: '.now()->subMonths(2)->format('j F Y').'. Next inspection due: '.now()->addMonths(10)->format('j F Y').'.',
                    '',
                    'All appliances and flues inspected have been recorded as safe to use.',
                ],
            ],
            [
                'title' => 'Energy Performance Certificate',
                'category' => 'compliance',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => null,
                'lines' => [
                    'Energy Performance Certificate for 14 Willow Court, Unit 4B.',
                    'Current rating: B (84). Potential rating: B (86).',
                    'Certificate reference: EPC-9812-4410-2201.',
                    'Valid until: '.now()->addYears(8)->format('j F Y').'.',
                    '',
                    'Recommendations: retain the existing LED lighting and maintain the boiler annually to preserve efficiency.',
                ],
            ],
            [
                'title' => 'Rent Statement — '.now()->subMonthNoOverflow()->format('F Y'),
                'category' => 'financial',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => $tenancy->id,
                'lines' => [
                    'Rent statement for Unit 4B, 14 Willow Court.',
                    'Tenant: Alex Morgan. Tenancy: '.$tenancy->reference.'.',
                    'Rent due: £1,850.00. Payments received: £1,850.00. Balance: £0.00.',
                    '',
                    'This statement is generated from the PropNest ledger. Please retain it for your records.',
                ],
            ],
            [
                'title' => 'Building Insurance Schedule',
                'category' => 'property',
                'property_id' => $properties['willow']->id,
                'tenancy_id' => null,
                'lines' => [
                    'Buildings insurance summary for 14 Willow Court.',
                    'Policyholder: Harbour & Vine Property Group. Insurer: Albion Residential.',
                    'Cover includes the structure, communal areas and landlord fixtures.',
                    'Contents belonging to the tenant are not covered by this policy.',
                ],
            ],
        ];

        foreach ($documents as $definition) {
            $slug = Str::slug($definition['title']).'.pdf';
            $path = 'documents/'.$slug;
            $pdf = SimplePdf::make(
                $definition['title'],
                'Harbour & Vine Property Group · Issued '.now()->format('j F Y'),
                $definition['lines']
            );

            Storage::disk('public')->put($path, $pdf);

            Document::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'tenancy_id' => $definition['tenancy_id'],
                    'title' => $definition['title'],
                ],
                [
                    'property_id' => $definition['property_id'],
                    'uploaded_by' => $users['manager']->id,
                    'category' => $definition['category'],
                    'file_path' => $path,
                    'version' => '1.0',
                    'visibility' => 'tenant',
                    'file_size' => strlen($pdf),
                    'mime_type' => 'application/pdf',
                ]
            );
        }
    }

    /**
     * @param  array<string, Property>  $properties
     * @param  array<string, User>  $users
     */
    private function seedAnnouncements(Organization $organization, array $properties, array $users): void
    {
        $announcements = [
            [
                'title' => 'Lift maintenance at Willow Court',
                'property_id' => $properties['willow']->id,
                'body' => 'The passenger lift will be out of service on Thursday between 09:00 and 13:00 while our engineer replaces the door sensor. Please use the stairs during this time. We are sorry for any inconvenience and will post an update as soon as the lift is back in service.',
                'audience' => 'tenants',
                'pinned' => true,
            ],
            [
                'title' => 'Fire alarm testing — this Friday',
                'property_id' => null,
                'body' => 'The monthly fire alarm test will take place this Friday at 10:00 and should last around five minutes. You do not need to leave your home. If you use a hearing-impaired alarm or have access requirements, please reply to this notice so we can make arrangements.',
                'audience' => 'all',
                'pinned' => false,
            ],
            [
                'title' => 'A small reminder about rent day',
                'property_id' => null,
                'body' => 'Rent is due on the first of each month. If your circumstances change, please speak to us early — we would much rather help you find a workable plan than let arrears build up. You can view your ledger and receipts any time under Rent & payments.',
                'audience' => 'tenants',
                'pinned' => false,
            ],
        ];

        foreach ($announcements as $index => $definition) {
            Announcement::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'title' => $definition['title'],
                ],
                [
                    ...$definition,
                    'author_id' => $users['manager']->id,
                    'published_at' => now()->subDays($index + 1),
                ]
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function seedMaintenance(Organization $organization, Tenancy $tenancy, array $users): void
    {
        $definitions = [
            [
                'reference' => 'MR-26-0001',
                'title' => 'Kitchen tap dripping',
                'category' => 'Plumbing',
                'description' => 'The mixer tap over the kitchen sink has started dripping constantly. It is not flooding, but it is wasting water and the noise is noticeable at night.',
                'priority' => 'normal',
                'status' => 'in_progress',
                'assigned_to' => $users['contractor']->id,
                'scheduled_for' => now()->addDay()->setTime(10, 30),
                'completed_at' => null,
                'cost' => null,
                'tenant_feedback' => null,
                'updates' => [
                    ['message' => 'Request submitted and added to the property manager queue.', 'status_to' => 'submitted', 'user_id' => $users['tenant']->id],
                    ['message' => 'Triaged as a plumbing issue. Danny has been asked to attend.', 'status_from' => 'submitted', 'status_to' => 'triaged', 'user_id' => $users['manager']->id],
                    ['message' => 'Visit booked for tomorrow between 10:30 and 12:00. Danny will bring a replacement cartridge.', 'status_from' => 'triaged', 'status_to' => 'scheduled', 'user_id' => $users['manager']->id],
                    ['message' => 'On my way. I have the parts with me and expect this to take around 30 minutes.', 'status_from' => 'scheduled', 'status_to' => 'in_progress', 'user_id' => $users['contractor']->id],
                ],
            ],
            [
                'reference' => 'MR-26-0002',
                'title' => 'Boiler pressure warning',
                'category' => 'Heating',
                'description' => 'The boiler is showing a low pressure warning (0.8 bar). The heating still comes on but the radiators downstairs are slow to warm up.',
                'priority' => 'high',
                'status' => 'scheduled',
                'assigned_to' => $users['contractor']->id,
                'scheduled_for' => now()->addDays(2)->setTime(9, 0),
                'completed_at' => null,
                'cost' => null,
                'tenant_feedback' => null,
                'updates' => [
                    ['message' => 'Request submitted and added to the property manager queue.', 'status_to' => 'submitted', 'user_id' => $users['tenant']->id],
                    ['message' => 'Thanks for flagging this. We have asked our Gas Safe engineer to attend.', 'status_from' => 'submitted', 'status_to' => 'scheduled', 'user_id' => $users['manager']->id],
                ],
            ],
            [
                'reference' => 'MR-26-0003',
                'title' => 'Living room window latch',
                'category' => 'Security',
                'description' => 'The latch on the large living room window does not close fully, so the window rattles in windy weather. It still locks, but it does not feel secure.',
                'priority' => 'normal',
                'status' => 'completed',
                'assigned_to' => $users['contractor']->id,
                'scheduled_for' => now()->subDays(26)->setTime(14, 0),
                'completed_at' => now()->subDays(25)->setTime(15, 30),
                'cost' => 85,
                'tenant_feedback' => null,
                'updates' => [
                    ['message' => 'Request submitted and added to the property manager queue.', 'status_to' => 'submitted', 'user_id' => $users['tenant']->id],
                    ['message' => 'Latch replaced and the window tested. Please let us know if anything still feels loose.', 'status_from' => 'in_progress', 'status_to' => 'completed', 'user_id' => $users['contractor']->id],
                ],
            ],
            [
                'reference' => 'MR-26-0004',
                'title' => 'Front door lock is stiff',
                'category' => 'Security',
                'description' => 'The front door lock has become difficult to turn over the last week. The key works but needs a firm push and twist.',
                'priority' => 'low',
                'status' => 'submitted',
                'assigned_to' => null,
                'scheduled_for' => null,
                'completed_at' => null,
                'cost' => null,
                'tenant_feedback' => null,
                'updates' => [
                    ['message' => 'Request submitted and added to the property manager queue.', 'status_to' => 'submitted', 'user_id' => $users['tenant']->id],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $updates = $definition['updates'];
            unset($definition['updates']);

            $request = MaintenanceRequest::updateOrCreate(
                ['reference' => $definition['reference']],
                [
                    ...$definition,
                    'organization_id' => $organization->id,
                    'property_id' => $tenancy->property_id,
                    'tenancy_id' => $tenancy->id,
                    'submitted_by' => $users['tenant']->id,
                ]
            );

            foreach ($updates as $offset => $update) {
                MaintenanceUpdate::updateOrCreate(
                    [
                        'maintenance_request_id' => $request->id,
                        'message' => $update['message'],
                    ],
                    [
                        ...$update,
                        'created_at' => now()->subDays(count($updates) - $offset)->setTime(9 + $offset, 15),
                        'updated_at' => now()->subDays(count($updates) - $offset)->setTime(9 + $offset, 15),
                    ]
                );
            }
        }
    }

    private function seedServiceRequests(Tenancy $tenancy): void
    {
        $requests = [
            [
                'reference' => 'SR-26-0001',
                'type' => 'renewal',
                'subject' => 'Would like to renew for another 12 months',
                'details' => 'We have loved living at Willow Court and would like to stay for another year if possible. A 12-month renewal at the current rent would be ideal.',
                'status' => 'in_review',
                'response' => null,
                'resolved_at' => null,
                'created_at' => now()->subDays(4),
            ],
            [
                'reference' => 'SR-26-0002',
                'type' => 'pet',
                'subject' => 'Permission for a small cat',
                'details' => 'I am hoping to adopt a quiet, indoor cat from a local rescue. I would be happy to sign a pet addendum and cover any additional cleaning at the end of the tenancy.',
                'status' => 'action_required',
                'response' => 'Thanks for checking with us first. We are supportive in principle — please send the rescue’s details and we will confirm the pet addendum terms.',
                'resolved_at' => null,
                'created_at' => now()->subDays(11),
            ],
            [
                'reference' => 'SR-26-0003',
                'type' => 'details',
                'subject' => 'New mobile number',
                'details' => 'Please update my contact number to 07700 900456. My email address has not changed.',
                'status' => 'completed',
                'response' => 'Your contact number has been updated. Thanks for letting us know.',
                'resolved_at' => now()->subDays(30),
                'created_at' => now()->subDays(32),
            ],
        ];

        foreach ($requests as $request) {
            ServiceRequest::updateOrCreate(
                ['reference' => $request['reference']],
                [
                    ...$request,
                    'tenancy_id' => $tenancy->id,
                    'user_id' => $tenancy->tenant_id,
                ]
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function seedNotifications(array $users): void
    {
        $notifications = [
            [
                'title' => 'Rent payment is overdue',
                'message' => 'Your '.now()->format('F').' rent of £1,850.00 is now overdue. You can settle it securely from Rent & payments.',
                'type' => 'payment',
                'link' => '/payments',
                'read_at' => null,
                'created_at' => now()->subHours(3),
            ],
            [
                'title' => 'Boiler visit booked',
                'message' => 'Danny will attend on '.now()->addDays(2)->format('l').' between 09:00 and 10:00 to look at the boiler pressure.',
                'type' => 'maintenance',
                'link' => '/maintenance',
                'read_at' => null,
                'created_at' => now()->subDay(),
            ],
            [
                'title' => 'New document added',
                'message' => 'Your '.now()->subMonthNoOverflow()->format('F').' rent statement is now available in Documents.',
                'type' => 'document',
                'link' => '/documents',
                'read_at' => now()->subDays(2),
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Lift maintenance at Willow Court',
                'message' => 'The lift will be out of service on Thursday between 09:00 and 13:00.',
                'type' => 'announcement',
                'link' => '/announcements',
                'read_at' => now()->subDays(4),
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($notifications as $notification) {
            PortalNotification::updateOrCreate(
                [
                    'user_id' => $users['tenant']->id,
                    'title' => $notification['title'],
                ],
                $notification
            );
        }

        PortalNotification::updateOrCreate(
            [
                'user_id' => $users['manager']->id,
                'title' => 'New maintenance request',
            ],
            [
                'message' => 'MR-26-0004 — Front door lock is stiff was submitted at 14 Willow Court.',
                'type' => 'maintenance',
                'link' => '/admin/maintenance',
                'read_at' => null,
                'created_at' => now()->subHours(5),
            ]
        );
    }
}
