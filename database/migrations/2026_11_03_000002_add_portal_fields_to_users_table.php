<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role')->default('tenant')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('status')->default('active')->after('job_title');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->boolean('two_factor_enabled')->default(false)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn([
                'role',
                'phone',
                'job_title',
                'status',
                'last_login_at',
                'two_factor_enabled',
            ]);
        });
    }
};
