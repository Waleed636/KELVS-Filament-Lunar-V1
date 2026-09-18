<?php

namespace App\Mail;

use App\Models\PartialOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartRecoveryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PartialOrder $partialOrder;
    public int $step;
    public string $checkoutUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PartialOrder $partialOrder, int $step = 1)
    {
        $this->partialOrder = $partialOrder;
        $this->step = $step;
        $this->checkoutUrl = rtrim(config('app.url', 'https://kelvsint.com'), '/') . '/checkout';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->step === 2)
            ? 'We saved your bag, but stock is running low! | KELVS'
            : 'Did you leave something behind at KELVS?';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cart.recovery',
        );
    }
}
