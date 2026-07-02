<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorRegistrationController extends Controller
{
    public function create()
    {
        return view('vendors.register', [
            'metaTitle' => 'Vendor Registration | Kids Party Planner',
            'cities' => City::orderBy('sort_order')->orderBy('name')->get(),
            'services' => Service::where('is_active', true)->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:160'],
            'contact_person' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'city' => ['required_without:city_id', 'nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:1000'],
            'coverage_areas' => ['nullable', 'string', 'max:1000'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => [Rule::exists('services', 'id')],
            'account_name' => ['nullable', 'string', 'max:160'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'ifsc' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $city = ! empty($validated['city_id']) ? City::find($validated['city_id']) : null;
        $user = User::create([
            'name' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'vendor',
            'city' => $city?->name ?: $validated['city'],
            'address' => $validated['address'] ?? null,
            'password' => $validated['password'],
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'city_id' => $city?->id,
            'business_name' => $validated['business_name'],
            'slug' => $this->uniqueSlug($validated['business_name']),
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'city' => $city?->name ?: $validated['city'],
            'state' => $city?->state ?: ($validated['state'] ?? null),
            'address' => $validated['address'] ?? null,
            'coverage_areas' => collect(explode(',', $validated['coverage_areas'] ?? ''))->map(fn ($area) => trim($area))->filter()->values()->all(),
            'bank_details' => [
                'account_name' => $validated['account_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'ifsc' => $validated['ifsc'] ?? null,
            ],
            'status' => 'Pending',
            'commission_percent' => 20,
        ]);

        $vendor->services()->sync($validated['service_ids']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('vendor.dashboard')->with('success', 'Vendor profile submitted. Admin approval is pending.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
