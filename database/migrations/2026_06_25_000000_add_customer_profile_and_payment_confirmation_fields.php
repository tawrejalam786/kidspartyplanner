<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pending_email')->nullable()->after('email');
            $table->string('email_change_token', 64)->nullable()->index()->after('pending_email');
            $table->timestamp('email_change_requested_at')->nullable()->after('email_change_token');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('confirmation_emailed_at')->nullable()->after('cancelled_at');
        });

        Schema::table('city_payment_settings', function (Blueprint $table) {
            $table->text('razorpay_webhook_secret')->nullable()->after('razorpay_key_secret');
        });
    }

    public function down(): void
    {
        Schema::table('city_payment_settings', function (Blueprint $table) {
            $table->dropColumn('razorpay_webhook_secret');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('confirmation_emailed_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pending_email', 'email_change_token', 'email_change_requested_at']);
        });
    }
};
