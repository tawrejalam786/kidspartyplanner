<?php

namespace App\Services;

use App\Mail\PaymentConfirmed;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationMailer
{
    public function send(Booking $booking): void
    {
        if ($booking->confirmation_emailed_at || ! $booking->customer_email) {
            return;
        }

        $booking->loadMissing(['items.addons.addon', 'bookingAddons.addon', 'service', 'package', 'city', 'latestPayment']);

        Mail::to($booking->customer_email)->send(new PaymentConfirmed($booking));

        $booking->forceFill(['confirmation_emailed_at' => now()])->save();
    }
}
