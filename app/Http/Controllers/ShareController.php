<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use App\Notifications\PostCompartidoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * Compartir un post (en comunidad o en perfil personal).
     * community_id = null  →  compartir en el feed/perfil propio
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'community_id' => 'nullable|exists:communities,id',
        ]);

        $communityId = $request->community_id;

        // ── Compartir en perfil personal ──────────────────────────────────────
        if (!$communityId) {
            // Evitar duplicado en perfil
            $yaEnPerfil = Post::where('user_id', Auth::id())
                ->where('shared_from_post_id', $post->id)
                ->doesntHave('communities')
                ->exists();

            if ($yaEnPerfil) {
                return response()->json(['error' => 'Ya compartiste esta publicación en tu perfil.'], 422);
            }

            $shared = Post::create([
                'user_id'             => Auth::id(),
                'title'               => $post->title,
                'content'             => $post->content,
                'image'               => $post->image,
                'shared_from_post_id' => $post->id,
            ]);
            // Sin comunidad → aparece en el feed general y en el perfil del usuario

            if ($post->user_id !== Auth::id()) {
                $post->user->notify(new PostCompartidoNotification($shared, Auth::user()));
            }

            return response()->json([
                'success' => true,
                'message' => 'Publicación compartida en tu perfil.',
            ]);
        }

        // ── Compartir en comunidad ─────────────────────────────────────────────
        $community = Community::findOrFail($communityId);

        if (Auth::user()->rol !== 'admin') {
            $esMiembro = $community->users()->where('user_id', Auth::id())->exists();
            if (!$esMiembro) {
                return response()->json(['error' => 'Debes pertenecer a esa comunidad para compartir ahí.'], 403);
            }
        }

        $yaCompartido = Post::where('user_id', Auth::id())
            ->where('shared_from_post_id', $post->id)
            ->whereHas('communities', fn($q) => $q->where('communities.id', $community->id))
            ->exists();

        if ($yaCompartido) {
            return response()->json(['error' => 'Ya compartiste esta publicación en esa comunidad.'], 422);
        }

        $shared = Post::create([
            'user_id'             => Auth::id(),
            'title'               => $post->title,
            'content'             => $post->content,
            'image'               => $post->image,
            'shared_from_post_id' => $post->id,
        ]);
        $shared->communities()->attach($community->id);

        if ($post->user_id !== Auth::id()) {
            $post->user->notify(new PostCompartidoNotification($shared, Auth::user(), $community));
        }

        return response()->json([
            'success' => true,
            'message' => "Publicación compartida en {$community->name}.",
        ]);
    }
}
