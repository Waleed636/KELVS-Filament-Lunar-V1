<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class OrderShippedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $trackingNumber;
    public string $trackingUrl;
    public string $carrier;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Order $order,
        string $trackingNumber,
        ?string $trackingUrl = null,
        string $carrier = 'PostEx'
    ) {
        $this->order = $order;
        $this->trackingNumber = $trackingNumber;
        $this->carrier = $carrier;
        $this->trackingUrl = $trackingUrl ?: 'https://postex.pk/tracking?tracking_no=' . urlencode($trackingNumber);

        $this->order->load(['shippingAddress', 'billingAddress']);

        // Safely load physical product lines
        if ($this->order->lines) {
            $this->order->lines->where('type', '!=', 'shipping')->load(['purchasable.product']);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your KELVS Order has Shipped! Tracking #' . $this->trackingNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.shipped',
        );
    }
}
