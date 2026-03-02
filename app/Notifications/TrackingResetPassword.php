<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TrackingResetPassword extends ResetPassword
{

    public function __construct($token){
        parent::__construct($token); // IMPORTANT
    }

    
    public function toMail($notifiable){
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Tracking System - Password Reset Request')
            ->greeting('Hello!')
            ->line('We received a request to reset your password for your Tracking System account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Regards,')
            ->salutation('Tracking System Team');
    }
}