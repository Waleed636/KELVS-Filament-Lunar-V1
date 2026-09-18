<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $step === 2 ? 'Items in your bag are waiting' : 'You left items in your bag' }}</title>
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
            padding: 35px 20px;
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
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-recover {
            display: inline-block;
            background-color: #0d0d0d;
            color: #ffffff !important;
            padding: 16px 36px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        .items-card {
            background-color: #fcfcfc;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .items-heading {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888888;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .item-title {
            font-weight: 600;
            color: #0d0d0d;
        }
        .total-row {
            margin-top: 15px;
            padding-top: 12px;
            border-top: 2px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 700;
            color: #0d0d0d;
        }
        .perks {
            display: table;
            width: 100%;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        .perk-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            font-size: 13px;
            color: #555555;
            font-weight: 500;
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
            .perks {
                display: block;
            }
            .perk-item {
                display: block;
                padding: 6px 0;
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
                @if($step === 2)
                    <h2>Your items are still waiting, but stock is limited ⏳</h2>
                    <p>Hello {{ $partialOrder->name ?: 'there' }}, we noticed your bag is still saved from yesterday. Popular pieces tend to move quickly, and we don't want you to miss out on yours.</p>
                @else
                    <h2>Did you forget something in your bag? 🛍️</h2>
                    <p>Hello {{ $partialOrder->name ?: 'there' }}, we noticed you were about to place an order at KELVS but didn't get a chance to complete it. Good news: we held your items in your bag so you can pick right up where you left off!</p>
                @endif
            </div>

            <!-- Items summary card -->
            @if(!empty($partialOrder->cart_contents) && is_array($partialOrder->cart_contents))
                <div class="items-card">
                    <div class="items-heading">Your Saved Items</div>
                    <table class="items-table">
                        @foreach($partialOrder->cart_contents as $item)
                            <tr>
                                <td>
                                    <span class="item-title">{{ $item['name'] ?? 'Product' }}</span>
                                    <span style="color: #888888; font-size: 13px;"> &times; {{ $item['quantity'] ?? 1 }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    PKR {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    @if($partialOrder->cart_total > 0)
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>PKR {{ number_format($partialOrder->cart_total, 2) }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recovery Call To Action -->
            <div class="cta-container">
                <a href="{{ $checkoutUrl }}" class="btn-recover">Return to Checkout &rarr;</a>
            </div>

            <!-- Value / Trust Perks -->
            <div class="perks">
                <div class="perk-item">
                    🚚 Fast Nationwide Delivery
                </div>
                <div class="perk-item">
                    💵 Cash on Delivery Available
                </div>
                <div class="perk-item">
                    🛡️ 100% Quality Guaranteed
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Need assistance or have questions? Contact us at <a href="mailto:contact@kelvsint.com">contact@kelvsint.com</a></p>
            <p>&copy; {{ date('Y') }} KELVS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
