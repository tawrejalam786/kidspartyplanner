<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'customer'");
        } elseif (DB::getDriverName() === 'sqlite') {
            $this->expandSqliteUserRoleConstraint();
        }

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->string('contact_person');
            $table->string('phone', 20);
            $table->string('email');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('address')->nullable();
            $table->json('coverage_areas')->nullable();
            $table->json('bank_details')->nullable();
            $table->json('documents')->nullable();
            $table->decimal('commission_percent', 5, 2)->default(20);
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->string('status')->default('Pending')->index();
            $table->text('admin_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('service_vendor', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->primary(['service_id', 'vendor_id']);
        });

        Schema::create('booking_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('Assigned')->index();
            $table->decimal('assigned_amount', 10, 2)->default(0);
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('vendor_earning', 10, 2)->default(0);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'vendor_id']);
        });

        Schema::create('vendor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('gross_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('status')->default('Pending')->index();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('vendor_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('Requested')->index();
            $table->json('bank_details')->nullable();
            $table->string('payout_reference')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawals');
        Schema::dropIfExists('vendor_earnings');
        Schema::dropIfExists('booking_assignments');
        Schema::dropIfExists('service_vendor');
        Schema::dropIfExists('vendors');
    }

    private function expandSqliteUserRoleConstraint(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        DB::statement(<<<'SQL'
CREATE TABLE users_role_fix (
    id integer primary key autoincrement not null,
    name varchar not null,
    email varchar not null,
    pending_email varchar,
    email_change_token varchar,
    email_change_requested_at datetime,
    google_id varchar,
    avatar varchar,
    phone varchar(20),
    role varchar not null default 'customer' check ("role" in ('admin', 'customer', 'vendor')),
    city varchar,
    address text,
    email_verified_at datetime,
    password varchar not null,
    remember_token varchar,
    created_at datetime,
    updated_at datetime
)
SQL);
        DB::statement(<<<'SQL'
INSERT INTO users_role_fix (
    id, name, email, pending_email, email_change_token, email_change_requested_at, google_id, avatar,
    phone, role, city, address, email_verified_at, password, remember_token, created_at, updated_at
)
SELECT
    id, name, email, pending_email, email_change_token, email_change_requested_at, google_id, avatar,
    phone, role, city, address, email_verified_at, password, remember_token, created_at, updated_at
FROM users
SQL);
        DB::statement('DROP TABLE users');
        DB::statement('ALTER TABLE users_role_fix RENAME TO users');
        DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        DB::statement('CREATE UNIQUE INDEX users_google_id_unique ON users (google_id)');
        DB::statement('CREATE INDEX users_role_index ON users (role)');
        DB::statement('CREATE INDEX users_email_change_token_index ON users (email_change_token)');
        DB::statement('PRAGMA foreign_keys=ON');
    }
};
