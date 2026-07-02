<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\City;
use App\Models\Package as PartyPackage;
use App\Models\Service;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->cart($request, false)?->load(['city', 'items.service.images', 'items.package']);

        return view('cart.index', [
            'metaTitle' => 'Your Party Cart | Kids Party Planner',
            'metaDescription' => 'Review selected kids party services and packages before checkout.',
            'cart' => $cart,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['integer', 'exists:addons,id'],
        ]);

        abort_if(empty($validated['service_id']) && empty($validated['package_id']), 422, 'Select a service or package.');

        $city = ! empty($validated['city_id'])
            ? City::where('is_active', true)->findOrFail($validated['city_id'])
            : City::where('slug', $request->session()->get('city_slug'))->first() ?? City::where('is_current', true)->where('is_active', true)->orderBy('sort_order')->first();

        $service = ! empty($validated['service_id']) ? Service::where('is_active', true)->with(['cityPrices', 'addons'])->findOrFail($validated['service_id']) : null;
        $package = ! empty($validated['package_id']) ? PartyPackage::where('is_active', true)->findOrFail($validated['package_id']) : null;
        $unitPrice = $service ? $service->priceForCity($city) : $package->effective_price;
        $addons = $service
            ? $service->addons->whereIn('id', $validated['addon_ids'] ?? [])->map(fn ($addon) => ['id' => $addon->id, 'name' => $addon->name, 'price' => (float) ($addon->pivot->price_override ?: $addon->price), 'image' => $addon->image_url, 'quantity' => 1])->values()->all()
            : [];

        $cart = $this->cart($request, true);
        $cart->update(['city_id' => $city?->id]);
        $item = $cart->items()->where('service_id', $service?->id)->where('package_id', $package?->id)->first();

        if ($item) {
            $item->update(['quantity' => min(10, $item->quantity + ($validated['quantity'] ?? 1)), 'unit_price' => $unitPrice, 'selected_addons' => $addons ?: $item->selected_addons]);
        } else {
            $cart->items()->create(['service_id' => $service?->id, 'package_id' => $package?->id, 'quantity' => $validated['quantity'] ?? 1, 'unit_price' => $unitPrice, 'selected_addons' => $addons]);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Added to cart.', 'count' => $cart->items()->sum('quantity')])
            : redirect()->route('cart.index')->with('success', 'Added to your party cart.');
    }

    public function update(Request $request, int $item)
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:10']]);
        $cart = $this->cart($request, false);
        abort_unless($cart, 404);
        $cart->items()->findOrFail($item)->update($validated);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, int $item)
    {
        $cart = $this->cart($request, false);
        abort_unless($cart, 404);
        $cart->items()->findOrFail($item)->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    private function cart(Request $request, bool $create): ?Cart
    {
        $attributes = $request->user()
            ? ['user_id' => $request->user()->id]
            : ['session_token' => $request->session()->getId()];

        return $create ? Cart::firstOrCreate($attributes) : Cart::where($attributes)->latest()->first();
    }
}
