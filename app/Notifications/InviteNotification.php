<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token, public string $name)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Meghívó a ' . config('app.name') . ' webalkalmazásába')
            ->greeting('Helló ' . $this->name . '!')
            ->line('Úgy néz ki meghívtak a Davidikum Kollégium hivatalos webalkalmazásába!')
            ->line('Az alábbi gombra kattintva el is kezdheted a regisztrációt:')
            ->action('Meghívó elfogadása', route('filament.common.auth.register', ['t' => $this->token]));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
