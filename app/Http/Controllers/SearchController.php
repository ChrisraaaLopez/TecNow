<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    // Misma paleta que community-icon.blade.php
    private static array $iconColors = [
        'ingenieria-en-sistemas-computacionales' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
        'ingenieria-electromecanica'              => ['bg' => '#fef9c3', 'color' => '#a16207'],
        'ingenieria-civil'                        => ['bg' => '#f1f5f9', 'color' => '#334155'],
        'ingenieria-en-gestion-empresarial'       => ['bg' => '#dcfce7', 'color' => '#15803d'],
        'licenciatura-en-arquitectura'            => ['bg' => '#ede9fe', 'color' => '#6d28d9'],
        'ingenieria-mecatronica'                  => ['bg' => '#ffedd5', 'color' => '#c2410c'],
        'ingenieria-en-industrias-alimentarias'   => ['bg' => '#d1fae5', 'color' => '#065f46'],
        'ingenieria-sistemas-automotrices'        => ['bg' => '#fee2e2', 'color' => '#991b1b'],
        'ingenieria-industrial'                   => ['bg' => '#e0f2fe', 'color' => '#0369a1'],
        'avisos-institucionales'                  => ['bg' => '#1e40af', 'color' => '#ffffff'],
    ];

    public function suggestions(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['posts' => [], 'communities' => [], 'users' => []]);
        }

        $posts = Post::with('user')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%");
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => [
                'type'    => 'post',
                'id'      => $p->id,
                'title'   => $p->title,
                'excerpt' => Str::limit($p->content, 55),
                'author'  => $p->user->name,
                'url'     => route('posts.show', $p),
            ]);

        $communities = Community::where('name', 'like', "%{$q}%")
            ->withCount('users')
            ->take(3)
            ->get()
            ->map(function ($c) {
                $cfg = self::$iconColors[$c->slug] ?? ['bg' => 'rgba(30,64,175,0.1)', 'color' => '#1e40af'];
                return [
                    'type'    => 'community',
                    'id'      => $c->id,
                    'name'    => $c->name,
                    'initial' => mb_strtoupper(mb_substr($c->name, 0, 1)),
                    'bg'      => $cfg['bg'],
                    'color'   => $cfg['color'],
                    'members' => $c->users_count,
                    'url'     => route('communities.show', $c),
                ];
            });

        $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%");
            })
            ->where('activo', true)
            ->take(3)
            ->get()
            ->map(fn($u) => [
                'type'     => 'user',
                'id'       => $u->id,
                'name'     => $u->name,
                'username' => $u->username,
                'avatar'   => asset('avatars/' . $u->avatar),
                'marco'    => $u->marco ? asset('marcos/' . $u->marco) : null,
                'url'      => route('perfil.show', $u->username),
            ]);

        return response()->json(['posts' => $posts, 'communities' => $communities, 'users' => $users]);
    }
}
