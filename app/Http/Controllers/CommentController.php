<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content'   => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'post_id'   => $post->id,
            'user_id'   => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content'   => $validated['content'],
        ]);

        return back();
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
