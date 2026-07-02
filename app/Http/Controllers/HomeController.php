<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Banner;
use App\Models\City;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Package as PartyPackage;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'metaTitle' => Setting::getValue('meta_title', 'Kids Party Planner'),
            'metaDescription' => Setting::getValue('meta_description', 'Book kids birthday party services in Delhi NCR.'),
            'categories' => Category::where('is_active', true)->withCount('services')->orderBy('sort_order')->take(7)->get(),
            'banners' => Banner::where('is_active', true)->where('placement', 'home')->orderBy('sort_order')->get(),
            'cities' => City::where('is_current', true)->where('is_active', true)->orderBy('sort_order')->get(),
            'featuredServices' => Service::where('is_active', true)->where('featured', true)->with(['category', 'images', 'cityPrices'])->orderBy('sort_order')->take(8)->get(),
            'categoryServices' => Category::whereIn('slug', ['kids-activities-games', 'birthday-decoration', 'anniversary-decoration', 'new-born-baby-decoration'])->with(['services' => fn ($query) => $query->where('is_active', true)->with(['category', 'images', 'cityPrices'])->orderByDesc('trending')->orderBy('sort_order')->take(4)])->get()->keyBy('slug'),
            'packages' => PartyPackage::where('is_active', true)->where('featured', true)->orderByDesc('trending')->take(4)->get(),
            'gallery' => Gallery::where('is_active', true)->orderBy('sort_order')->take(8)->get(),
            'reviews' => Review::where('is_approved', true)->latest()->take(6)->get(),
            'faqs' => Faq::where('is_active', true)->whereNull('service_id')->orderBy('sort_order')->take(6)->get()->map(fn ($faq) => ['question' => $faq->question, 'answer' => $faq->answer]),
        ]);
    }
}
