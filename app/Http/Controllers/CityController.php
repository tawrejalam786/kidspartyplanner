<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function select(Request $request)
    {
        $validated = $request->validate(['city_id' => ['required', 'exists:cities,id']]);
        $city = City::where('is_active', true)->findOrFail($validated['city_id']);
        $request->session()->put('city_slug', $city->slug);

        if ($request->expectsJson()) {
            return response()->json(['city' => $city->name, 'redirect' => $request->input('redirect', url()->previous())]);
        }

        return back()->with('success', 'Showing services and prices for '.$city->name.'.');
    }
}
