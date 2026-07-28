<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PolicyPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;
        $now = now();

        $policies = [
            [
                'slug' => 'return-policy',
                'title' => ['en' => 'Return & Exchange Policy'],
                'description' => ['en' => 'Official KELVS Return and Exchange Guidelines for orders in Pakistan.'],
                'content' => ['en' => '
                    <h2>Return & Exchange Policy</h2>
                    <p>At <strong>KELVS Skincare</strong>, customer satisfaction and product integrity are our highest priorities. We carefully inspect every product before dispatch. If you receive a damaged, defective, or incorrect item, we offer a straightforward <strong>7-day return and replacement guarantee</strong>.</p>
                    <h3>1. Eligibility for Returns & Exchanges</h3>
                    <ul>
                        <li>The return request must be raised within <strong>7 days</strong> of order delivery.</li>
                        <li>The product must be unused, unopened, and in its original retail packaging with all safety seals and batch labels intact.</li>
                        <li>Proof of purchase (Order ID, receipt, or registered phone number) is required.</li>
                        <li>Due to hygiene and safety standards, opened or partially used skincare products cannot be returned unless a verified manufacturing defect is confirmed by our quality team.</li>
                    </ul>
                    <h3>2. How to Initiate a Return</h3>
                    <ol>
                        <li>Contact our Customer Care team with your <strong>Order Number</strong> and a brief explanation of the issue.</li>
                        <li>Provide clear photos or a short video showing the damaged or incorrect item alongside the outer box shipping label.</li>
                        <li>Our support team will verify the details within 24 business hours and guide you through reverse courier pickup or dispatch instructions.</li>
                    </ol>
                    <h3>3. Exchange Process</h3>
                    <p>Once the returned item is received and inspected at our warehouse, a fresh replacement item will be dispatched to your address free of additional shipping charges.</p>
                '],
                'post_type' => 'page',
                'status' => 'publish',
                'published_at' => $now,
                'user_id' => $userId,
            ],
            [
                'slug' => 'privacy-policy',
                'title' => ['en' => 'Privacy Policy'],
                'description' => ['en' => 'How KELVS Skincare collects, uses, and protects customer data in Pakistan.'],
                'content' => ['en' => '
                    <h2>Privacy Policy</h2>
                    <p><strong>KELVS Skincare</strong> ("we", "our", "us") respects your privacy and is dedicated to protecting your personal information. This Privacy Policy outlines how your data is collected, processed, and safeguarded when visiting or making a purchase on <strong>kelvsint.com</strong>.</p>
                    <h3>1. Information We Collect</h3>
                    <p>When you place an order or create an account, we collect necessary personal details, including:</p>
                    <ul>
                        <li><strong>Contact Information:</strong> Full Name, Email Address, Mobile Number.</li>
                        <li><strong>Delivery Details:</strong> Complete Shipping Address, City, Postal Code.</li>
                        <li><strong>Transaction Data:</strong> Payment method selection (Cash on Delivery / Online Bank Transfer) and order history.</li>
                        <li><strong>Technical Data:</strong> IP address, device type, browser cookies for shopping cart functionality.</li>
                    </ul>
                    <h3>2. How We Use Your Information</h3>
                    <ul>
                        <li>To process, fulfill, and deliver your orders via logistics partners across Pakistan.</li>
                        <li>To send order status updates, tracking SMS, and customer support communications.</li>
                        <li>To maintain store security, prevent fraudulent checkouts, and improve user experience.</li>
                    </ul>
                    <h3>3. Data Protection & Sharing</h3>
                    <p>We <strong>never sell, trade, or rent</strong> your personal information to third parties. Your address and phone number are strictly shared with authorized logistics partners (e.g., PostEx, Leopards, TCS) solely for order delivery.</p>
                '],
                'post_type' => 'page',
                'status' => 'publish',
                'published_at' => $now,
                'user_id' => $userId,
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => ['en' => 'Terms & Conditions'],
                'description' => ['en' => 'Terms of service and store rules for shopping on KELVS Skincare.'],
                'content' => ['en' => '
                    <h2>Terms & Conditions</h2>
                    <p>Welcome to <strong>KELVS Skincare</strong>. By accessing <strong>kelvsint.com</strong>, browsing our product catalogue, or placing an order, you agree to be bound by the following Terms and Conditions.</p>
                    <h3>1. Product Formulations & Usage</h3>
                    <ul>
                        <li>KELVS products are dermatologically inspired cosmetic formulations intended for external facial and body application only.</li>
                        <li>Because individual skin types vary, we strongly recommend performing a <strong>24-hour patch test</strong> behind the ear or inner forearm prior to full application.</li>
                        <li>Avoid direct contact with eyes. Discontinue use immediately if irritation occurs and consult a qualified dermatologist.</li>
                    </ul>
                    <h3>2. Orders & Pricing</h3>
                    <ul>
                        <li>All prices listed on the website are in <strong>Pakistani Rupees (PKR)</strong> and include applicable local sales taxes.</li>
                        <li>We reserve the right to decline or cancel orders in cases of stock unavailability, pricing errors, or suspected fraudulent activity.</li>
                    </ul>
                    <h3>3. Intellectual Property</h3>
                    <p>All logos, brand names, product titles, custom formulas, text content, and graphics belong exclusively to KELVS Skincare. Unauthorized reproduction or commercial use is strictly prohibited.</p>
                '],
                'post_type' => 'page',
                'status' => 'publish',
                'published_at' => $now,
                'user_id' => $userId,
            ],
            [
                'slug' => 'shipping-policy',
                'title' => ['en' => 'Shipping & Delivery Policy'],
                'description' => ['en' => 'Nationwide shipping rates, delivery timelines, and courier tracking details across Pakistan.'],
                'content' => ['en' => '
                    <h2>Shipping & Delivery Policy</h2>
                    <p>We deliver nationwide across <strong>all major cities and remote towns in Pakistan</strong>. Your orders are processed quickly to ensure minimal wait times.</p>
                    <h3>1. Delivery Timelines</h3>
                    <ul>
                        <li><strong>Major Cities</strong> (Lahore, Karachi, Islamabad, Rawalpindi, Faisalabad, Multan, Sialkot, Gujranwala): <strong>2 to 4 working days</strong>.</li>
                        <li><strong>Other Cities & Remote Areas:</strong> <strong>3 to 5 working days</strong>.</li>
                        <li><strong>Order Dispatch:</strong> Orders placed before 3:00 PM (Monday through Saturday) are processed and handed over to courier partners same-day or next morning.</li>
                    </ul>
                    <h3>2. Delivery Charges & Free Shipping</h3>
                    <ul>
                        <li>Standard Flat Shipping fee applies at checkout for regular orders.</li>
                        <li><strong>Free Nationwide Shipping</strong> is automatically applied on qualifying order values or ongoing promotional campaigns.</li>
                    </ul>
                    <h3>3. Order Tracking</h3>
                    <p>Once your parcel is dispatched, a tracking link with your courier Consignment Number (CN) will be sent via SMS and Email so you can monitor delivery progress in real-time.</p>
                '],
                'post_type' => 'page',
                'status' => 'publish',
                'published_at' => $now,
                'user_id' => $userId,
            ],
            [
                'slug' => 'refund-policy',
                'title' => ['en' => 'Refund Policy'],
                'description' => ['en' => 'Official KELVS Refund procedures and payment reversal details.'],
                'content' => ['en' => '
                    <h2>Refund Policy</h2>
                    <p>At <strong>KELVS Skincare</strong>, we process refunds promptly for eligible orders in accordance with our return guidelines.</p>
                    <h3>1. Refund Eligibility</h3>
                    <p>Refunds are applicable in the following scenarios:</p>
                    <ul>
                        <li>You received a damaged, defective, or incorrect product and opted for a refund instead of a replacement.</li>
                        <li>Your prepaid order was cancelled prior to parcel dispatch.</li>
                        <li>Your parcel was lost in transit by the logistics partner.</li>
                    </ul>
                    <h3>2. Refund Method & Processing Time</h3>
                    <ul>
                        <li><strong>Bank Transfer (IBAN):</strong> Refunded directly to your Pakistani bank account within <strong>3 to 5 business days</strong> after return inspection.</li>
                        <li><strong>JazzCash / EasyPaisa:</strong> Transferred to your registered mobile wallet number within 24 to 48 hours.</li>
                        <li><strong>Store Credit Voucher:</strong> Instant voucher code issued for your next purchase.</li>
                    </ul>
                    <h3>3. Shipping Costs</h3>
                    <p>Original delivery shipping charges are non-refundable unless the return was caused by a fulfillment error on our end.</p>
                '],
                'post_type' => 'page',
                'status' => 'publish',
                'published_at' => $now,
                'user_id' => $userId,
            ],
        ];

        foreach ($policies as $data) {
            Post::updateOrCreate(
                ['slug' => $data['slug'], 'post_type' => 'page'],
                $data
            );
        }
    }
}
