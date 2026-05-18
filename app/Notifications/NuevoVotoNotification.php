<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NuevoVotoNotification extends Notification
{
    public function __construct(
        public Post $post,
        public int  $karma,
        public User $voter
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo'  => "{$this->voter->name} votó tu publicación",
            'mensaje' => "\"{$this->post->title}\" ahora tiene {$this->karma} puntos de karma.",
            'url'     => route('posts.show', $this->post->id),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Broadcast sincrónico (onConnection sync) — evita depender del queue worker.
     * Sin esto la notificación se encola y nunca aparece en tiempo real si no hay worker.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toDatabase($notifiable)))
            ->onConnection('sync');
    }
}
