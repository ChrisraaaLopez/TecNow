<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function showPublic($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $posts = $user->posts()
            ->with(['votes', 'comments', 'sharedFrom.user'])
            ->latest()
            ->get();

        $postsCount = $user->posts()->count();
        $likesCount = \App\Models\PostVote::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('vote', 1)
            ->count();
        $commentsCount = $user->comments()->count();

        return view('perfil_publico', compact('user', 'posts', 'postsCount', 'likesCount', 'commentsCount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users,username,' . Auth::id(), 'regex:/^[a-zA-Z0-9_]+$/'],
            'avatar'   => ['nullable', 'string'],
            'marco'    => ['nullable', 'string'],
        ], [
            'username.unique' => 'Ese username ya está en uso.',
            'username.regex'  => 'Solo letras, números y guión bajo.',
            'username.min'    => 'Mínimo 3 caracteres.',
        ]);

        Auth::user()->update([
            'username' => $request->username,
            'avatar'   => $request->avatar ?? Auth::user()->avatar,
            'marco'    => $request->has('marco') ? $request->marco : Auth::user()->marco,
        ]);

        return redirect()->route('perfil')->with('success', '¡Perfil actualizado!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return redirect()->route('perfil')->with('success', '¡Contraseña actualizada!');
    }
}