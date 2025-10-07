<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class NewUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Welcome to DormCo Management Dashboard')
            ->greeting('Welcome to DormCo Management Dashboard!')
            ->line('Your account has been created successfully.')
            ->line('To get started, please set your password by clicking the button below.')
            ->action('Set Your Password', $resetUrl)
            ->line('This password reset link will expire in 24 hours.')
            ->line('If you did not request this account, no further action is required.')
            ->salutation('Best regards, The DormCo Team');
    }

    /**
     * Get the password reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
            [
                'token' => app('auth.password.broker')->createToken($notifiable),
                'email' => $notifiable->getEmailForPasswordReset(),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}