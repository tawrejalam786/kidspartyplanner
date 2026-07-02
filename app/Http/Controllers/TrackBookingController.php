<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class TrackBookingController extends Controller
{
    public function index(Request $request)
    {
        $booking = null;
        if ($request->filled(['booking_no', 'mobile'])) {
            $validated = $request->validate(['booking_no' => ['required', 'string', 'max:40'], 'mobile' => ['required', 'string', 'max:20']]);
            $booking = Booking::with(['city', 'items', 'latestPayment'])
                ->where('booking_no', $validated['booking_no'])
                ->where('customer_phone', $validated['mobile'])
                ->first();
        }

        return view('booking.track', ['metaTitle' => 'Track Booking | Kids Party Planner', 'booking' => $booking, 'searched' => $request->filled(['booking_no', 'mobile'])]);
    }
}
