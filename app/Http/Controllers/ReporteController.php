<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reporte;
use App\Models\User;
use App\Notifications\ReporteRecibidoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'motivo'     => 'required|string|max:100',
            'descripcion'=> 'nullable|string|max:500',
            'post_id'    => 'nullable|exists:posts,id',
            'comment_id' => 'nullable|exists:comments,id',
        ]);

        if (!$request->post_id && !$request->comment_id) {
            return back()->with('error', 'Debe indicar qué estás reportando.');
        }

        // Prevenir reporte duplicado del mismo usuario
        $existe = Reporte::where('user_id', Auth::id())
            ->where('estado', 'pendiente')
            ->when($request->post_id, fn($q) => $q->where('post_id', $request->post_id))
            ->when($request->comment_id, fn($q) => $q->where('comment_id', $request->comment_id))
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya reportaste este contenido anteriormente.');
        }

        // Determinar el usuario reportado
        $reportedUserId = null;
        if ($request->post_id) {
            $reportedUserId = Post::find($request->post_id)?->user_id;
        } elseif ($request->comment_id) {
            $reportedUserId = Comment::find($request->comment_id)?->user_id;
        }

        $reporte = Reporte::create([
            'motivo'           => $request->motivo,
            'descripcion'      => $request->descripcion,
            'estado'           => 'pendiente',
            'user_id'          => Auth::id(),
            'post_id'          => $request->post_id,
            'comment_id'       => $request->comment_id,
            'reported_user_id' => $reportedUserId,
        ]);

        // Notificar a todos los admins
        User::where('rol', 'admin')->each(function (User $admin) use ($reporte) {
            $admin->notify(new ReporteRecibidoNotification($reporte));
        });

        return back()->with('success', 'Reporte enviado. Los administradores lo revisarán pronto.');
    }
}
