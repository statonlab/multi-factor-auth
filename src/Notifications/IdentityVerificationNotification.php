<?php

namespace Statonlab\MultiFactorAuth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Statonlab\MultiFactorAuth\Models\AuthenticationToken;

class IdentityVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \Statonlab\MultiFactorAuth\Models\AuthenticationToken */
    protected $token;

    /**
     * @var string
     */
    protected $channel;

    /**
     * Create a new notification instance.
     *
     * @param \Statonlab\MultiFactorAuth\Models\AuthenticationToken $token
     * @param string $channel
     */
    public function __construct(AuthenticationToken $token, string $channel = 'mail')
    {
        $this->token = $token;
        $this->channel = $channel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [$this->channel];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->greeting("Hello $notifiable->name")
            ->subject("Identity Notification")
            ->line('Please use the code below to verify your identity.')
            ->line($this->token->token)
            ->line('Thank you for using our application!');
    }
}
