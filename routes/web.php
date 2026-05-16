<?php

use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RecoverPasswordController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $communities = \App\Models\Community::withCount('users')->get();
        $posts = \App\Models\Post::with(['user', 'communities', 'votes'])->latest()->get();

        return view('dashboard', compact('communities', 'posts'));
    })->name('dashboard');

    // Rutas para perfiles
    Route::get('/perfil', function () {
        $user = Auth::user();
        $posts = $user->posts()
            ->with('votes')
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
    Route::post('/communities/{community}/add-admin', [CommunityController::class, 'addAdmin'])->name('communities.addAdmin');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('auth');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::get('/popular', [PostController::class, 'popular'])->name('popular');
    Route::get('/trending', [PostController::class, 'trending'])->name('trending');

    // Funciones experimentales
    Route::post('/posts/{post}/vote', [PostVoteController::class, 'vote'])->name('posts.vote');

    // Notificaciones
    Route::get('/notificaciones', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificaciones/{id}/leer', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notificaciones/leer-todas', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Admin - Gestión de roles
    Route::middleware('is_admin')->group(function () {
        Route::get('/admin/usuarios', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users');
        Route::patch('/admin/usuarios/{user}/rol', [App\Http\Controllers\AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    });
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
    Route::patch('/usuarios/{user}/rol',          [AdminController::class, 'cambiarRol'])     ->name('usuarios.rol');
    Route::patch('/usuarios/{user}/suspender',    [AdminController::class, 'suspender'])      ->name('usuarios.suspender');
    Route::get('/posts',                          [AdminController::class, 'posts'])          ->name('posts');
    Route::patch('/posts/{post}/fijar',           [AdminController::class, 'fijarPost'])      ->name('posts.fijar');
    Route::delete('/posts/{post}',                [AdminController::class, 'eliminarPost'])   ->name('posts.eliminar');
});
