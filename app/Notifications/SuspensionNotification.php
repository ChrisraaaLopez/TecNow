<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SuspensionNotification extends Notification
{
    public function __construct(public bool $suspendido) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->suspendido) {
            return [
                'titulo'  => 'Cuenta suspendida',
                'mensaje' => 'Tu cuenta ha sido suspendida por un administrador. Puedes seguir viendo el contenido pero no puedes publicar ni comentar.',
                'url'     => route('dashboard'),
            ];
        }

        return [
            'titulo'  => 'Cuenta reactivada',
            'mensaje' => 'Tu cuenta ha sido reactivada. Ya puedes publicar y comentar de nuevo.',
            'url'     => route('dashboard'),
        ];
    }
}
