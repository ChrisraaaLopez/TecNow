<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentVoteController extends Controller
{
    //
    public function vote(Request $request, Comment $comment)
    {
        $request->validate(['vote' => 'required|in:1,-1']);

        $value = (int) $request->vote;

        $existing = CommentVote::where('user_id', Auth::id())
            ->where('comment_id', $comment->id)
            ->first();

        if ($existing) {
            if ($existing->vote === $value) {
                CommentVote::where('user_id', Auth::id())
                    ->where('comment_id', $comment->id)
                    ->delete();
            } else {
                CommentVote::where('user_id', Auth::id())
                    ->where('comment_id', $comment->id)
                    ->update(['vote' => $value]);
            }
        } else {
            CommentVote::create([
                'user_id'    => Auth::id(),
                'comment_id' => $comment->id,
                'vote'       => $value,
            ]);
        }

        $karma    = CommentVote::where('comment_id', $comment->id)->sum('vote');
        $userVote = CommentVote::where('user_id', Auth::id())
            ->where('comment_id', $comment->id)
            ->value('vote');

        return response()->json([
            'karma'     => $karma,
            'user_vote' => $userVote,
        ]);
    }
}
