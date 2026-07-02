<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Package as PartyPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = City::where('slug', $request->session()->get('city_slug'))->where('is_active', true)->first() ?? City::where('is_current', true)->where('is_active', true)->orderBy('sort_order')->first();

        return view('packages.index', [
            'metaTitle' => 'Kids Birthday Party Packages in Delhi NCR',
            'metaDescription' => 'Book curated birthday party packages for kids in Delhi, Noida, and Gurgaon.',
            'packages' => PartyPackage::where('is_active', true)->when($selectedCity, fn ($query) => $query->whereHas('cities', fn ($city) => $city->whereKey($selectedCity->id)))->with('cities')->orderByDesc('trending')->latest()->paginate(9),
            'selectedCity' => $selectedCity,
        ]);
    }

    public function show(Request $request, PartyPackage $package)
    {
        abort_unless($package->is_active, 404);

        $selectedCity = City::where('slug', $request->session()->get('city_slug'))->where('is_active', true)->first();
        $package->load(['includedServices.images', 'cities']);

        return view('packages.show', [
            'metaTitle' => $package->meta_title ?: $package->title.' | Kids Party Planner',
            'metaDescription' => $package->meta_description ?: $package->description,
            'package' => $package,
            'selectedCity' => $selectedCity,
            'relatedPackages' => PartyPackage::where('is_active', true)->whereKeyNot($package->id)->take(3)->get(),
        ]);
    }
}
