<?php

namespace App\Providers;

use App\Models\CityPaymentSetting;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Category;
use App\Models\City;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            static $marketplaceContext;

            if ($marketplaceContext === null) {
                $activeCities = Schema::hasTable('cities')
                    ? City::where('is_active', true)->with(['areas' => fn ($query) => $query->where('is_active', true)->orderBy('name')])->orderBy('sort_order')->get()
                    : collect();
                $futureCities = Schema::hasTable('cities')
                    ? City::where('is_current', false)->orderBy('sort_order')->get()
                    : collect();
                $selectedCity = $activeCities->firstWhere('slug', session('city_slug')) ?: $activeCities->firstWhere('is_current', true) ?: $activeCities->first();
                $navCategories = Schema::hasTable('categories')
                    ? Category::where('is_active', true)
                        ->with(['subcategories' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                        ->orderBy('sort_order')
                        ->get()
                    : collect();
                $cartCount = 0;
                $recentBookingActivity = collect();

                if (Schema::hasTable('carts')) {
                    $cart = auth()->check()
                        ? Cart::where('user_id', auth()->id())->latest()->first()
                        : Cart::where('session_token', session()->getId())->latest()->first();
                    $cartCount = $cart?->items()->sum('quantity') ?? 0;
                }

                if (Schema::hasTable('bookings')) {
                    $recentBookingActivity = Booking::whereIn('payment_status', ['Paid', 'Partially Paid'])
                        ->with(['service', 'package', 'items', 'city', 'cityPaymentSetting'])
                        ->latest('updated_at')
                        ->take(8)
                        ->get();
                }

                $marketplaceContext = compact('activeCities', 'futureCities', 'selectedCity', 'navCategories', 'cartCount', 'recentBookingActivity');
            }

            $view->with($marketplaceContext);
        });

        View::composer('partials.booking-form', function ($view) {
            $cities = Schema::hasTable('city_payment_settings')
                ? CityPaymentSetting::where('is_active', true)->orderByDesc('is_default')->orderBy('city')->get()
                : collect();

            $view->with('cities', $cities);
        });
    }
}
