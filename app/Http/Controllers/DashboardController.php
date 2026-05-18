<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $communities = Community::withCount('users')->get();

        $sort  = $request->query('sort', 'reciente');
        $query = $request->query('q', '');

        $postsQuery = Post::with(['user', 'communities', 'votes', 'comments', 'sharedFrom.user']);

        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        if ($sort === 'popular') {
            $postsQuery->orderByDesc('hot_score');
        } elseif ($sort === 'trending') {
            $postsQuery->where('created_at', '>=', now()->subHours(48))->orderByDesc('hot_score');
        } else {
            $postsQuery->latest();
        }

        $posts = $postsQuery->get();

        $stats = [
            'miembros'      => User::where('activo', true)->count(),
            'publicaciones' => Post::whereDate('created_at', today())->count(),
            'comunidades'   => Community::count(),
        ];

        return view('dashboard', compact('communities', 'posts', 'sort', 'query', 'stats'));
    }

    public function comunidades()
    {
        $communities     = Community::withCount('users')->orderBy('tipo')->orderBy('name')->get();
        $userCommunityIds = Auth::user()->communities()->pluck('communities.id');
        return view('comunidades.index', compact('communities', 'userCommunityIds'));
    }

    public function perfil()
    {
        $user = Auth::user();

        $posts = $user->posts()
            ->with(['votes', 'communities', 'comments', 'sharedFrom.user'])
            ->latest()
            ->get();

        $postsCount    = $user->posts()->count();
        $likesCount    = PostVote::whereIn('post_id', $user->posts()->pluck('id'))
                            ->where('vote', 1)
                            ->count();
        $commentsCount = $user->comments()->count();

        return view('perfil', compact('posts', 'postsCount', 'likesCount', 'commentsCount'));
    }
}
