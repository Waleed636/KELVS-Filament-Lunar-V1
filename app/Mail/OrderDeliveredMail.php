<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class OrderDeliveredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->order->load(['shippingAddress', 'billingAddress']);

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
            subject: 'Delivered: Your KELVS Order has Arrived! (#' . $this->order->reference . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.delivered',
        );
    }
}
