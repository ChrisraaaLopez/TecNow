<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Notification;

class NuevoVotoNotification extends Notification
{
    public function __construct(public Post $post, public int $karma) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo'  => 'Tu publicación está ganando votos',
            'mensaje' => "Tu post \"{$this->post->title}\" alcanzó {$this->karma} puntos de karma.",
            'url'     => route('posts.show', $this->post->id),
        ];
    }
}
