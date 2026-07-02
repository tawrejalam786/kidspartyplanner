<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('state')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('pincode', 12)->nullable();
            $table->decimal('travel_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['city_id', 'slug']);
        });

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });

        Schema::create('service_city_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('advance_percent', 5, 2)->nullable();
            $table->decimal('travel_fee', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['service_id', 'city_id']);
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addon_service', function (Blueprint $table) {
            $table->foreignId('addon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->primary(['addon_id', 'service_id']);
        });

        Schema::create('package_service', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->primary(['package_id', 'service_id']);
        });

        Schema::create('city_package', function (Blueprint $table) {
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->primary(['city_id', 'package_id']);
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->json('selected_addons')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->string('item_type')->default('service');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'service_id']);
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('placement')->default('home');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->string('group')->default('general');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('Requested');
            $table->text('reason');
            $table->string('gateway_refund_id')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->string('age_group')->nullable();
            $table->unsignedInteger('kids_capacity')->nullable();
            $table->json('requirements')->nullable();
            $table->text('terms')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->string('video_url')->nullable();
            $table->decimal('advance_percent', 5, 2)->nullable();
            $table->boolean('trending')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('trending')->default(false);
            $table->text('terms')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('area_name')->nullable();
            $table->text('full_address')->nullable();
            $table->string('landmark')->nullable();
            $table->string('event_type')->nullable();
            $table->string('age_group')->nullable();
            $table->string('venue_type')->nullable();
            $table->string('decoration_theme')->nullable();
            $table->string('workflow_status')->default('New')->index();
            $table->string('payment_status')->default('Pending')->index();
            $table->string('tracking_status')->default('Booking Placed');
            $table->string('invoice_no')->nullable()->unique();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_status')->default('Pending')->index();
            $table->decimal('refunded_amount', 10, 2)->default(0);
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
        });

        $now = now();
        foreach ([
            ['Delhi', 'delhi', 'Delhi', true, true, 1],
            ['Noida', 'noida', 'Uttar Pradesh', true, true, 2],
            ['Gurgaon', 'gurgaon', 'Haryana', true, true, 3],
            ['Mumbai', 'mumbai', 'Maharashtra', false, false, 4],
            ['Pune', 'pune', 'Maharashtra', false, false, 5],
            ['Jaipur', 'jaipur', 'Rajasthan', false, false, 6],
        ] as [$name, $slug, $state, $current, $active, $sort]) {
            DB::table('cities')->insert([
                'name' => $name,
                'slug' => $slug,
                'state' => $state,
                'is_current' => $current,
                'is_active' => $active,
                'sort_order' => $sort,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (DB::table('city_payment_settings')->get() as $setting) {
            $cityId = DB::table('cities')->where('slug', $setting->slug)->value('id');
            if ($cityId) {
                DB::table('bookings')->where('city_payment_setting_id', $setting->id)->update(['city_id' => $cityId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn(['meta_keywords', 'og_image']));
        Schema::table('blogs', fn (Blueprint $table) => $table->dropColumn(['meta_keywords', 'og_image']));
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn(['payment_status', 'refunded_amount']));
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('area_id');
            $table->dropColumn(['area_name', 'full_address', 'landmark', 'event_type', 'age_group', 'venue_type', 'decoration_theme', 'workflow_status', 'payment_status', 'tracking_status', 'invoice_no', 'cancellation_reason', 'cancelled_at']);
        });
        Schema::table('packages', fn (Blueprint $table) => $table->dropColumn(['trending', 'terms', 'meta_keywords', 'og_image']));
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subcategory_id');
            $table->dropColumn(['age_group', 'kids_capacity', 'requirements', 'terms', 'cancellation_policy', 'video_url', 'advance_percent', 'trending', 'sort_order', 'meta_keywords', 'og_image']);
        });
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['sort_order', 'meta_title', 'meta_description', 'meta_keywords', 'og_image']));

        Schema::dropIfExists('refunds');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('booking_addons');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('city_package');
        Schema::dropIfExists('package_service');
        Schema::dropIfExists('addon_service');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('service_city_prices');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cities');
    }
};
