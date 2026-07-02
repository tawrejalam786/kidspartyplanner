<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\CityPaymentSetting;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'Bookings' => Booking::count(),
                'Today Bookings' => Booking::whereDate('created_at', today())->count(),
                'Upcoming Bookings' => Booking::whereDate('event_date', '>=', today())->whereNotIn('workflow_status', ['Cancelled', 'Completed', 'Refunded'])->count(),
                'Confirmed Bookings' => Booking::where('workflow_status', 'Confirmed')->count(),
                'Cancelled Bookings' => Booking::where('workflow_status', 'Cancelled')->count(),
                'Pending Payments' => Booking::where('payment_status', 'Pending')->count(),
                'Customers' => User::where('role', 'customer')->count(),
                'Vendors' => Vendor::count(),
                'Total Revenue' => Payment::where('payment_status', 'Paid')->sum('amount'),
            ],
            'statusCounts' => collect(['New', 'Confirmed', 'Completed', 'Cancelled'])->mapWithKeys(fn ($status) => [$status => Booking::where('workflow_status', $status)->count()]),
            'cityStats' => CityPaymentSetting::where('is_active', true)->withCount('bookings')->orderByDesc('bookings_count')->get(),
            'topServices' => Service::withCount('bookings')->orderByDesc('bookings_count')->take(5)->get(),
            'bookings' => Booking::with(['service', 'package', 'items', 'city', 'cityPaymentSetting', 'latestPayment'])->latest()->take(8)->get(),
            'todayEvents' => Booking::with(['service', 'package', 'items', 'city', 'cityPaymentSetting'])->whereDate('event_date', today())->whereNotIn('workflow_status', ['Cancelled', 'Completed', 'Refunded'])->orderBy('event_time')->get(),
            'upcomingEvents' => Booking::with(['service', 'package', 'items', 'city', 'cityPaymentSetting'])->whereBetween('event_date', [today(), today()->addDays(7)])->whereNotIn('workflow_status', ['Cancelled', 'Completed', 'Refunded'])->orderBy('event_date')->orderBy('event_time')->take(8)->get(),
            'enquiries' => Enquiry::latest()->take(6)->get(),
        ]);
    }
}
