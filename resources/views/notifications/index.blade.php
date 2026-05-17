@extends('layouts.app')

@section('content')
<x-navbar />

<div class="max-w-3xl mx-auto py-8 px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Notificaciones</h1>

    @if(auth()->user()->unreadNotifications->count())
    <form method="POST" action="{{ route('notifications.readAll') }}">
      @csrf
      <button class="text-sm px-4 py-2 rounded-lg text-white hover:scale-105 transition-transform" style="background:#1e40af">
        Marcar todas como leídas
      </button>
    </form>
    @endif
  </div>

  @if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
  @endif

  @forelse($notifications as $notification)
  @php
    $data = $notification->data;
    $leida = $notification->read_at !== null;
  @endphp
  <div class="mb-3 rounded-lg border transition-all {{ $leida ? 'bg-white border-border opacity-70' : 'bg-blue-50 border-blue-200' }}">
    <div class="flex items-start justify-between gap-4 p-4">
      <div class="flex-1">
        <p class="font-semibold text-sm">{{ $data['titulo'] ?? 'Notificación' }}</p>
        <p class="text-sm text-gray-600 mt-0.5">{{ $data['mensaje'] ?? '' }}</p>
        @if(!empty($data['url']))
        <a href="{{ $data['url'] }}" class="text-xs text-primary hover:underline mt-1 inline-block">
          Ver →
        </a>
        @endif
        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
      </div>
      @if(!$leida)
      <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
        @csrf
        <button class="text-xs text-blue-600 hover:underline whitespace-nowrap mt-1">
          Marcar leída
        </button>
      </form>
      @else
      <span class="text-xs text-gray-400 mt-1">Leída</span>
      @endif
    </div>
  </div>
  @empty
  <div class="bg-card border border-border rounded-lg p-12 text-center">
    <p class="text-3xl mb-3">🔔</p>
    <p class="text-gray-500 text-sm">No tienes notificaciones todavía.</p>
  </div>
  @endforelse

  <div class="mt-4">
    {{ $notifications->links() }}
  </div>
</div>
@endsection
