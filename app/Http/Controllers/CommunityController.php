<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function show(Community $community)
    {
        $communities = Community::withCount('users')->get();

        // Ensure the current community also has users_count
        $community->loadCount('users');

        $posts = $community->posts()
            ->with(['user', 'communities', 'votes', 'comments', 'sharedFrom.user'])
            ->latest()
            ->get();

        $isMember = $community->users()->where('user_id', Auth::id())->exists();

        return view('communities.show', compact('community', 'communities', 'posts', 'isMember'));
    }

    public function join(Community $community)
    {
        if (!$community->users()->where('user_id', Auth::id())->exists()) {
            $community->users()->attach(Auth::id(), ['role' => 'member']);
        }
        return back()->with('success', "Te uniste a {$community->name}.");
    }

    public function leave(Community $community)
    {
        $community->users()->detach(Auth::id());
        return back()->with('success', "Saliste de {$community->name}.");
    }

    public function store(Request $request)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'No tienes permiso para crear comunidades.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'tipo'        => 'nullable|in:carrera,avisos,general',
        ]);

        $community = Community::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => '💬',
            'tipo'        => $request->tipo ?? 'general',
            'slug'        => Str::slug($request->name),
            'created_by'  => Auth::id(),
        ]);

        $community->users()->attach(Auth::id(), ['role' => 'admin']);

        return back()->with('success', 'Comunidad creada exitosamente.');
    }

    public function addAdmin(Request $request, Community $community)
    {
        $isAdmin = $community->users()->where('user_id', Auth::id())->wherePivot('role', 'admin')->exists();
        if (!$isAdmin && Auth::user()->rol !== 'admin') {
            abort(403, 'No tienes permiso.');
        }

        $request->validate(['username' => 'required|exists:users,username']);

        $user = User::where('username', $request->username)->firstOrFail();
        $community->users()->syncWithoutDetaching([$user->id => ['role' => 'admin']]);

        return back()->with('success', 'Administrador añadido correctamente.');
    }
}
