<?php

namespace App\Http\Controllers;

use App\Events\CommentAddedEvent;
use App\Models\Post;
use App\Models\Comment;
use App\Notifications\NuevoComentarioNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
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

        // Broadcast en tiempo real el nuevo conteo y el ID del comentario
        try {
            CommentAddedEvent::dispatch(
                $post->id,
                $post->comments()->count(),
                $comment->id,
                $comment->parent_id
            );
        } catch (\Exception $e) {
            // Si Reverb no está disponible, el comentario igual se guarda
        }

        // Notificar al autor del post (si no es el mismo que comenta)
        try {
            if ($post->user_id !== Auth::id()) {
                $post->user->notify(new NuevoComentarioNotification($comment));
            }

            // Si es una respuesta, notificar también al autor del comentario padre
            if (!empty($validated['parent_id'])) {
                $parentComment = Comment::find($validated['parent_id']);
                if ($parentComment && $parentComment->user_id !== Auth::id() && $parentComment->user_id !== $post->user_id) {
                    $parentComment->user->notify(new NuevoComentarioNotification($comment));
                }
            }
        } catch (\Exception $e) {
            // Si la notificación falla, el comentario igual se guarda
        }

        return back();
    }

    public function card(Comment $comment)
    {
        $comment->load(['user', 'votes']);
        $karma    = $comment->votes->sum('vote');
        $userVote = $comment->votes->where('user_id', Auth::id())->first()?->vote;
        return view('comments._card', compact('comment', 'karma', 'userVote'));
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        $isAdmin = $user->rol === 'admin' || $user->global_role === 'admin';

        if ($comment->user_id !== $user->id && !$isAdmin) {
            abort(403);
        }

        $comment->delete();
        return back();
    }
}
