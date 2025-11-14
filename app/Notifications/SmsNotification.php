<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Services\SmsService;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $message;
    private ?string $sender;
    private ?string $sendAt;

    public function __construct(string $message, ?string $sender = null, ?string $sendAt = null)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->sendAt = $sendAt;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): array
    {
        return [
            'from' => $this->sender ?? config('services.aqilas.default_sender'),
            'text' => $this->message,
            'to' => [$this->getPhoneNumber($notifiable)],
            'send_at' => $this->sendAt,
        ];
    }

    protected function getPhoneNumber($notifiable): string
    {
        // Adapter selon la structure de votre modèle User
        return $notifiable->phone_number ?? $notifiable->telephone ?? '';
    }
}
