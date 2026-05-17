<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\CommentApiController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login', [AuthApiController::class, 'login']);

    // Posts públicos (FYP) — cualquiera puede ver sin login
    Route::get('/posts', [PostApiController::class, 'index']);
    Route::get('/posts/{post}', [PostApiController::class, 'show']);
    Route::get('/posts/{post}/comments', [CommentApiController::class, 'index']);

    // Rutas protegidas — requieren token JWT
    Route::middleware('auth:api')->group(function () {

        // Auth
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);

        // Posts
        Route::post('/posts', [PostApiController::class, 'store']);
        Route::delete('/posts/{post}', [PostApiController::class, 'destroy']);

        // Comentarios
        Route::post('/posts/{post}/comments', [CommentApiController::class, 'store']);
        Route::delete('/comments/{comment}', [CommentApiController::class, 'destroy']);
    });
});