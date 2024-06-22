<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;

class CustomVerifyEmail extends VerifyEmailNotification
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Custom Email Verification Subject')
            ->line('This is a custom email verification message.')
            ->action('Verify Email', $verificationUrl)
            ->line('Thank you for using our application!');
    }
}
