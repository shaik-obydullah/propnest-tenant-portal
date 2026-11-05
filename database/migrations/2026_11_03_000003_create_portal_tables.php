<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('landlord_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('reference')->unique();
            $table->string('type')->default('apartment');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('postcode');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);
            $table->unsignedInteger('size_sqft')->nullable();
            $table->string('status')->default('available');
            $table->string('accent')->default('forest');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number');
            $table->string('floor')->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->string('status')->default('occupied');
            $table->timestamps();
            $table->unique(['property_id', 'unit_number']);
        });

        Schema::create('tenancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2);
            $table->string('payment_frequency')->default('monthly');
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->string('deposit_scheme')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenancy_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('method')->nullable();
            $table->string('status')->default('pending');
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->string('priority')->default('normal');
            $table->string('status')->default('submitted');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('tenant_feedback')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('tenancy_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('category');
            $table->string('file_path');
            $table->string('version')->default('1.0');
            $table->string('visibility')->default('tenant');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('audience')->default('tenants');
            $table->boolean('pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->string('type');
            $table->string('subject');
            $table->text('details');
            $table->string('status')->default('submitted');
            $table->text('response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_notifications');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('maintenance_updates');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('rent_payments');
        Schema::dropIfExists('tenancies');
        Schema::dropIfExists('units');
        Schema::dropIfExists('properties');
    }
};
