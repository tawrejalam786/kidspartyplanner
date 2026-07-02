<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['nullable', 'exists:services,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        Review::create([...$validated, 'user_id' => auth()->id(), 'is_approved' => false]);

        return back()->with('success', 'Review submitted. It will appear after approval.');
    }
}
