<?php

namespace App\Livewire\Storefront;

use App\Models\EmailSubscriber;
use Livewire\Component;

class NewsletterPopup extends Component
{
    public $email = '';
    public $phone = '';
    public $submitted = false;
    public $discountCode = 'WELCOME10';

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

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.storefront.newsletter-popup');
    }
}
