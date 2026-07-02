<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('slug')->unique();
            $table->decimal('advance_percent', 5, 2)->default(30);
            $table->decimal('minimum_advance', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->string('razorpay_key_id')->nullable();
            $table->text('razorpay_key_secret')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('city_payment_settings')->insert([
            ['city' => 'Delhi', 'slug' => 'delhi', 'advance_percent' => 30, 'minimum_advance' => 500, 'service_fee' => 149, 'tax_percent' => 5, 'payment_instructions' => 'Delhi bookings are confirmed after the city advance is received.', 'is_default' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['city' => 'Noida', 'slug' => 'noida', 'advance_percent' => 35, 'minimum_advance' => 750, 'service_fee' => 199, 'tax_percent' => 5, 'payment_instructions' => 'Noida and Greater Noida travel is included within the listed service area.', 'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['city' => 'Gurgaon', 'slug' => 'gurgaon', 'advance_percent' => 40, 'minimum_advance' => 1000, 'service_fee' => 249, 'tax_percent' => 5, 'payment_instructions' => 'Gurgaon bookings may require venue access and parking confirmation.', 'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['city' => 'Ghaziabad', 'slug' => 'ghaziabad', 'advance_percent' => 35, 'minimum_advance' => 750, 'service_fee' => 199, 'tax_percent' => 5, 'payment_instructions' => 'Ghaziabad bookings are subject to artist availability for the selected date.', 'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['city' => 'Faridabad', 'slug' => 'faridabad', 'advance_percent' => 40, 'minimum_advance' => 1000, 'service_fee' => 249, 'tax_percent' => 5, 'payment_instructions' => 'Faridabad travel and timing are confirmed before payment.', 'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('city_payment_setting_id')->nullable()->after('package_id')->constrained()->nullOnDelete();
            $table->decimal('base_amount', 10, 2)->default(0)->after('payment_type');
            $table->decimal('service_fee', 10, 2)->default(0)->after('base_amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('service_fee');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_payment_setting_id');
            $table->dropColumn(['base_amount', 'service_fee', 'tax_amount']);
        });

        Schema::dropIfExists('city_payment_settings');
    }
};
