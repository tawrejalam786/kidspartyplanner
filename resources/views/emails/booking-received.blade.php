<h2>New booking received</h2>
<p><strong>Booking:</strong> {{ $booking->booking_no }}</p>
<p><strong>Customer:</strong> {{ $booking->customer_name }} ({{ $booking->customer_phone }})</p>
<p><strong>Email:</strong> {{ $booking->customer_email }}</p>
<p><strong>Item:</strong> {{ $booking->item_title }}</p>
<p><strong>Date:</strong> {{ $booking->event_date->format('d M Y') }} at {{ \Illuminate\Support\Str::of($booking->event_time)->substr(0, 5) }}</p>
<p><strong>Address:</strong> {{ $booking->full_address ?: $booking->location }}</p>
<p><strong>Kids:</strong> {{ $booking->number_of_kids }}</p>
<p><strong>Total:</strong> Rs. {{ number_format((float) $booking->total_amount) }}</p>
<p><strong>Payable:</strong> Rs. {{ number_format((float) $booking->payable_amount) }}</p>
@if($booking->message)
    <p><strong>Message:</strong> {{ $booking->message }}</p>
@endif
