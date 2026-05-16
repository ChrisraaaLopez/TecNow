<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    public function json()
    {
        $notifications = Auth::user()->notifications()->latest()->take(20)->get()->map(function ($n) {
            return [
                'id'        => $n->id,
                'type'      => $n->type,
                'data'      => $n->data,
                'read_at'   => $n->read_at,
                'created_at'=> $n->created_at->toISOString(),
            ];
        });

        $unread = Auth::user()->unreadNotifications()->count();

        return response()->json(['notifs' => $notifications, 'sinLeer' => $unread]);
    }
}