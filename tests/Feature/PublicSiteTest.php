<?php

namespace Tests\Feature;

use App\Mail\PaymentConfirmed;
use App\Mail\VerifyEmailChange;
use App\Models\Addon;
use App\Models\Banner;
use App\Models\Booking;
use App\Models\City;
use App\Models\CityPaymentSetting;
use App\Models\BookingAssignment;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorEarning;
use App\Models\VendorWithdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/categories', '/services', '/packages', '/gallery', '/reviews', '/contact', '/faq', '/cart', '/track-booking', '/terms', '/privacy-policy', '/refund-policy'] as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/delhi/birthday-decoration')->assertOk()->assertSee('Birthday Decoration');
        $this->get('/')
            ->assertOk()
            ->assertSee('Kids Activities & Games')
            ->assertSee('mobile-bottom-nav', false)
            ->assertSee('assets/images/kidspartyplanner-logo.png', false);
        $this->get('/services?search=Magic')
            ->assertOk()
            ->assertSee('Results for')
            ->assertSee('mobile-filter-toolbar', false)
            ->assertSee('service-filter-mobile', false)
            ->assertSee('Magic Show');
        $this->get('/login')->assertOk()->assertSee('Continue with Google');
        $this->get('/register')->assertOk()->assertSee('Continue with Google');
        $this->get('/vendors/register')->assertOk()->assertSee('Join Kids Party Planner');
    }

    public function test_google_login_fails_gracefully_until_credentials_are_configured(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $this->get('/auth/google/redirect')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_google_callback_creates_and_authenticates_customer(): void
    {
        config(['services.google.client_id' => 'test-client', 'services.google.client_secret' => 'test-secret']);
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-user-123');
        $googleUser->shouldReceive('getEmail')->andReturn('google-parent@example.com');
        $googleUser->shouldReceive('getName')->andReturn('Google Parent');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
        Socialite::shouldReceive('driver')->once()->with('google')->andReturnSelf();
        Socialite::shouldReceive('user')->once()->andReturn($googleUser);

        $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'google-parent@example.com',
            'google_id' => 'google-user-123',
            'role' => 'customer',
        ]);
    }

    public function test_customer_dashboard_renders_for_customer(): void
    {
        $customer = User::where('email', 'parent@example.com')->firstOrFail();

        $this->actingAs($customer)->get('/dashboard')->assertOk();
        $this->actingAs($customer)->get('/services/magic-show')
            ->assertOk()
            ->assertSee('Reserve this experience')
            ->assertSee('How early should we book?');
    }

    public function test_admin_dashboard_renders_for_admin(): void
    {
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_admin_can_log_in_with_seeded_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@kidspartyplanner.in',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_booking_uses_city_specific_payment_rules(): void
    {
        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $delhi = CityPaymentSetting::where('slug', 'delhi')->firstOrFail();

        $response = $this->actingAs($customer)->post('/booking', [
            'service_id' => $service->id,
            'city_payment_setting_id' => $delhi->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'event_date' => today()->addWeek()->format('Y-m-d'),
            'event_time' => '16:00',
            'full_address' => 'South Delhi party venue, near metro gate 2',
            'number_of_kids' => 15,
            'payment_type' => 'advance',
        ]);

        $booking = Booking::latest()->firstOrFail();

        $response->assertRedirect(route('payments.checkout', $booking));
        $this->assertSame($delhi->id, $booking->city_payment_setting_id);
        $this->assertSame('149.00', $booking->service_fee);
        $this->assertSame('97.45', $booking->tax_amount);
        $this->assertSame('2046.45', $booking->total_amount);
        $this->assertSame('613.94', $booking->payable_amount);
        $this->assertSame('South Delhi party venue, near metro gate 2', $booking->full_address);
        $this->assertSame('South Delhi party venue, near metro gate 2', $booking->location);
    }

    public function test_booking_rejects_offline_payment_type(): void
    {
        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $delhi = CityPaymentSetting::where('slug', 'delhi')->firstOrFail();

        $this->actingAs($customer)->post('/booking', [
            'service_id' => $service->id,
            'city_payment_setting_id' => $delhi->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'event_date' => today()->addWeek()->format('Y-m-d'),
            'event_time' => '16:00',
            'full_address' => 'South Delhi party venue',
            'number_of_kids' => 15,
            'payment_type' => 'offline',
        ])->assertSessionHasErrors('payment_type');
    }

    public function test_admin_can_open_city_payment_settings(): void
    {
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();

        $this->actingAs($admin)->get('/admin/city-payments')->assertOk()->assertSee('City Payment Settings');
        $this->actingAs($admin)->get('/admin/service-prices')->assertOk()->assertSee('Service City Pricing');
        $this->actingAs($admin)->get('/admin/banners')->assertOk()->assertSee('Banner Management');
    }

    public function test_admin_can_search_resource_records(): void
    {
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();

        $this->actingAs($admin)->get('/admin/services?search=Animal+Magic')
            ->assertOk()
            ->assertSee('Animal Magic')
            ->assertDontSee('Adult Games');
    }

    public function test_service_search_uses_base_price_when_city_override_is_missing(): void
    {
        $service = Service::where('slug', 'animal-magic')->firstOrFail();
        $service->cityPrices()->delete();

        $this->get('/services?search=Animal+Magic&city=delhi')
            ->assertOk()
            ->assertSee('Animal Magic')
            ->assertDontSee('No services matched these filters.');
    }

    public function test_admin_can_replace_a_service_primary_image(): void
    {
        Storage::fake('public');
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $service->images()->delete();
        $service->images()->create([
            'path' => 'services/old-image.jpg',
            'alt_text' => $service->title,
            'is_primary' => true,
        ]);
        Storage::disk('public')->put('services/old-image.jpg', 'old image');

        $response = $this->actingAs($admin)->put('/admin/services/'.$service->id, [
            'category_id' => (string) $service->category_id,
            'subcategory_id' => $service->subcategory_id ? (string) $service->subcategory_id : null,
            'title' => $service->title,
            'slug' => $service->slug,
            'price' => $service->price,
            'is_active' => 1,
            'primary_image' => $this->tinyPngUpload('updated-service.png'),
        ]);

        $response->assertRedirect(route('admin.resources.index', 'services'));
        $primaryImage = $service->refresh()->primaryImage;
        $this->assertNotNull($primaryImage);
        $this->assertSame(1, $service->images()->count());
        Storage::disk('public')->assertExists($primaryImage->path);
        Storage::disk('public')->assertMissing('services/old-image.jpg');
    }

    public function test_admin_can_replace_a_banner_image(): void
    {
        Storage::fake('public');
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();
        $banner = Banner::where('placement', 'home')->firstOrFail();
        $banner->update(['image' => 'banners/old-banner.jpg']);
        Storage::disk('public')->put('banners/old-banner.jpg', 'old banner');

        $response = $this->actingAs($admin)->put('/admin/banners/'.$banner->id, [
            'title' => '',
            'subtitle' => '',
            'button_text' => '',
            'button_url' => $banner->button_url,
            'placement' => 'home',
            'sort_order' => $banner->sort_order,
            'is_active' => 1,
            'image' => $this->tinyPngUpload('updated-banner.png'),
        ]);

        $response->assertRedirect(route('admin.resources.index', 'banners'));
        $newPath = $banner->refresh()->image;
        $this->assertStringStartsWith('banners/', $newPath);
        $this->assertNotSame('banners/old-banner.jpg', $newPath);
        $this->assertSame('', $banner->title);
        $this->assertNull($banner->subtitle);
        $this->assertNull($banner->button_text);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing('banners/old-banner.jpg');
        $this->get('/')->assertOk()->assertSee('/storage/'.$newPath, false)->assertSee('image-only', false);
    }

    public function test_admin_can_keep_existing_banner_and_service_images_on_edit(): void
    {
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();
        $banner = Banner::where('placement', 'home')->firstOrFail();
        $banner->update(['image' => 'banners/existing-banner.png']);

        $this->actingAs($admin)->get('/admin/banners/'.$banner->id.'/edit')
            ->assertOk()
            ->assertSee('Current image')
            ->assertSee('banners/existing-banner.png')
            ->assertSee('Recommended 1600 x 700 px')
            ->assertSee('Leave blank to keep current image');

        $this->actingAs($admin)->put('/admin/banners/'.$banner->id, [
            'title' => 'Updated title only',
            'subtitle' => $banner->subtitle,
            'button_text' => $banner->button_text,
            'button_url' => $banner->button_url,
            'placement' => 'home',
            'sort_order' => $banner->sort_order,
            'is_active' => 1,
        ])->assertRedirect(route('admin.resources.index', 'banners'));

        $this->assertSame('banners/existing-banner.png', $banner->refresh()->image);

        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $this->actingAs($admin)->get('/admin/services/'.$service->id.'/edit')
            ->assertOk()
            ->assertSee('Current image')
            ->assertSee('Recommended 1200 x 600 px')
            ->assertSee('Leave blank to keep current image');
    }

    public function test_customer_can_checkout_cart_and_track_booking(): void
    {
        Mail::fake();
        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $city = City::where('slug', 'noida')->firstOrFail();
        $addon = Addon::where('slug', 'mascot')->firstOrFail();

        $this->actingAs($customer)->post('/cart', [
            'service_id' => $service->id,
            'city_id' => $city->id,
            'quantity' => 1,
            'addon_ids' => [$addon->id],
        ])->assertRedirect('/cart');

        $this->actingAs($customer)->get('/cart')->assertOk()->assertSee($addon->name)->assertSee('alt="'.$addon->name.'"', false);
        $this->actingAs($customer)->get('/checkout')->assertOk()->assertSee('Event details')->assertSee($addon->name);

        $response = $this->actingAs($customer)->post('/checkout', [
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'event_date' => today()->addDays(10)->format('Y-m-d'),
            'event_time' => '17:00',
            'city_id' => $city->id,
            'full_address' => 'Sector 62, Noida, Uttar Pradesh',
            'event_type' => 'Kids Birthday',
            'number_of_kids' => 18,
            'age_group' => '4-6 years',
            'venue_type' => 'Society',
            'payment_type' => 'advance',
        ]);

        $booking = Booking::latest()->firstOrFail();
        $response->assertRedirect(route('payments.checkout', $booking));
        $this->assertSame($city->id, $booking->city_id);
        $this->assertCount(1, $booking->items);
        $this->assertDatabaseHas('booking_items', ['booking_id' => $booking->id, 'service_id' => $service->id]);
        $this->assertDatabaseHas('booking_addons', ['booking_id' => $booking->id, 'addon_id' => $addon->id, 'name' => $addon->name]);

        $this->get('/track-booking?booking_no='.urlencode($booking->booking_no).'&mobile='.urlencode($booking->customer_phone))
            ->assertOk()
            ->assertSee($booking->booking_no)
            ->assertSee('Booking Placed');
    }

    public function test_payment_verification_sends_customer_confirmation_invoice_email(): void
    {
        Mail::fake();
        config(['services.razorpay.key' => 'rzp_test_key', 'services.razorpay.secret' => 'test_secret']);

        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $delhi = CityPaymentSetting::where('slug', 'delhi')->firstOrFail();

        $this->actingAs($customer)->post('/booking', [
            'service_id' => $service->id,
            'city_payment_setting_id' => $delhi->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'event_date' => today()->addWeek()->format('Y-m-d'),
            'event_time' => '16:00',
            'full_address' => 'South Delhi party venue',
            'number_of_kids' => 15,
            'payment_type' => 'advance',
        ]);

        $booking = Booking::latest()->firstOrFail();
        Payment::create([
            'booking_id' => $booking->id,
            'razorpay_order_id' => 'order_test_123',
            'amount' => $booking->payable_amount,
            'currency' => 'INR',
            'status' => 'created',
            'payment_status' => 'Pending',
            'method' => 'razorpay',
        ]);

        $signature = hash_hmac('sha256', 'order_test_123|pay_test_123', 'test_secret');

        $this->actingAs($customer)->postJson(route('payments.verify', $booking), [
            'razorpay_order_id' => 'order_test_123',
            'razorpay_payment_id' => 'pay_test_123',
            'razorpay_signature' => $signature,
        ])->assertOk()->assertJson(['redirect' => route('payments.success', $booking)]);

        $booking->refresh();
        $this->assertSame('Confirmed', $booking->workflow_status);
        $this->assertSame('Partially Paid', $booking->payment_status);
        $this->assertNotNull($booking->confirmation_emailed_at);
        Mail::assertSent(PaymentConfirmed::class, fn ($mail) => $mail->hasTo($customer->email) && $mail->booking->is($booking));
    }

    public function test_customer_can_update_profile_avatar_and_verify_new_email(): void
    {
        Storage::fake('public');
        Mail::fake();
        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $customer->update(['google_id' => 'google-user-123', 'avatar' => 'https://example.com/avatar.jpg']);

        $this->actingAs($customer)->put(route('dashboard.profile.update'), [
            'name' => 'Parent Updated',
            'email' => 'parent.updated@example.com',
            'phone' => '9999999999',
            'city' => 'Noida',
            'address' => 'Sector 62, Noida',
            'avatar' => $this->tinyPngUpload('avatar.png'),
        ])->assertRedirect(route('dashboard.profile'));

        $customer->refresh();
        $this->assertSame('parent@example.com', $customer->email);
        $this->assertSame('parent.updated@example.com', $customer->pending_email);
        $this->assertSame('9999999999', $customer->phone);
        Storage::disk('public')->assertExists($customer->avatar);

        $verificationUrl = null;
        Mail::assertSent(VerifyEmailChange::class, function (VerifyEmailChange $mail) use (&$verificationUrl) {
            $verificationUrl = $mail->verificationUrl;
            return $mail->hasTo('parent.updated@example.com');
        });

        $this->actingAs($customer)->get($verificationUrl)
            ->assertRedirect(route('dashboard.profile'));

        $customer->refresh();
        $this->assertSame('parent.updated@example.com', $customer->email);
        $this->assertNull($customer->pending_email);
        $this->assertNotNull($customer->email_verified_at);
    }

    public function test_vendor_can_register_and_access_pending_dashboard(): void
    {
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $city = City::where('slug', 'delhi')->firstOrFail();

        $this->post(route('vendors.register.store'), [
            'business_name' => 'Pune Party Partner',
            'contact_person' => 'Vendor Person',
            'email' => 'pune.vendor@example.com',
            'phone' => '7777770000',
            'city_id' => $city->id,
            'coverage_areas' => 'Dwarka, Rohini',
            'service_ids' => [$service->id],
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('vendor.dashboard'));

        $this->assertAuthenticated();
        $vendor = Vendor::where('email', 'pune.vendor@example.com')->firstOrFail();
        $this->assertSame('Pending', $vendor->status);
        $this->get(route('vendor.dashboard'))->assertOk()->assertSee('Approval pending');
    }

    public function test_admin_can_assign_booking_and_vendor_can_complete_withdrawal(): void
    {
        $admin = User::where('email', 'admin@kidspartyplanner.in')->firstOrFail();
        $vendor = Vendor::where('email', 'vendor@example.com')->firstOrFail();
        $customer = User::where('email', 'parent@example.com')->firstOrFail();
        $service = Service::where('slug', 'magic-show')->firstOrFail();
        $delhi = CityPaymentSetting::where('slug', 'delhi')->firstOrFail();

        $this->actingAs($customer)->post('/booking', [
            'service_id' => $service->id,
            'city_payment_setting_id' => $delhi->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'event_date' => today()->addWeek()->format('Y-m-d'),
            'event_time' => '16:00',
            'full_address' => 'South Delhi party venue',
            'number_of_kids' => 15,
            'payment_type' => 'advance',
        ]);

        $booking = Booking::latest()->firstOrFail();

        $this->actingAs($admin)->post(route('admin.resources.store', 'booking-assignments'), [
            'booking_id' => (string) $booking->id,
            'vendor_id' => (string) $vendor->id,
            'status' => 'Assigned',
            'assigned_amount' => 2000,
            'notes' => 'Manual assignment for Delhi job',
        ])->assertRedirect(route('admin.resources.index', 'booking-assignments'));

        $assignment = BookingAssignment::where('booking_id', $booking->id)->where('vendor_id', $vendor->id)->firstOrFail();
        $this->assertSame('400.00', $assignment->platform_commission);
        $this->assertSame('1600.00', $assignment->vendor_earning);
        $this->assertDatabaseHas('vendor_earnings', ['booking_assignment_id' => $assignment->id, 'status' => 'Pending']);

        $this->actingAs($vendor->user)->get(route('vendor.dashboard'))->assertOk()->assertSee($booking->booking_no);
        $this->actingAs($vendor->user)->post(route('vendor.assignments.accept', $assignment))->assertRedirect();
        $this->actingAs($vendor->user)->post(route('vendor.assignments.complete', $assignment))->assertRedirect();

        $earning = VendorEarning::where('booking_assignment_id', $assignment->id)->firstOrFail();
        $this->assertSame('Available', $earning->status);

        $this->actingAs($vendor->user)->post(route('vendor.withdrawals.store'), [
            'amount' => 500,
        ])->assertRedirect();

        $withdrawal = VendorWithdrawal::where('vendor_id', $vendor->id)->firstOrFail();
        $this->assertSame('Requested', $withdrawal->status);

        $this->actingAs($vendor->user)->from(route('vendor.dashboard'))->post(route('vendor.withdrawals.store'), [
            'amount' => 1200,
        ])->assertRedirect(route('vendor.dashboard'))->assertSessionHasErrors('amount');
    }

    private function tinyPngUpload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'kpp-upload-');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
