<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #111827; }
        .receipt { max-width: 640px; margin: 0 auto; border: 1px dashed #d1d5db; padding: 20px; }
        .header { text-align: center; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; letter-spacing: 1px; }
        .sub { font-size: 12px; color: #6b7280; }
        .divider { margin: 10px 0; border-top: 1px dotted #9ca3af; }
        .meta { font-size: 12px; display: flex; justify-content: space-between; margin-bottom: 8px; }
        .section-title { font-size: 12px; font-weight: 600; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 6px 0; }
        th { text-align: left; color: #6b7280; font-weight: 600; border-bottom: 1px dotted #d1d5db; }
        tfoot td { border-top: 1px dotted #d1d5db; }
        .right { text-align: right; }
        .total { font-size: 16px; font-weight: 700; }
        .footer { text-align: center; font-size: 12px; color: #6b7280; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="title">{{ config('app.name', 'Demo Resort') }}</div>
            <div class="sub">Official Receipt</div>
            <div class="sub">Address: Sample Street, Sample City • Tel: 000-0000</div>
        </div>
        <div class="divider"></div>
        <div class="meta">
            <div>OR No: OR-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div>Date: {{ $booking->created_at?->format('M d, Y') ?? now()->format('M d, Y') }}</div>
        </div>
        <div class="meta">
            <div>Guest: {{ $booking->guest_name }}</div>
            <div>Payment: {{ ucfirst($booking->payment_method?->value ?? 'cash') }}</div>
        </div>
        <div class="divider"></div>
        <div class="section-title">Details</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($booking->exclusiveResortRental)
                            {{ $booking->exclusiveResortRental->name }} (Exclusive Rental)
                        @elseif($booking->roomType)
                            {{ $booking->roomType->name }} (Room)
                        @else
                            Booking
                        @endif
                        • {{ $booking->check_in->format('M d, Y') }} – {{ $booking->check_out->format('M d, Y') }}
                        • {{ $booking->pax_count }} pax
                    </td>
                    <td class="right">₱{{ number_format($booking->total_price, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="right">Subtotal</td>
                    <td class="right">₱{{ number_format($booking->total_price, 2) }}</td>
                </tr>
                <tr>
                    <td class="right total">Total</td>
                    <td class="right total">₱{{ number_format($booking->total_price, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="divider"></div>
        <div class="meta">
            <div>Cashier: {{ $booking->user?->name ?? 'System' }}</div>
            <div>Reference: {{ $booking->id }}</div>
        </div>
        <div class="footer">Thank you!</div>
    </div>
</body>
</html>
