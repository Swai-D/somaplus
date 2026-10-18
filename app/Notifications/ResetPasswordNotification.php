<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The password reset email, in the language of the person who asked for it.
 * The link points at the address the request came from, so a reset started at
 * one school's address is finished at that school's address.
 */
class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        return (new MailMessage)
            ->subject(__('passwords.mail.subject', ['app' => config('app.name')]))
            ->greeting(__('passwords.mail.greeting'))
            ->line(__('passwords.mail.intro'))
            ->line($url)
            ->line(__('passwords.mail.expires', ['minutes' => (int) config('auth.passwords.users.expire')]))
            ->line(__('passwords.mail.ignore'))
            ->salutation(config('app.name'));
    }
}
