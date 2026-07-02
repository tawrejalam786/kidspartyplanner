<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function categories()
    {
        return view('categories.index', [
            'metaTitle' => 'Party Categories | Kids Party Planner',
            'metaDescription' => 'Browse kids activities, birthday decorations, anniversary setups, welcome baby decor and party packages.',
            'categories' => Category::where('is_active', true)->with(['subcategories' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])->withCount('services')->orderBy('sort_order')->get(),
        ]);
    }

    public function index(Request $request)
    {
        $selectedCity = $this->selectedCity($request);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $subcategories = Subcategory::where('is_active', true)->orderBy('sort_order')->get();
        $services = $this->filteredServices($request, $selectedCity)->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('services._cards', compact('services', 'selectedCity'))->render(),
                'count' => $services->total(),
            ]);
        }

        return view('services.index', [
            'metaTitle' => 'Party Services in '.($selectedCity?->name ?? 'Delhi NCR'),
            'metaDescription' => 'Browse and book kids party activities and decorations with city-wise pricing.',
            'categories' => $categories,
            'subcategories' => $subcategories,
            'services' => $services,
            'selectedCity' => $selectedCity,
        ]);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);
        $request->merge(['category' => $category->id]);
        return $this->index($request)->with('categoryPage', $category);
    }

    public function subcategory(Request $request, Subcategory $subcategory)
    {
        abort_unless($subcategory->is_active, 404);
        $request->merge(['subcategory' => $subcategory->id, 'category' => $subcategory->category_id]);
        return $this->index($request)->with('subcategoryPage', $subcategory);
    }

    public function cityCategory(Request $request, City $city, Category $category)
    {
        abort_unless($city->is_active && $category->is_active, 404);
        $request->session()->put('city_slug', $city->slug);
        $request->merge(['city' => $city->slug, 'category' => $category->id]);
        return $this->index($request)->with('categoryPage', $category);
    }

    public function show(Request $request, Service $service)
    {
        abort_unless($service->is_active, 404);
        $selectedCity = $this->selectedCity($request);
        $service->load(['category', 'subcategory', 'images', 'reviews', 'cityPrices.city', 'addons', 'faqs' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')]);
        $serviceFaqs = $service->faqs->map(fn ($faq) => [
            'id' => 'managed-'.$faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer,
        ]);
        if ($serviceFaqs->isEmpty()) {
            $serviceFaqs = collect($service->faq ?? [])->map(fn ($faq, $index) => [
                'id' => 'legacy-'.$index,
                'question' => $faq['question'] ?? 'Service question',
                'answer' => $faq['answer'] ?? '',
            ]);
        }

        return view('services.show', [
            'metaTitle' => $service->meta_title ?: $service->title.' | Kids Party Planner',
            'metaDescription' => $service->meta_description ?: $service->short_description,
            'metaKeywords' => $service->meta_keywords,
            'ogImage' => $service->og_image ?: $service->image_url,
            'service' => $service,
            'selectedCity' => $selectedCity,
            'displayPrice' => $service->priceForCity($selectedCity),
            'serviceFaqs' => $serviceFaqs,
            'relatedServices' => Service::where('is_active', true)->where('category_id', $service->category_id)->whereKeyNot($service->id)->with(['category', 'images', 'cityPrices'])->orderBy('sort_order')->take(4)->get(),
        ]);
    }

    private function filteredServices(Request $request, ?City $city)
    {
        $query = Service::query()->where('is_active', true)->with(['category', 'subcategory', 'images', 'cityPrices']);

        if ($city) {
            $query->where(fn ($service) => $service
                ->whereDoesntHave('cityPrices')
                ->orWhereHas('cityPrices', fn ($price) => $price->where('city_id', $city->id)->where('is_available', true)));
        }
        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('short_description', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        }
        if ($request->filled('category')) $query->where('category_id', $request->integer('category'));
        if ($request->filled('subcategory')) $query->where('subcategory_id', $request->integer('subcategory'));
        if ($request->filled('max_price')) {
            $max = $request->integer('max_price');
            $city
                ? $query->where(fn ($service) => $service
                    ->whereHas('cityPrices', fn ($price) => $price->where('city_id', $city->id)->where(fn ($q) => $q->where('sale_price', '<=', $max)->orWhere(fn ($inner) => $inner->whereNull('sale_price')->where('price', '<=', $max))))
                    ->orWhere(fn ($fallback) => $fallback->whereDoesntHave('cityPrices')->where(fn ($q) => $q->where('discount_price', '<=', $max)->orWhere(fn ($inner) => $inner->whereNull('discount_price')->where('price', '<=', $max)))))
                : $query->where(fn ($q) => $q->where('discount_price', '<=', $max)->orWhere(fn ($inner) => $inner->whereNull('discount_price')->where('price', '<=', $max)));
        }
        if ($request->filled('rating')) $query->where('rating', '>=', $request->float('rating'));

        return match ($request->get('sort')) {
            'price_low' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'price_high' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            'rating' => $query->orderByDesc('rating'),
            'trending' => $query->orderByDesc('trending')->orderBy('sort_order'),
            default => $query->orderByDesc('featured')->orderBy('sort_order'),
        };
    }

    private function selectedCity(Request $request): ?City
    {
        $slug = $request->get('city', $request->session()->get('city_slug'));
        $city = $slug ? City::where('slug', $slug)->where('is_active', true)->first() : null;
        $city ??= City::where('is_current', true)->where('is_active', true)->orderBy('sort_order')->first();
        if ($city) $request->session()->put('city_slug', $city->slug);
        return $city;
    }
}
