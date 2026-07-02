<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TrackBookingController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\VendorRegistrationController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::post('/city/select', [CityController::class, 'select'])->name('city.select');
Route::get('/categories', [ServiceController::class, 'categories'])->name('categories.index');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/filter', [ServiceController::class, 'index'])->name('services.filter');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/categories/{category:slug}', [ServiceController::class, 'category'])->name('categories.show');
Route::get('/subcategories/{subcategory:slug}', [ServiceController::class, 'subcategory'])->name('subcategories.show');
Route::get('/{city:slug}/{category:slug}', [ServiceController::class, 'cityCategory'])
    ->whereIn('city', ['delhi', 'noida', 'gurgaon', 'mumbai', 'pune', 'jaipur'])
    ->withoutScopedBindings()
    ->name('city.categories.show');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package:slug}', [PackageController::class, 'show'])->name('packages.show');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/reviews', [PageController::class, 'reviews'])->name('reviews');
Route::post('/reviews', [ReviewController::class, 'store'])->middleware('auth')->name('reviews.store');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::post('/contact', [EnquiryController::class, 'store'])->name('enquiries.store');
Route::get('/blog', [PageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{blog:slug}', [PageController::class, 'blogShow'])->name('blog.show');
Route::get('/terms', [PageController::class, 'policy'])->defaults('slug', 'terms')->name('terms');
Route::get('/privacy-policy', [PageController::class, 'policy'])->defaults('slug', 'privacy-policy')->name('privacy');
Route::get('/refund-policy', [PageController::class, 'policy'])->defaults('slug', 'refund-policy')->name('refund');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/track-booking', [TrackBookingController::class, 'index'])->name('booking.track');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/vendors/register', [VendorRegistrationController::class, 'create'])->name('vendors.register');
Route::post('/vendors/register', [VendorRegistrationController::class, 'store'])->name('vendors.register.store');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/razorpay/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/payment/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payment/{booking}/order', [PaymentController::class, 'createOrder'])->name('payments.order');
    Route::post('/payment/{booking}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::get('/booking-success/{booking}', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payment-failed/{booking}', [PaymentController::class, 'failed'])->name('payments.failed');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/bookings/{booking}', [DashboardController::class, 'booking'])->name('dashboard.booking');
    Route::post('/dashboard/bookings/{booking}/cancel', [DashboardController::class, 'cancel'])->name('dashboard.booking.cancel');
    Route::get('/dashboard/bookings/{booking}/rebook', [DashboardController::class, 'rebook'])->name('dashboard.booking.rebook');
    Route::get('/dashboard/bookings/{booking}/invoice', [DashboardController::class, 'invoice'])->name('dashboard.booking.invoice');
    Route::get('/dashboard/payments', [DashboardController::class, 'payments'])->name('dashboard.payments');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/dashboard/profile/email/verify/{token}', [DashboardController::class, 'verifyProfileEmail'])->middleware('signed')->name('dashboard.profile.email.verify');
    Route::post('/dashboard/profile/email/resend', [DashboardController::class, 'resendProfileEmailVerification'])->name('dashboard.profile.email.resend');
    Route::get('/dashboard/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{service:slug}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{service:slug}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'role:vendor'])
    ->group(function () {
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
        Route::post('/assignments/{assignment}/accept', [VendorDashboardController::class, 'accept'])->name('assignments.accept');
        Route::post('/assignments/{assignment}/complete', [VendorDashboardController::class, 'complete'])->name('assignments.complete');
        Route::post('/withdrawals', [VendorDashboardController::class, 'requestWithdrawal'])->name('withdrawals.store');
    });

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.store');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/{resource}', [ResourceController::class, 'index'])->where('resource', ResourceController::resources())->name('resources.index');
        Route::get('/{resource}/create', [ResourceController::class, 'create'])->where('resource', ResourceController::resources())->name('resources.create');
        Route::post('/{resource}', [ResourceController::class, 'store'])->where('resource', ResourceController::resources())->name('resources.store');
        Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->where('resource', ResourceController::resources())->name('resources.edit');
        Route::put('/{resource}/{id}', [ResourceController::class, 'update'])->where('resource', ResourceController::resources())->name('resources.update');
        Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->where('resource', ResourceController::resources())->name('resources.destroy');
    });
