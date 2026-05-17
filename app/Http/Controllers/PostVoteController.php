<?php

namespace App\Http\Controllers;

use App\Notifications\NuevoVotoNotification;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostVote;
use Illuminate\Support\Facades\Auth;

class PostVoteController extends Controller
{
    //
    public function vote(Request $request, Post $post)
    {
        $request->validate([
            'vote' => 'required|in:1,-1',
        ]);

        $value = (int) $request->vote;

        $existing = PostVote::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            if ($existing->vote === $value) {
                PostVote::where('user_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->delete();
            } else {
                PostVote::where('user_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->update(['vote' => $value]);
            }
        } else {
            PostVote::create([
                'user_id' => Auth::id(),
                'post_id' => $post->id,
                'vote'    => $value,
            ]);
        }

        $post->refresh();
        $post->updateHotScore();

        $karma = PostVote::where('post_id', $post->id)->sum('vote');

        // Notificar al autor cuando recibe un upvote (no notificar al quitarlo)
        if ($post->user_id !== Auth::id() && $value === 1 && !$existing) {
            $post->user->notify(new NuevoVotoNotification($post, $karma));
        }
        $userVote = PostVote::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->value('vote');

        return response()->json([
            'karma'     => $karma,
            'user_vote' => $userVote, // null si canceló, 1 o -1 si votó
        ]);
    }
}
