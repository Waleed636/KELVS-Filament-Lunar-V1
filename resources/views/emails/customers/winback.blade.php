<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Miss You at KELVS</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 20px 0;
            -webkit-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #eaeaea;
        }
        .header {
            background-color: #0d0d0d;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 26px;
            letter-spacing: 4px;
            margin: 0;
            font-weight: 800;
            text-transform: uppercase;
        }
        .content {
            padding: 35px 30px;
            color: #333333;
        }
        .greeting h2 {
            font-size: 20px;
            color: #111111;
            margin-top: 0;
            margin-bottom: 12px;
            font-weight: 700;
        }
        .greeting p {
            font-size: 15px;
            line-height: 1.6;
            color: #555555;
            margin-bottom: 24px;
        }
        .coupon-card {
            background: linear-gradient(135deg, #0d0d0d 0%, #1f1f1f 100%);
            border-radius: 8px;
            padding: 26px 20px;
            text-align: center;
            color: #ffffff;
            margin: 28px 0;
            border: 1px solid #333333;
        }
        .coupon-tag {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d4af37;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .coupon-code {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            display: inline-block;
            padding: 10px 24px;
            border-radius: 6px;
            border: 1px dashed #d4af37;
            margin: 8px 0;
        }
        .coupon-desc {
            font-size: 13px;
            color: #cccccc;
            margin-top: 8px;
        }
        .cta-container {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn-shop {
            display: inline-block;
            background-color: #0d0d0d;
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 36px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-radius: 4px;
            transition: background-color 0.2s;
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
            padding: 10px 6px;
            font-size: 12px;
            color: #555555;
            font-weight: 600;
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
                padding: 25px 18px;
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
                <h2>It's been a while, {{ $customerName ?: 'friend' }} ✨</h2>
                <p>
                    We noticed it's been about a month since your last order with us! We hope you've been thoroughly enjoying your pieces.
                </p>
                <p>
                    Since your last visit, we've introduced fresh additions and seasonal favorites to our catalog. To welcome you back, we've prepared an exclusive return treat just for you:
                </p>
            </div>

            <div class="coupon-card">
                <div class="coupon-tag">Exclusive Comeback Gift</div>
                <div class="coupon-code">{{ $couponCode }}</div>
                <div class="coupon-desc">Get <strong>10% OFF</strong> your next order. Enter this code at checkout.</div>
            </div>

            <div class="cta-container">
                <a href="{{ $storeUrl }}" target="_blank" class="btn-shop">Explore New Arrivals &rarr;</a>
            </div>

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
