<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->locale(app()->getLocale());
    }

    public function build()
    {
        return $this->subject(__('mail.subject_welcome'))
                    ->view('emails.welcome');
    }
}
