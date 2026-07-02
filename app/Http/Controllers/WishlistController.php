<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.wishlist', [
            'metaTitle' => 'My Wishlist | Kids Party Planner',
            'wishlists' => $request->user()->wishlists()->with('service.category', 'service.images')->latest()->paginate(12),
        ]);
    }

    public function store(Request $request, Service $service)
    {
        Wishlist::firstOrCreate(['user_id' => $request->user()->id, 'service_id' => $service->id]);
        return back()->with('success', 'Saved to your wishlist.');
    }

    public function destroy(Request $request, Service $service)
    {
        Wishlist::where('user_id', $request->user()->id)->where('service_id', $service->id)->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
