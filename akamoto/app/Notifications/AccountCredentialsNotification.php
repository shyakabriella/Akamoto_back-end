<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCredentialsNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly string $plainPassword
    ) {
    }

    /**
     * Send notification by email.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build email message.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Akamoto Account Credentials')
            ->view('emails.account-credentials', [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => config('app.frontend_url', config('app.url')),
            ]);
    }

    /**
     * Array notification data.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'username' => $this->user->username,
            'email' => $this->user->email,
        ];
    }
}