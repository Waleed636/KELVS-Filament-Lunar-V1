<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Has Shipped</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #fafafa;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #eaeaea;
        }
        .header {
            background-color: #0d0d0d;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            margin: 0;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting h2 {
            font-size: 22px;
            font-weight: 600;
            margin-top: 0;
            color: #0d0d0d;
        }
        .greeting p {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
            margin-bottom: 25px;
        }
        .tracking-card {
            background: #f8fafc;
            border: 2px dashed #0d0d0d;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            margin-bottom: 30px;
        }
        .tracking-card .label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .tracking-card .tracking-number {
            font-size: 22px;
            font-weight: 700;
            color: #0d0d0d;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-family: monospace;
        }
        .tracking-card .courier-tag {
            display: inline-block;
            background: #e2e8f0;
            color: #1e293b;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .btn-track {
            display: inline-block;
            background-color: #0d0d0d;
            color: #ffffff !important;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .order-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fcfcfc;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
        }
        .order-meta-table td {
            padding: 12px 18px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-meta-table tr:last-child td {
            border-bottom: none;
        }
        .details-heading {
            font-size: 17px;
            font-weight: 600;
            color: #0d0d0d;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888888;
            border-bottom: 1px solid #eaeaea;
        }
        .items-table td {
            padding: 12px 10px;
            font-size: 14px;
            border-bottom: 1px solid #f5f5f5;
            vertical-align: middle;
        }
        .item-name {
            font-weight: 500;
            color: #0d0d0d;
        }
        .item-sku {
            font-size: 12px;
            color: #888888;
            margin-top: 3px;
        }
        .footer {
            background-color: #f7f7f7;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #eaeaea;
        }
        .footer p {
            font-size: 13px;
            color: #888888;
            margin: 0 0 8px 0;
            line-height: 1.5;
        }
        .footer a {
            color: #0d0d0d;
            text-decoration: none;
            font-weight: 500;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0 auto;
                border-radius: 0;
                border: none;
            }
            .content {
                padding: 25px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>KELVS</h1>
        </div>
        <div class="content">
            <div class="greeting">
                <h2>Your Order is on the Way! 📦</h2>
                <p>Hello {{ $order->shippingAddress?->first_name ?? 'Valued Customer' }}, great news! Your order <strong>#{{ $order->reference }}</strong> has been dispatched and is currently in transit with our logistics partner.</p>
            </div>

            <!-- Tracking Card -->
            <div class="tracking-card">
                <div class="label">Courier Partner</div>
                <div class="courier-tag">{{ $carrier }}</div>
                <div class="label">Tracking Number</div>
                <div class="tracking-number">{{ $trackingNumber }}</div>
                <div style="margin-top: 15px;">
                    <a href="{{ $trackingUrl }}" target="_blank" class="btn-track">Track Your Package</a>
                </div>
            </div>

            <table class="order-meta-table">
                <tr>
                    <td><strong>Order Reference:</strong></td>
                    <td style="text-align: right;">{{ $order->reference }}</td>
                </tr>
                <tr>
                    <td><strong>Delivery Address:</strong></td>
                    <td style="text-align: right;">
                        {{ $order->shippingAddress?->line_one }}, {{ $order->shippingAddress?->city }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Contact Phone:</strong></td>
                    <td style="text-align: right;">{{ $order->shippingAddress?->contact_phone ?? 'N/A' }}</td>
                </tr>
            </table>

            <div class="details-heading">Items in Shipment</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Item</th>
                        <th style="text-align: center; width: 30%;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->lines as $line)
                        @if($line->type !== 'shipping')
                            <tr>
                                <td>
                                    <div class="item-name">{{ $line->description }}</div>
                                    <div class="item-sku">SKU: {{ $line->identifier }}</div>
                                </td>
                                <td style="text-align: center; font-weight: 600;">{{ $line->quantity }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <p style="font-size: 14px; color: #64748b; line-height: 1.5; margin-top: 20px;">
                💡 <em>Tip: Couriers typically deliver within 2–4 business days. Please ensure someone is available at the delivery address to receive the parcel and complete payment if your order is Cash on Delivery.</em>
            </p>
        </div>

        <div class="footer">
            <p>Need help with your shipment? Contact our concierge team at <a href="mailto:contact@kelvsint.com">contact@kelvsint.com</a></p>
            <p>&copy; {{ date('Y') }} KELVS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
