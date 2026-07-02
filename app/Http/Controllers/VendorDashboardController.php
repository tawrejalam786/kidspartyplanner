<?php

namespace App\Http\Controllers;

use App\Models\BookingAssignment;
use App\Models\Vendor;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $vendor = $this->vendor($request)->load(['services', 'assignments.booking.city', 'earnings.booking', 'withdrawals']);
        $availableBalance = $this->availableBalance($vendor);

        return view('vendor.dashboard', [
            'metaTitle' => 'Vendor Dashboard | Kids Party Planner',
            'vendor' => $vendor,
            'assignments' => $vendor->assignments()->with(['booking.city', 'booking.items'])->latest()->paginate(10),
            'earnings' => $vendor->earnings()->with('booking')->latest()->take(8)->get(),
            'withdrawals' => $vendor->withdrawals()->latest()->take(8)->get(),
            'stats' => [
                'assigned' => $vendor->assignments()->count(),
                'active' => $vendor->assignments()->whereIn('status', ['Assigned', 'Accepted', 'In Progress'])->count(),
                'available' => $availableBalance,
                'pending' => $vendor->earnings()->where('status', 'Pending')->sum('net_amount'),
            ],
        ]);
    }

    public function accept(Request $request, BookingAssignment $assignment)
    {
        $vendor = $this->vendor($request);
        abort_unless($assignment->vendor_id === $vendor->id, 403);
        abort_unless($assignment->status === 'Assigned', 422, 'This job cannot be accepted now.');

        $assignment->update([
            'status' => 'Accepted',
            'accepted_at' => now(),
        ]);

        $assignment->booking->update([
            'workflow_status' => 'Assigned',
            'tracking_status' => 'Team Assigned',
        ]);

        return back()->with('success', 'Job accepted.');
    }

    public function complete(Request $request, BookingAssignment $assignment)
    {
        $vendor = $this->vendor($request);
        abort_unless($assignment->vendor_id === $vendor->id, 403);
        abort_unless(in_array($assignment->status, ['Assigned', 'Accepted', 'In Progress'], true), 422, 'This job cannot be completed now.');

        $assignment->update([
            'status' => 'Completed',
            'completed_at' => now(),
        ]);

        $assignment->earning?->update([
            'status' => 'Available',
            'available_at' => now(),
        ]);

        $assignment->booking->update([
            'status' => 'Completed',
            'workflow_status' => 'Completed',
            'tracking_status' => 'Completed',
        ]);

        return back()->with('success', 'Job marked completed. Earning is now available for withdrawal.');
    }

    public function requestWithdrawal(Request $request)
    {
        $vendor = $this->vendor($request);
        $available = $this->availableBalance($vendor);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:'.$available],
        ]);

        VendorWithdrawal::create([
            'vendor_id' => $vendor->id,
            'amount' => $validated['amount'],
            'bank_details' => $vendor->bank_details,
            'status' => 'Requested',
        ]);

        return back()->with('success', 'Withdrawal request submitted for admin review.');
    }

    private function vendor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor, 403);

        return $vendor;
    }

    private function availableBalance(Vendor $vendor): float
    {
        $available = (float) $vendor->earnings()->where('status', 'Available')->sum('net_amount');
        $reserved = (float) $vendor->withdrawals()
            ->whereIn('status', ['Requested', 'Approved'])
            ->sum('amount');

        return max(0, $available - $reserved);
    }
}
