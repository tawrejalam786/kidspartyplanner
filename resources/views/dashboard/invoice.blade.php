<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $booking->invoice_no }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#45464b;margin:0;padding:40px;background:#f7faf9}.invoice{max-width:850px;margin:auto;background:#fff;padding:40px;border:1px solid #ddd}.head,.totals div{display:flex;justify-content:space-between;gap:20px}.brand{font-size:24px;font-weight:800;color:#ef303e}.meta{color:#666;line-height:1.8}table{width:100%;border-collapse:collapse;margin:30px 0}th,td{text-align:left;padding:12px;border-bottom:1px solid #ddd}th{text-transform:uppercase;font-size:11px;color:#666}.addon-image{width:46px;height:42px;object-fit:cover;border-radius:5px}.totals{margin-left:auto;max-width:360px}.totals div{padding:7px 0}.grand{font-size:20px;font-weight:800;border-top:2px solid #45464b;margin-top:8px;padding-top:14px!important}@media print{body{background:#fff;padding:0}.invoice{border:0}}
    </style>
</head>
<body>
<main class="invoice">
    <div class="head">
        <div><div class="brand">Kids Party Planner</div><div class="meta">TC-37, Pandav Nagar, Shadipur<br>New Delhi - 110008<br>+91 9910434330</div></div>
        <div><h1>INVOICE</h1><div class="meta">{{ $booking->invoice_no }}<br>{{ $booking->created_at->format('d M Y') }}<br>Booking: {{ $booking->booking_no }}</div></div>
    </div>
    <hr>
    <div class="head">
        <div><strong>Bill to</strong><div class="meta">{{ $booking->customer_name }}<br>{{ $booking->customer_phone }}<br>{{ $booking->customer_email }}</div></div>
        <div><strong>Event</strong><div class="meta">{{ $booking->event_date->format('d M Y') }} at {{ \Illuminate\Support\Str::of($booking->event_time)->substr(0,5) }}<br>{{ $booking->city?->name }}<br>{{ $booking->full_address ?: $booking->location }}</div></div>
    </div>
    <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
            @forelse($booking->items as $item)
                <tr><td>{{ $item->item_name }}</td><td>{{ $item->quantity }}</td><td>&#8377;{{ number_format((float)$item->unit_price,2) }}</td><td>&#8377;{{ number_format((float)$item->line_total,2) }}</td></tr>
            @empty
                <tr><td>{{ $booking->item_title }}</td><td>1</td><td>&#8377;{{ number_format((float)$booking->base_amount,2) }}</td><td>&#8377;{{ number_format((float)$booking->base_amount,2) }}</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($booking->bookingAddons->isNotEmpty())
        <h2>Selected add-ons</h2>
        <table><tbody>
            @foreach($booking->bookingAddons as $addon)
                <tr><td><img class="addon-image" src="{{ $addon->addon?->image_url ?? \App\Models\Addon::fallbackImageUrl() }}" alt="{{ $addon->name }}"></td><td>{{ $addon->name }}</td><td>Qty {{ $addon->quantity }}</td><td>&#8377;{{ number_format((float)$addon->price,2) }}</td></tr>
            @endforeach
        </tbody></table>
    @endif
    <div class="totals">
        <div><span>Subtotal</span><strong>&#8377;{{ number_format((float)$booking->base_amount,2) }}</strong></div>
        @if($booking->coupon_discount>0)<div><span>Discount</span><strong>-&#8377;{{ number_format((float)$booking->coupon_discount,2) }}</strong></div>@endif
        <div><span>Fees</span><strong>&#8377;{{ number_format((float)$booking->service_fee,2) }}</strong></div>
        <div><span>Tax</span><strong>&#8377;{{ number_format((float)$booking->tax_amount,2) }}</strong></div>
        <div class="grand"><span>Total</span><strong>&#8377;{{ number_format((float)$booking->total_amount,2) }}</strong></div>
        <div><span>Payment status</span><strong>{{ $booking->payment_status }}</strong></div>
    </div>
    <p class="meta">Thank you for choosing Kids Party Planner. This invoice records the services booked and payments received.</p>
</main>
</body>
</html>
