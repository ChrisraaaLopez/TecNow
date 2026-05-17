<?php

namespace App\Notifications;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PostCompartidoNotification extends Notification
{
    public function __construct(
        public Post       $sharedPost,
        public User       $sharedBy,
        public ?Community $community = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
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

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toDatabase($notifiable)))
            ->onConnection('sync');
    }
}
