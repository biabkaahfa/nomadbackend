<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return [
            'subject' => 'Réinitialisation de votre mot de passe',
            'greeting' => 'Bonjour !',
            'message' => 'Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe.',
            'action_url' => $url,
            'action_text' => 'Réinitialiser le mot de passe',
            'expiration' => 'Ce lien expire dans 60 minutes.',
            'footer' => 'Si vous n\'avez pas fait cette demande, ignorez cet email.',
            'salutation' => 'Cordialement, L\'équipe ' . config('app.name'),
        ];
    }
}
