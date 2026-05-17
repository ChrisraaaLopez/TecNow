<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create(Request $request)
    {
        // Solo mostrar comunidades a las que el usuario pertenece (o todas si es admin)
        if (Auth::user()->rol === 'admin') {
            $communities = Community::orderBy('tipo')->orderBy('name')->get();
        } else {
            $communities = Auth::user()->communities()->orderBy('tipo')->orderBy('name')->get();
        }

        $selectedCommunityId = $request->query('community_id');
        return view('posts.create', compact('communities', 'selectedCommunityId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'community_id' => 'nullable|exists:communities,id',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Validaciones de comunidad
        if (!empty($validated['community_id'])) {
            $community = Community::find($validated['community_id']);

            // Bloquear avisos para no-admins
            if ($community?->isAvisos() && Auth::user()->rol !== 'admin') {
                return back()->withInput()->with('error', 'Solo los administradores pueden publicar en avisos institucionales.');
            }

            // Bloquear si el usuario no es miembro de la comunidad (excepto admins)
            if (Auth::user()->rol !== 'admin') {
                $esMiembro = $community->users()->where('user_id', Auth::id())->exists();
                if (!$esMiembro) {
                    return back()->withInput()->with('error', 'Debes unirte a la comunidad antes de publicar en ella.');
                }
            }
        }

        // Guardar imagen si se subió
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'image'   => $imagePath,
        ]);

        if (!empty($validated['community_id'])) {
            $post->communities()->attach($validated['community_id']);
            return redirect()->route('communities.show', Community::find($validated['community_id']))
                ->with('success', 'Publicación creada exitosamente.');
        }

        return redirect()->route('dashboard')->with('success', 'Publicación creada exitosamente.');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            abort(403);
        }

        $communities = Community::orderBy('tipo')->orderBy('name')->get();
        $selectedCommunityId = $post->communities()->first()?->id;
        return view('posts.edit', compact('post', 'communities', 'selectedCommunityId'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $imagePath = $post->image;

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        if ($request->boolean('remove_image') && $post->image) {
            Storage::disk('public')->delete($post->image);
            $imagePath = null;
        }

        $post->update([
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'image'   => $imagePath,
        ]);

        return back()->with('success', 'Publicación actualizada.');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id() && Auth::user()->rol !== 'admin') {
            abort(403);
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('success', 'Publicación eliminada.');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'communities', 'votes']);
        $communities = \App\Models\Community::withCount('users')->get();

        // Comentarios de primer nivel con sus replies y votos
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'votes', 'replies.user', 'replies.votes'])
            ->latest()
            ->get();

        $stats = [
            'miembros'      => \App\Models\User::where('activo', true)->count(),
            'publicaciones' => Post::whereDate('created_at', today())->count(),
            'comunidades'   => \App\Models\Community::count(),
        ];

        return view('posts.show', compact('post', 'communities', 'comments', 'stats'));
    }

    public function popular()
    {
        $communities = Community::withCount('users')->get();
        $posts = Post::with(['user', 'communities', 'votes', 'comments'])
            ->orderByDesc('hot_score')
            ->get();

        $stats = [
            'miembros'      => \App\Models\User::where('activo', true)->count(),
            'publicaciones' => Post::whereDate('created_at', today())->count(),
            'comunidades'   => Community::count(),
        ];

        return view('popular', compact('communities', 'posts', 'stats'));
    }

    public function trending()
    {
        $communities = Community::withCount('users')->get();
        $posts = Post::with(['user', 'communities', 'votes', 'comments'])
            ->where('created_at', '>=', now()->subHours(48))
            ->orderByDesc('hot_score')
            ->get();

        $stats = [
            'miembros'      => \App\Models\User::where('activo', true)->count(),
            'publicaciones' => Post::whereDate('created_at', today())->count(),
            'comunidades'   => Community::count(),
        ];

        return view('popular', compact('communities', 'posts', 'stats'));
    }
}
