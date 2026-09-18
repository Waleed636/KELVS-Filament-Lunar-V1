<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class CustomerWinBackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $customerName;
    public string $customerEmail;
    public ?Order $lastOrder;
    public string $couponCode;
    public string $storeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $customerName, string $customerEmail, ?Order $lastOrder = null, string $couponCode = 'MISSYOU10')
    {
        $this->customerName = trim($customerName);
        $this->customerEmail = trim($customerEmail);
        $this->lastOrder = $lastOrder;
        $this->couponCode = $couponCode;
        $this->storeUrl = rtrim(config('app.url', 'https://kelvsint.com'), '/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We miss you at KELVS ✨ (Here is 10% OFF your next order)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customers.winback',
            with: [
                'customerName' => $this->customerName,
                'couponCode' => $this->couponCode,
                'storeUrl' => $this->storeUrl,
                'lastOrder' => $this->lastOrder,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
