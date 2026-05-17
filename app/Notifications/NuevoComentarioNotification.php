<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

class NuevoComentarioNotification extends Notification
{
    public function __construct(public Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo'  => 'Nuevo comentario en tu publicación',
            'mensaje' => "{$this->comment->user->name} comentó: \"{$this->comment->content}\"",
            'url'     => route('posts.show', $this->comment->post_id),
        ];
    }
}
