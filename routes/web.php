<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\TenancyController as AdminTenancyController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Contractor\JobController as ContractorJobController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\AnnouncementController as TenantAnnouncementController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\DocumentController as TenantDocumentController;
use App\Http\Controllers\Tenant\MaintenanceController as TenantMaintenanceController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\ServiceRequestController as TenantServiceRequestController;
use App\Http\Controllers\Tenant\TenancyController as TenantTenancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::get('/dashboard', TenantDashboardController::class)->name('tenant.dashboard');

    Route::get('/tenancy', [TenantTenancyController::class, 'show'])->name('tenant.tenancy.show');

    Route::get('/payments', [TenantPaymentController::class, 'index'])->name('tenant.payments.index');
    Route::get('/payments/checkout', [TenantPaymentController::class, 'checkout'])->name('tenant.payments.checkout');
    Route::post('/payments/{payment}/pay', [TenantPaymentController::class, 'pay'])->name('tenant.payments.pay');
    Route::get('/payments/{payment}/receipt', [TenantPaymentController::class, 'receipt'])->name('tenant.payments.receipt');

    Route::get('/maintenance', [TenantMaintenanceController::class, 'index'])->name('tenant.maintenance.index');
    Route::get('/maintenance/new', [TenantMaintenanceController::class, 'create'])->name('tenant.maintenance.create');
    Route::post('/maintenance', [TenantMaintenanceController::class, 'store'])->name('tenant.maintenance.store');
    Route::get('/maintenance/{maintenance}', [TenantMaintenanceController::class, 'show'])->name('tenant.maintenance.show');
    Route::post('/maintenance/{maintenance}/updates', [TenantMaintenanceController::class, 'addUpdate'])->name('tenant.maintenance.updates.store');
    Route::post('/maintenance/{maintenance}/feedback', [TenantMaintenanceController::class, 'feedback'])->name('tenant.maintenance.feedback');

    Route::get('/documents', [TenantDocumentController::class, 'index'])->name('tenant.documents.index');
    Route::get('/documents/{document}/download', [TenantDocumentController::class, 'download'])->name('tenant.documents.download');

    Route::get('/requests', [TenantServiceRequestController::class, 'index'])->name('tenant.requests.index');
    Route::get('/requests/new', [TenantServiceRequestController::class, 'create'])->name('tenant.requests.create');
    Route::post('/requests', [TenantServiceRequestController::class, 'store'])->name('tenant.requests.store');
    Route::get('/requests/{serviceRequest}', [TenantServiceRequestController::class, 'show'])->name('tenant.requests.show');

    Route::get('/announcements', [TenantAnnouncementController::class, 'index'])->name('tenant.announcements.index');
    Route::get('/announcements/{announcement}', [TenantAnnouncementController::class, 'show'])->name('tenant.announcements.show');
});

Route::middleware(['auth', 'role:platform_admin,agency_admin,property_manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [AdminPropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [AdminPropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}', [AdminPropertyController::class, 'show'])->name('properties.show');
        Route::get('/properties/{property}/edit', [AdminPropertyController::class, 'edit'])->name('properties.edit');
        Route::patch('/properties/{property}', [AdminPropertyController::class, 'update'])->name('properties.update');

        Route::get('/tenancies', [AdminTenancyController::class, 'index'])->name('tenancies.index');
        Route::get('/tenancies/{tenancy}', [AdminTenancyController::class, 'show'])->name('tenancies.show');
        Route::post('/payments/{payment}/mark-paid', [AdminTenancyController::class, 'markPaid'])->name('payments.mark-paid');

        Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/{maintenance}', [AdminMaintenanceController::class, 'show'])->name('maintenance.show');
        Route::patch('/maintenance/{maintenance}', [AdminMaintenanceController::class, 'update'])->name('maintenance.update');

        Route::get('/tenants', [AdminTenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/{tenancy}', [AdminTenantController::class, 'show'])->name('tenants.show');

        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

Route::middleware(['auth', 'role:landlord'])
    ->prefix('landlord')
    ->name('landlord.')
    ->group(function () {
        Route::get('/', LandlordDashboardController::class)->name('dashboard');
        Route::get('/properties/{property}', [LandlordDashboardController::class, 'property'])->name('properties.show');
    });

Route::middleware(['auth', 'role:contractor'])
    ->prefix('contractor')
    ->name('contractor.')
    ->group(function () {
        Route::get('/', [ContractorJobController::class, 'index'])->name('dashboard');
        Route::get('/jobs/{maintenance}', [ContractorJobController::class, 'show'])->name('jobs.show');
        Route::patch('/jobs/{maintenance}', [ContractorJobController::class, 'update'])->name('jobs.update');
    });
