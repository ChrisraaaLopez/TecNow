<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    // Ver todos los posts — público
    public function index()
    {
        $posts = Post::with(['user:id,name,username,avatar'])
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $posts,
        ]);
    }

    // Ver un post individual — público
    public function show(Post $post)
    {
        $post->load(['user:id,name,username,avatar', 'comments.user:id,name,username,avatar']);

        return response()->json([
            'success' => true,
            'data'    => $post,
        ]);
    }

    // Crear post — requiere login y no estar bloqueado
    public function store(Request $request)
    {
        if (!auth('api')->user()->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está bloqueada. No puedes crear posts.',
            ], 403);
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => auth('api')->id(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post creado correctamente.',
            'data'    => $post->load('user:id,name,username,avatar'),
        ], 201);
    }

    // Eliminar post — solo el dueño
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este post.',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post eliminado correctamente.',
        ]);
    }
}