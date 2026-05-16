<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
  // ─── Dashboard ────────────────────────────────────────────────
  public function index()
  {
    $stats = [
      'usuarios'         => User::count(),
      'posts'            => Post::count(),
      'reportesPendientes' => Reporte::where('estado', 'pendiente')->count(),
      'postsFijados'     => Post::where('fijada', true)->count(),
    ];

    $reportesRecientes = Reporte::with(['usuario', 'post'])
      ->where('estado', 'pendiente')
      ->latest()
      ->take(5)
      ->get();

    return view('admin.index', compact('stats', 'reportesRecientes'));
  }

  // ─── Reportes ─────────────────────────────────────────────────
  public function reportes()
  {
    $reportes = Reporte::with(['usuario', 'post.user'])
      ->orderByRaw("FIELD(estado, 'pendiente', 'resuelto_eliminado', 'resuelto_descartado')")
      ->latest()
      ->paginate(15);

    return view('admin.reportes', compact('reportes'));
  }

  public function resolverReporte(Request $request, Reporte $reporte)
  {
    $accion = $request->input('accion'); // 'descartar' | 'eliminar'

    if ($accion === 'eliminar') {
      $reporte->post?->delete();
      $reporte->update(['estado' => 'resuelto_eliminado']);
    } else {
      $reporte->update(['estado' => 'resuelto_descartado']);
    }

    return back()->with('success', $accion === 'eliminar'
      ? 'Publicación eliminada y reporte resuelto.'
      : 'Reporte descartado correctamente.');
  }

  // ─── Usuarios ─────────────────────────────────────────────────
  public function usuarios(Request $request)
  {
    $query = User::withCount(['posts', 'reportes']);

    if ($request->filled('buscar')) {
      $buscar = $request->buscar;
      $query->where(function ($q) use ($buscar) {
        $q->where('name', 'like', "%{$buscar}%")
          ->orWhere('username', 'like', "%{$buscar}%")
          ->orWhere('email', 'like', "%{$buscar}%");
      });
    }

    $usuarios = $query->latest()->paginate(20)->withQueryString();

    return view('admin.usuarios', compact('usuarios'));
  }

  public function cambiarRol(Request $request, User $user)
  {
    $request->validate([
      'rol' => ['required', 'in:estudiante,docente,admin'],
    ]);

    $user->update(['rol' => $request->rol]);

    return back()->with('success', "Rol de {$user->name} actualizado a {$request->rol}.");
  }

  public function suspender(User $user)
  {
    // No permitir suspender al propio admin
    if ($user->id === auth()->id()) {
      return back()->with('error', 'No puedes suspenderte a ti mismo.');
    }

    $user->update(['activo' => !$user->activo]);

    $mensaje = $user->activo ? 'Cuenta reactivada.' : 'Cuenta suspendida.';

    return back()->with('success', $mensaje);
  }

  // ─── Posts ────────────────────────────────────────────────────
  public function posts(Request $request)
  {
    $query = Post::with('user')->withCount(['comments', 'votes']);

    if ($request->filled('buscar')) {
      $query->where('title', 'like', "%{$request->buscar}%");
    }

    if ($request->filtro === 'fijados') {
      $query->where('fijada', true);
    }

    $posts = $query->latest()->paginate(20)->withQueryString();

    return view('admin.posts', compact('posts'));
  }

  public function fijarPost(Post $post)
  {
    $post->update(['fijada' => !$post->fijada]);

    return back()->with('success', $post->fijada ? 'Post fijado en el foro.' : 'Post desfijado.');
  }

  public function eliminarPost(Post $post)
  {
    $post->delete();

    return back()->with('success', 'Publicación eliminada correctamente.');
  }
}
