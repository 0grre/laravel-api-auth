<?php

namespace Ogrre\ApiAuth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ApiResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('laravel-api-auth::messages.reset_password_subject'))
            ->line(Lang::get('laravel-api-auth::messages.reset_password_line_1'))
            ->action(Lang::get('laravel-api-auth::messages.reset_password_action'), url(config('app.url').route('password.reset', $this->token, false)))
            ->line(Lang::get('laravel-api-auth::messages.reset_password_line_2'));
    }
}
