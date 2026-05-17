<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    // Ver comentarios de un post — público
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user:id,name,username,avatar')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $comments,
        ]);
    }

    // Crear comentario — requiere login y no estar bloqueado
    public function store(Request $request, Post $post)
    {
        if (!auth('api')->user()->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está bloqueada. No puedes comentar.',
            ], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => auth('api')->id(),
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comentario creado correctamente.',
            'data'    => $comment->load('user:id,name,username,avatar'),
        ], 201);
    }

    // Eliminar comentario — solo el dueño
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado correctamente.',
        ]);
    }
}