<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Has Been Delivered</title>
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
        .delivered-badge {
            display: inline-block;
            background-color: #e6f4ea;
            color: #137333;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .care-box {
            background: #f8fafc;
            border-left: 4px solid #0d0d0d;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin: 30px 0;
        }
        .care-box h3 {
            margin-top: 0;
            font-size: 16px;
            color: #0d0d0d;
        }
        .care-box ul {
            margin: 0;
            padding-left: 20px;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }
        .btn-shop {
            display: inline-block;
            background-color: #0d0d0d;
            color: #ffffff !important;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            letter-spacing: 0.5px;
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
            <div style="text-align: center;">
                <span class="delivered-badge">✓ Package Delivered</span>
            </div>
            
            <div class="greeting">
                <h2>Welcome to the KELVS Family! ✨</h2>
                <p>Hello {{ $order->shippingAddress?->first_name ?? 'Valued Customer' }},</p>
                <p>Our courier records show that your order <strong>#{{ $order->reference }}</strong> has been delivered successfully. We hope you love opening and wearing your new pieces as much as we loved creating them for you.</p>
            </div>

            <div class="care-box">
                <h3>Fabric & Garment Care Tips</h3>
                <ul>
                    <li>Hand wash or gentle machine cycle in cold water.</li>
                    <li>Always dry in shade inside-out to preserve color vibrancy.</li>
                    <li>Iron on low-to-medium heat for the crispest look.</li>
                </ul>
            </div>

            <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                If you have any questions about your order or need an exchange, our team is always here to ensure your complete satisfaction.
            </p>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ config('app.url', 'https://kelvsint.com') }}" target="_blank" class="btn-shop">Visit KELVS Store</a>
            </div>
        </div>

        <div class="footer">
            <p>Questions or feedback? Reach us anytime at <a href="mailto:contact@kelvsint.com">contact@kelvsint.com</a></p>
            <p>&copy; {{ date('Y') }} KELVS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
