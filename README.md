# PropNest Tenant Portal

A multi-role property management portal built with **Laravel 12**. Tenants manage their
tenancy, rent, maintenance requests, documents and announcements; staff run the portfolio
from an admin workspace; landlords and contractors get focused dashboards of their own.

The application ships with a seeded demo organisation (Harbour & Vine Property Group) so
you can explore every role immediately after starting it.

---

## Features

### Tenant portal (`/dashboard`)
- Dashboard with tenancy summary, upcoming rent and open requests
- Tenancy details (dates, rent, deposit and deposit scheme)
- Rent ledger, secure checkout flow for outstanding payments, and downloadable receipts
- Maintenance requests: submit, track status, add updates and leave feedback
- Document library with PDF downloads (tenancy, compliance, financial, property)
- Service requests (renewal, notice to vacate, details change, pet permission, etc.)
- Property announcements and in-app notifications
- Profile and password management

### Staff workspace (`/admin`)
Available to `platform_admin`, `agency_admin` and `property_manager` roles.
- Portfolio dashboard
- Properties: list, create, view and edit
- Tenancies: list, view and mark rent payments as paid
- Maintenance: triage, schedule, update status, assign contractors and record costs
- Tenants: list and view
- Announcements: publish and delete

### Landlord portal (`/landlord`)
- Dashboard of owned properties and a read-only property view

### Contractor portal (`/contractor`)
- List of jobs assigned to the signed-in contractor
- Job detail and status updates

### Authentication
- Login, registration (creates a tenant account), forgot/reset password, logout
- Role-based redirects to the correct dashboard after login
- Role authorization enforced by the `role` middleware

---

## Tech stack

| Layer | Technology |
| --- | --- |
| Framework | Laravel 12 |
| Language | PHP 8.2+ (container uses PHP 8.4) |
| Database | MySQL 8.4 (SQLite supported; used by the test suite) |
| Front end | Blade, Tailwind CSS 4, Vite 7 |
| Web server | Apache (Docker) |
| Testing | PHPUnit 11 |
| Local infra | Docker Compose (app, MySQL, phpMyAdmin) |

---

## Getting started

### Option 1 — Docker (recommended)

Requirements: Docker with the Compose plugin.

```bash
docker compose up -d --build
```

| Service | URL / Port |
| --- | --- |
| Application | http://localhost:8473 |
| phpMyAdmin | http://localhost:8474 |
| MySQL | `localhost:33068` (database `propnest`, user `propnest`, password `propnest_secret`) |

On start the entrypoint installs dependencies, links storage, runs migrations and seeds the
demo data (`SEED_DATABASE=true`). Compose environment variables drive the app — the container
does not rely on a host `.env` file.

Common commands:

```bash
docker compose logs -f app     # follow application logs
docker compose exec app bash   # shell into the container
docker compose down            # stop the stack
docker compose down -v         # stop and delete database/storage volumes
```

### Option 2 — Local PHP + Node

Requirements: PHP 8.2+, Composer, Node 20+, and a database.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`. To use SQLite locally:

```dotenv
DB_CONNECTION=sqlite
# DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD can be removed
```

```bash
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

For front-end hot reloading, run `npm run dev` alongside `php artisan serve`, or use the
combined helper:

```bash
composer dev
```

The `composer setup` script performs most first-time steps (install, `.env`, key, migrate,
npm install and build) in one go.

---

## Demo accounts

All seeded accounts use the password **`password`**.

| Role | Email |
| --- | --- |
| Platform administrator | `admin@propnest.test` |
| Agency administrator | `agency@propnest.test` |
| Property manager | `manager@propnest.test` |
| Landlord | `landlord@propnest.test` |
| Tenant (main demo) | `alex.morgan@propnest.test` |
| Tenant | `priya.shah@propnest.test` |
| Tenant | `daniel.okafor@propnest.test` |
| Tenant | `chloe.bennett@propnest.test` |
| Contractor | `contractor@propnest.test` |

Each role is redirected to its own dashboard after login:

| Role | Path |
| --- | --- |
| `platform_admin`, `agency_admin`, `property_manager` | `/admin` |
| `landlord` | `/landlord` |
| `contractor` | `/contractor` |
| `tenant` | `/dashboard` |

---

## Testing

```bash
composer test        # clears config cache and runs the suite
# or
php artisan test
```

Tests run against an in-memory SQLite database and cover authentication, role isolation,
tenant portal pages, rent payment, maintenance submissions and contractor scoping.

---

## Project structure

```
app/
├── Enums/UserRole.php              # Role definitions, labels and dashboard mapping
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                  # Staff workspace
│   │   ├── Auth/                   # Login, register, password reset
│   │   ├── Contractor/             # Assigned jobs
│   │   ├── Landlord/               # Landlord dashboard
│   │   └── Tenant/                 # Tenant portal features
│   └── Middleware/EnsureUserHasRole.php
├── Models/                         # Eloquent models
└── Support/                        # Reference generation, simple PDF writer, helpers
config/portal.php                   # Portal branding, demo credentials, workflow options
database/
├── migrations/                     # Users, organizations and portal tables
└── seeders/DatabaseSeeder.php      # Full demo organisation and data
docker/entrypoint.sh                # Container bootstrap (migrate + seed + Apache)
resources/views/                    # Blade views per role
routes/web.php                      # All web routes
tests/Feature/PortalTest.php        # End-to-end portal behaviour
```

### Domain model

`Organization` → `Property` → `Unit` → `Tenancy` → `RentPayment`, with supporting models for
`MaintenanceRequest` / `MaintenanceUpdate`, `Document`, `Announcement`, `ServiceRequest`,
`PortalNotification` and `User` (role-scoped).

---

## Configuration

Portal-level settings live in `config/portal.php`:

- Branding name and currency (`GBP`, `£`)
- Demo credential shortcuts
- Maintenance categories, priorities and statuses
- Service request types
- Document categories

Workflow options such as rent, maintenance and document categories are referenced throughout
the UI, so extending them is a single-file change.

---

## License

This project is released under the [MIT license](https://opensource.org/licenses/MIT).
