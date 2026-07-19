<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockActivityNotification extends Notification
{
    public function __construct(
        private string $title,
        private string $message,
        private string $category,
        private ?string $url = null,
        private string $color = 'blue'
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->payload($notifiable)))->onConnection('sync');
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage($this->payload($notifiable));
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour,')
            ->line($this->message)
            ->salutation('Cordialement, L’équipe Les Fermes Safia');

        $url = $this->personalizedUrl($notifiable);
        if ($url) {
            $mail->action('Voir le détail', url($url));
        }

        return $mail;
    }

    private function payload(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'url' => $this->personalizedUrl($notifiable),
            'color' => $this->color,
        ];
    }

    private function personalizedUrl(object $notifiable): ?string
    {
        if (!$this->url) {
            return null;
        }

        if ($notifiable->role === 'admin') {
            return str_replace(
                ['/comptable/aliments', '/comptable/oeufs', '/comptable/matieres-premieres', '/comptable/poulets'],
                ['/admin/aliments', '/admin/oeufs', '/admin/matieres-premieres', '/admin/poulets'],
                $this->url
            );
        }

        if ($notifiable->role === 'comptable') {
            return str_replace(
                ['/admin/aliments', '/admin/oeufs', '/admin/matieres-premieres', '/admin/poulets'],
                ['/comptable/aliments', '/comptable/oeufs', '/comptable/matieres-premieres', '/comptable/poulets'],
                $this->url
            );
        }

        return $this->url;
    }
}
