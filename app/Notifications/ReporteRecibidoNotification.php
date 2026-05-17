<?php

namespace App\Notifications;

use App\Models\Reporte;
use Illuminate\Notifications\Notification;

class ReporteRecibidoNotification extends Notification
{
    public function __construct(public Reporte $reporte) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $tipo = $this->reporte->comment_id ? 'comentario' : 'publicación';
        return [
            'titulo'  => 'Nuevo reporte recibido',
            'mensaje' => "Se reportó un {$tipo} por: {$this->reporte->motivo}.",
            'url'     => route('admin.reportes'),
        ];
    }
}
