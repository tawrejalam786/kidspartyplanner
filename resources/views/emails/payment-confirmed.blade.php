<h2>Your booking is confirmed</h2>
<p>Hi {{ $booking->customer_name }},</p>
<p>Payment has been received for booking <strong>{{ $booking->booking_no }}</strong>.</p>
<p><strong>Experience:</strong> {{ $booking->item_title }}</p>
<p><strong>Event:</strong> {{ $booking->event_date->format('d M Y') }} at {{ \Illuminate\Support\Str::of($booking->event_time)->substr(0, 5) }}</p>
<p><strong>Address:</strong> {{ $booking->full_address ?: $booking->location }}</p>
<p><strong>Paid now:</strong> Rs. {{ number_format((float) $booking->latestPayment?->amount) }}</p>
<p><strong>Payment status:</strong> {{ $booking->payment_status }}</p>
<p>Your invoice is attached. Our team will contact you before the event date for final coordination.</p>
