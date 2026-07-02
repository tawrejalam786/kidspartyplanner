<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['items.addons.addon', 'bookingAddons.addon', 'service', 'package', 'city', 'latestPayment']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed: '.$this->booking->booking_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmed',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => view('dashboard.invoice', ['booking' => $this->booking])->render(),
                ($this->booking->invoice_no ?: $this->booking->booking_no).'.html'
            )->withMime('text/html'),
        ];
    }
}
