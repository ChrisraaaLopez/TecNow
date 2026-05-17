<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
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
}
