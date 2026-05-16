<?php

namespace App\Notifications;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Notification;

class PostCompartidoNotification extends Notification
{
    public function __construct(
        public Post       $sharedPost,
        public User       $sharedBy,
        public ?Community $community = null   // null = compartido en perfil personal
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $donde = $this->community
            ? "en {$this->community->name}"
            : "en su perfil";

        return [
            'titulo'  => 'Tu publicación fue compartida',
            'mensaje' => "{$this->sharedBy->name} compartió \"{$this->sharedPost->title}\" {$donde}.",
            'url'     => route('posts.show', $this->sharedPost),
        ];
    }
}
