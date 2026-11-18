<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RegistrationOtpMail extends Mailable
{
    public function __construct(public string $name, public string $code)
    {
        $this->locale(app()->getLocale());
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.subject_otp'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.registration-otp', text: 'emails.registration-otp-text');
    }
}
