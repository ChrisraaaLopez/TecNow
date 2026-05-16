<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NuevoComentarioNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        // Bloquear comentarios en avisos si no es admin
        $community = $post->communities()->first();
        if ($community?->isAvisos() && Auth::user()->rol !== 'admin') {
            return back()->with('error', 'Solo los administradores pueden comentar en avisos institucionales.');
        }

        $validated = $request->validate([
            'content'   => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'post_id'   => $post->id,
            'user_id'   => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content'   => $validated['content'],
        ]);

        // Notificar al autor del post (no notificarse a sí mismo)
        if ($post->user_id !== Auth::id()) {
            $comment->load('user');
            $post->user->notify(new NuevoComentarioNotification($comment));
        }

        return back();
    }

    public function destroy(Comment $comment)
    {
        $isAdmin = Auth::user()->rol === 'admin';
        if ($comment->user_id !== Auth::id() && !$isAdmin) {
            abort(403);
        }

        $comment->delete();
        return back();
    }
}
