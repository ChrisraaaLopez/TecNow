<?php

use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RecoverPasswordController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentVoteController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
        $communities = \App\Models\Community::withCount('users')->get();
        $posts = \App\Models\Post::with(['user', 'communities', 'votes', 'comments'])->latest()->get();

        $sort  = $request->query('sort', 'reciente');
        $query = $request->query('q', '');

        $postsQuery = \App\Models\Post::with(['user', 'communities', 'votes', 'comments', 'sharedFrom.user']);

        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        if ($sort === 'popular') {
            $postsQuery->orderByDesc('hot_score');
        } elseif ($sort === 'trending') {
            $postsQuery->where('created_at', '>=', now()->subHours(48))->orderByDesc('hot_score');
        } else {
            $postsQuery->latest();
        }

        $posts = $postsQuery->get();

        $stats = [
            'miembros'     => \App\Models\User::where('activo', true)->count(),
            'publicaciones' => \App\Models\Post::whereDate('created_at', today())->count(),
            'comunidades'  => \App\Models\Community::count(),
        ];

        return view('dashboard', compact('communities', 'posts', 'sort', 'query', 'stats'));
    })->name('dashboard');

    // Rutas para perfiles
    Route::get('/perfil', function () {
        $user = Auth::user();
        $posts = $user->posts()
            ->with(['votes', 'communities', 'comments', 'sharedFrom.user'])
            ->latest()
            ->get();

        $postsCount = $user->posts()->count();
        $likesCount = \App\Models\PostVote::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('vote', 1)
            ->count();
        $commentsCount = $user->comments()->count();

        return view('perfil', compact('posts', 'postsCount', 'likesCount', 'commentsCount'));
    })->name('perfil');

    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::patch('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    // Perfil público de otros usuarios
    Route::get('/perfil/{username}', [ProfileController::class, 'showPublic'])->name('perfil.show');

    // Communities and Posts
    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.store');
    Route::get('/communities/{community:slug}', [CommunityController::class, 'show'])->name('communities.show');
    Route::post('/communities/{community:slug}/join', [CommunityController::class, 'join'])->name('communities.join');
    Route::post('/communities/{community:slug}/leave', [CommunityController::class, 'leave'])->name('communities.leave');
    Route::post('/communities/{community}/add-admin', [CommunityController::class, 'addAdmin'])->name('communities.addAdmin');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware('noSuspendido');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('auth');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::get('/popular', [PostController::class, 'popular'])->name('popular');
    Route::get('/trending', [PostController::class, 'trending'])->name('trending');

    Route::post('/posts/{post}/vote', [PostVoteController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{post}/share', [\App\Http\Controllers\ShareController::class, 'store'])->name('posts.share')->middleware('noSuspendido');

    // Notificaciones
    Route::get('/buscar/sugerencias', [\App\Http\Controllers\SearchController::class, 'suggestions'])->name('search.suggestions');

    Route::get('/notificaciones', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificaciones/json', [App\Http\Controllers\NotificationController::class, 'json'])->name('notifications.json');
    Route::post('/notificaciones/{id}/leer', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notificaciones/leer-todas', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Admin - Gestión de roles

    // Rutas para gestión de Comentarios
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/vote', [CommentVoteController::class, 'vote'])->name('comments.vote');
});

// Recuperar contraseñas
Route::get('/recuperar', [RecoverPasswordController::class, 'show'])->name('recover.show');
Route::post('/recuperar', [RecoverPasswordController::class, 'findUser'])->name('recover.find');
Route::post('/recuperar/reset', [RecoverPasswordController::class, 'reset'])->name('recover.reset');

require __DIR__.'/auth.php';

// Administradores
Route::prefix('admin')->middleware(['auth', 'esAdmin'])->name('admin.')->group(function () {
    Route::get('/',                               [AdminController::class, 'index'])          ->name('index');
    Route::get('/reportes',                       [AdminController::class, 'reportes'])       ->name('reportes');
    Route::patch('/reportes/{reporte}/resolver',  [AdminController::class, 'resolverReporte'])->name('reportes.resolver');
    Route::get('/usuarios',                       [AdminController::class, 'usuarios'])       ->name('usuarios');
    Route::patch('/usuarios/{user}/rol',          [AdminController::class, 'updateRole'])     ->name('usuarios.rol');
    Route::patch('/usuarios/{user}/suspender',    [AdminController::class, 'suspender'])      ->name('usuarios.suspender');
    Route::get('/posts',                          [AdminController::class, 'posts'])          ->name('posts');
    Route::patch('/posts/{post}/fijar',           [AdminController::class, 'fijarPost'])      ->name('posts.fijar');
    Route::delete('/posts/{post}',                [AdminController::class, 'eliminarPost'])   ->name('posts.eliminar');
});
