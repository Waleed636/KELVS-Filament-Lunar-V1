<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $email;
    public string $couponCode;
    public string $storeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $couponCode = 'WELCOME10')
    {
        $this->email = trim($email);
        $this->couponCode = $couponCode;
        $this->storeUrl = rtrim(config('app.url', 'https://kelvsint.com'), '/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to KELVS! Here is your 10% OFF code 🎁',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.welcome',
            with: [
                'email' => $this->email,
                'couponCode' => $this->couponCode,
                'storeUrl' => $this->storeUrl,
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
