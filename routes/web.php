<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RecoverPasswordController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentVoteController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;

Route::get('/', fn () => redirect('/dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil propio
    Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::patch('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    // Perfil público de otros usuarios
    Route::get('/perfil/{username}', [ProfileController::class, 'showPublic'])->name('perfil.show');

    // Comunidades
    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.store');
    Route::get('/communities/{community:slug}', [CommunityController::class, 'show'])->name('communities.show');
    Route::post('/communities/{community:slug}/join', [CommunityController::class, 'join'])->name('communities.join');
    Route::post('/communities/{community:slug}/leave', [CommunityController::class, 'leave'])->name('communities.leave');
    Route::post('/communities/{community}/add-admin', [CommunityController::class, 'addAdmin'])->name('communities.addAdmin');

    // Posts
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware('noSuspendido');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/popular', [PostController::class, 'popular'])->name('popular');
    Route::get('/trending', [PostController::class, 'trending'])->name('trending');

    // Votos y compartir
    Route::post('/posts/{post}/vote', [PostVoteController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{post}/share', [ShareController::class, 'store'])->name('posts.share')->middleware('noSuspendido');

    // Búsqueda
    Route::get('/buscar/sugerencias', [SearchController::class, 'suggestions'])->name('search.suggestions');

    // Notificaciones
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notificaciones/json', [NotificationController::class, 'json'])->name('notifications.json');
    Route::post('/notificaciones/{id}/leer', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notificaciones/leer-todas', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Comentarios
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('noSuspendido');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/vote', [CommentVoteController::class, 'vote'])->name('comments.vote');

    // Reportes
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store')->middleware('noSuspendido');
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
