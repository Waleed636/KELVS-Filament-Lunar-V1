<?php

namespace App\Livewire\Storefront;

use App\Mail\NewsletterWelcomeMail;
use App\Models\EmailSubscriber;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Component;

class NewsletterPopup extends Component
{
    public string $email = '';
    public string $phone = '';
    public bool $submitted = false;

    #[Locked]
    public string $discountCode = 'WELCOME10';

    public function submit()
    {
        // Check if both are empty
        if (empty($this->email) && empty($this->phone)) {
            $this->addError('email', 'Please enter your email or phone number.');
            $this->addError('phone', 'Please enter your email or phone number.');
            return;
        }

        $rules = [];
        $messages = [];

        if (!empty($this->email)) {
            $rules['email'] = 'email|max:255';
            $messages['email.email'] = 'Please enter a valid email address.';
        }

        if (!empty($this->phone)) {
            $rules['phone'] = 'string|min:8|max:20|regex:/^[+0-9\s\-()]+$/';
            $messages['phone.regex'] = 'Please enter a valid phone number.';
            $messages['phone.min'] = 'Phone number must be at least 8 digits.';
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        // Save subscriber to database
        EmailSubscriber::create([
            'email' => !empty($this->email) ? trim($this->email) : null,
            'phone' => !empty($this->phone) ? trim($this->phone) : null,
            'source' => 'popup',
            'discount_code' => $this->discountCode,
            'subscribed_at' => now(),
        ]);

        // Send instant welcome email with the coupon code if email provided
        $cleanEmail = !empty($this->email) ? trim($this->email) : null;
        if ($cleanEmail && filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($cleanEmail)->queue(new NewsletterWelcomeMail($cleanEmail, $this->discountCode));
                Log::info("Newsletter: Queued welcome email for {$cleanEmail}");
            } catch (\Throwable $e) {
                Log::error("Newsletter: Failed to queue welcome email for {$cleanEmail}: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
            }
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.storefront.newsletter-popup');
    }
}
