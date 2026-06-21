<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
            margin-bottom: 30px;
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
            padding: 15px 20px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-meta-table tr:last-child td {
            border-bottom: none;
        }
        .order-meta-table td strong {
            color: #0d0d0d;
        }
        .details-heading {
            font-size: 18px;
            font-weight: 600;
            color: #0d0d0d;
            margin-top: 40px;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            padding: 12px 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888888;
            border-bottom: 1px solid #eaeaea;
        }
        .items-table td {
            padding: 15px 10px;
            font-size: 15px;
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
            margin-top: 4px;
        }
        .totals-section {
            width: 100%;
            margin-top: 20px;
            border-top: 2px solid #eaeaea;
            padding-top: 20px;
        }
        .totals-table {
            width: 250px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            font-size: 14px;
            color: #555555;
        }
        .totals-table td.amount {
            text-align: right;
            font-weight: 500;
            color: #0d0d0d;
        }
        .totals-table tr.grand-total td {
            font-size: 18px;
            font-weight: 700;
            color: #0d0d0d;
            border-top: 1px solid #eaeaea;
            padding-top: 15px;
        }
        .totals-table tr.grand-total td.amount {
            color: #0d0d0d;
        }
        .addresses-container {
            margin-top: 40px;
            margin-bottom: 30px;
        }
        .address-box {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        .address-box h3 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888888;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .address-box p {
            font-size: 14px;
            line-height: 1.5;
            color: #555555;
            margin: 0;
        }
        .footer {
            background-color: #f7f7f7;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #eaeaea;
        }
        .footer p {
            font-size: 13px;
            color: #888888;
            margin: 0 0 10px 0;
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
                padding: 30px 20px;
            }
            .address-box {
                display: block;
                width: 100%;
                margin-bottom: 25px;
            }
            .totals-table {
                width: 100%;
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
                <h2>Thank you for your order!</h2>
                <p>Hello {{ $order->shippingAddress?->first_name ?? 'Valued Customer' }}, we are pleased to confirm that we have received your order. We are preparing it for shipment and will notify you when it's on its way.</p>
            </div>

            <table class="order-meta-table">
                <tr>
                    <td><strong>Order Reference:</strong></td>
                    <td style="text-align: right;">{{ $order->reference }}</td>
                </tr>
                <tr>
                    <td><strong>Date Placed:</strong></td>
                    <td style="text-align: right;">{{ $order->placed_at ? $order->placed_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td style="text-align: right;">{{ $order->transactions->first()?->card_type === 'cash-on-delivery' ? 'Cash on Delivery' : 'Credit Card' }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td style="text-align: right;"><span style="background-color: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; text-transform: uppercase;">{{ str_replace('-', ' ', $order->status) }}</span></td>
                </tr>
            </table>

            <div class="details-heading">Order Summary</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Item Description</th>
                        <th style="text-align: center; width: 15%;">Qty</th>
                        <th style="text-align: right; width: 15%;">Price</th>
                        <th style="text-align: right; width: 20%;">Total</th>
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
                                <td style="text-align: center;">{{ $line->quantity }}</td>
                                <td style="text-align: right;">{{ $line->unit_price->formatted() }}</td>
                                <td style="text-align: right; font-weight: 500;">{{ $line->sub_total->formatted() }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="amount">{{ $order->sub_total->formatted() }}</td>
                    </tr>
                    @if($order->shipping_total->value > 0)
                        <tr>
                            <td>Shipping</td>
                            <td class="amount">{{ $order->shipping_total->formatted() }}</td>
                        </tr>
                    @endif
                    @if($order->tax_total->value > 0)
                        <tr>
                            <td>Tax</td>
                            <td class="amount">{{ $order->tax_total->formatted() }}</td>
                        </tr>
                    @endif
                    @if($order->discount_total->value > 0)
                        <tr>
                            <td>Discount</td>
                            <td class="amount" style="color: #c5221f;">-{{ $order->discount_total->formatted() }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td>Total</td>
                        <td class="amount">{{ $order->total->formatted() }}</td>
                    </tr>
                </table>
            </div>

            <div style="clear: both; height: 10px;"></div>

            <div class="addresses-container">
                @if($order->shippingAddress)
                    <div class="address-box" style="margin-right: 4%;">
                        <h3>Shipping Address</h3>
                        <p>
                            <strong>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</strong><br>
                            {{ $order->shippingAddress->line_one }}<br>
                            @if($order->shippingAddress->line_two)
                                {{ $order->shippingAddress->line_two }}<br>
                            @endif
                            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postcode }}<br>
                            {{ $order->shippingAddress->country?->name }}<br>
                            @if($order->shippingAddress->contact_phone)
                                Phone: {{ $order->shippingAddress->contact_phone }}
                            @endif
                        </p>
                    </div>
                @endif
                @if($order->billingAddress && (!$order->shippingAddress || $order->billingAddress->id !== $order->shippingAddress->id))
                    <div class="address-box">
                        <h3>Billing Address</h3>
                        <p>
                            <strong>{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</strong><br>
                            {{ $order->billingAddress->line_one }}<br>
                            @if($order->billingAddress->line_two)
                                {{ $order->billingAddress->line_two }}<br>
                            @endif
                            {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postcode }}<br>
                            {{ $order->billingAddress->country?->name }}<br>
                            @if($order->billingAddress->contact_phone)
                                Phone: {{ $order->billingAddress->contact_phone }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="footer">
            <p>If you have any questions about your order, please reply to this email or contact us at <a href="mailto:support@kelvs.com">support@kelvs.com</a>.</p>
            <p>&copy; {{ date('Y') }} KELVS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
