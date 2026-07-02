<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['service', 'package']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Received: '.$this->booking->booking_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-received',
        );
    }
}
