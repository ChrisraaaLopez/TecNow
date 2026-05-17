@extends('layouts.app')

@section('content')
<x-navbar />

{{-- BODY --}}
<div x-data="{ showModal: false, showCommunityModal: false, showAddAdminModal: false, selectedCommunityId: null }"
  class="flex max-w-[1400px] mx-auto">

  {{-- SIDEBAR --}}
  <aside
    class="hidden lg:block w-64 border-r border-border bg-sidebar px-4 py-6 sticky top-[73px] h-[calc(100vh-73px)] overflow-y-auto">
    <nav class="space-y-1 mb-6">
      <a href="{{ route('dashboard') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-secondary text-secondary-foreground' : 'hover:bg-sidebar-accent' }} transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span>Inicio</span>
      </a>
      <a href="{{ route('popular') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('popular') ? 'bg-secondary text-secondary-foreground' : 'hover:bg-sidebar-accent' }} transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </svg>
        <span>Popular</span>
      </a>
    </nav>
    <div class="border-t border-sidebar-border pt-4">
      <div class="flex items-center justify-between mb-3 px-3">
        <p class="text-sm text-muted-foreground">Mis Comunidades</p>
        <button class="text-primary hover:text-blue-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </button>
      </div>
      <div class="space-y-1">
        @php $avisosPop = $communities->where('tipo', 'avisos')->first(); @endphp
        @if($avisosPop)
        <a href="{{ route('communities.show', $avisosPop) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
          <x-community-icon :community="$avisosPop" size="sm" />
          <div class="flex-1 min-w-0">
            <p class="text-sm truncate font-medium text-blue-600">{{ $avisosPop->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $avisosPop->users_count }} miembros</p>
          </div>
        </a>
        @endif
        @foreach ($communities->where('tipo', 'carrera') as $c)
        <a href="{{ route('communities.show', $c) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
          <x-community-icon :community="$c" size="sm" />
          <div class="flex-1 min-w-0">
            <p class="text-sm truncate">{{ $c->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $c->users_count }} miembros</p>
          </div>
        </a>
        @endforeach
      </div>
      <a href="{{ route('dashboard') }}"
        class="block w-full mt-3 px-3 py-2 text-sm text-primary hover:bg-sidebar-accent rounded-lg transition-colors text-center">
        Ver todas las comunidades
      </a>
      @if (Auth::user()->global_role === 'admin')
      <button @click="showCommunityModal = true"
        class="w-full mt-3 px-3 py-2 text-sm bg-primary text-white hover:bg-blue-700 rounded-lg transition-colors">
        + Crear Comunidad
      </button>
      @endif
    </div>
  </aside>

  {{-- MAIN --}}
  <main class="flex-1 px-4 lg:px-6 py-6">
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
      <div class="flex gap-2">
        <a href="{{ route('popular') }}"
          class="px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('popular') ? 'bg-primary text-white' : 'bg-card hover:bg-muted text-foreground border border-border' }}">
          Popular
        </a>
        <a href="{{ route('dashboard') }}"
          class="px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'bg-card hover:bg-muted text-foreground border border-border' }}">
          Reciente
        </a>
        <a href="{{ route('trending') }}"
          class="px-4 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('trending') ? 'bg-primary text-white' : 'bg-card hover:bg-muted text-foreground border border-border' }}">
          Trending
        </a>
      </div>
      <a href="{{ route('posts.create') }}">
        <button
          class="hover:scale-105 transition-transform text-sm flex items-center gap-2 px-4 py-2 rounded-lg font-medium"
          style="border: 3px solid #1e40af; background-color: #1e40af; color: white;">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Crear publicación</span>
        </button>
      </a>
    </div>

    {{-- Posts --}}
    <div class="space-y-4">
      @forelse($posts as $post)
      <div class="bg-card border border-border rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <a href="{{ route('perfil.show', $post->user->username) }}"
              class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden relative border border-gray-300 block hover:opacity-80 transition-opacity">
              <img src="{{ asset('avatars/' . $post->user->avatar) }}" class="w-full h-full object-cover">
              @if ($post->user->marco)
              <img src="{{ asset('marcos/' . $post->user->marco) }}"
                class="absolute inset-0 w-full h-full object-cover z-10" />
              @endif
            </a>
            <div>
              <div>
                <a href="{{ route('perfil.show', $post->user->username) }}"
                  class="text-sm font-semibold hover:text-primary transition-colors">{{ $post->user->name }}</a>
                @if ($post->communities->isNotEmpty())
                <span class="text-gray-500 font-normal text-xs">en</span>
                {{ $post->communities->first()->name }}
                @endif
              </div>
              <p class="text-xs text-muted-foreground">{{ $post->created_at->diffForHumans() }}</p>
            </div>
          </div>

          {{-- Acciones autor/admin --}}
          @php
          $karma = $post->votes->sum('vote');
          $userVote = $post->votes->where('user_id', Auth::id())->first()?->vote;
          $isAuthor = $post->user_id === Auth::id();
          $community = $post->communities->first();
          $isForumAdmin = $community
          ? $community
          ->users()
          ->where('user_id', Auth::id())
          ->wherePivot('role', 'admin')
          ->exists()
          : false;
          $isGlobalAdmin = Auth::user()->global_role === 'admin';
          @endphp

          @if ($isAuthor || $isForumAdmin || $isGlobalAdmin)
          <div class="flex items-center gap-2">
            <a href="{{ route('posts.edit', $post) }}"
              class="relative group p-1.5 rounded-lg text-blue-500 hover:bg-blue-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <span
                class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Editar
              </span>
            </a>

            <form action="{{ route('posts.destroy', $post) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" onclick="return confirm('¿Eliminar esta publicación?')"
                class="relative group p-1.5 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  Eliminar
                </span>
              </button>
            </form>
          </div>
          @endif
        </div>

        {{-- Contenido --}}
        <a href="{{ route('posts.show', $post) }}">
          <h3 class="text-lg font-bold mb-2 hover:text-primary transition-colors">{{ $post->title }}</h3>
        </a>
        <p class="text-sm text-foreground whitespace-pre-wrap mb-4">{{ $post->content }}</p>

        {{-- Karma --}}
        <div class="flex items-center gap-1 mt-2"
          x-data="{
                                karma: {{ $karma }},
                                userVote: {{ $userVote ?? 'null' }},
                                loading: false,
                                async vote(value) {
                                    if (this.loading) return;
                                    this.loading = true;
                                    try {
                                        const res = await fetch('{{ route('posts.vote', $post) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            },
                                            body: JSON.stringify({ vote: value }),
                                        });
                                        const data = await res.json();
                                        this.karma = data.karma;
                                        this.userVote = data.user_vote;

                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">

          <button type="button" @click="vote(1)"
            :disabled="loading"
            :class="userVote === 1
                                        ? 'text-orange-400 bg-orange-500/10'
                                        : 'text-gray-400 hover:text-orange-400 hover:bg-orange-500/10'"
            class="relative group p-1.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
              Upvote
            </span>
          </button>

          <span class="text-sm font-semibold min-w-[2rem] text-center"
            :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-400'"
            x-text="karma">
          </span>

          <button type="button" @click="vote(-1)"
            :disabled="loading"
            :class="userVote === -1
                                        ? 'text-blue-400 bg-blue-500/10'
                                        : 'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'"
            class="relative group p-1.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
              Downvote
            </span>
          </button>
        </div>
      </div>
      @empty
      <div class="bg-card border border-border rounded-lg p-12 text-center">
        <svg class="w-12 h-12 text-muted-foreground mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
        </svg>
        <p class="text-muted-foreground text-sm">No hay publicaciones todavía.</p>
        <p class="text-muted-foreground text-xs mt-1">¡Sé el primero en publicar algo!</p>
      </div>
      @endforelse
    </div>
  </main>

  {{-- ASIDE derecho --}}
  <aside class="hidden xl:block w-80 px-6 py-6">
    <div class="bg-card border border-border rounded-lg p-6 sticky top-24">
      <div class="mb-4">
        <h3 class="mb-2">Bienvenido a TecNow</h3>
        <p class="text-sm text-muted-foreground">Tu comunidad, tu voz, en tiempo real.</p>
      </div>
      <div class="space-y-3 mb-6 bg-secondary rounded-lg p-4">
        @foreach ([['📝', 'Comparte ideas y recursos académicos'], ['💬', 'Comenta y participa en debates'], ['⬆️', 'Vota el mejor contenido'], ['👥', 'Únete a comunidades']] as [$icon, $text])
        <div class="flex items-start gap-3 text-sm">
          <span class="text-xl">{{ $icon }}</span>
          <span class="text-muted-foreground">{{ $text }}</span>
        </div>
        @endforeach
      </div>
      <div class="border-t border-border pt-4 mb-6">
        <h4 class="mb-3 text-sm">Reglas de la comunidad</h4>
        <ul class="text-xs text-muted-foreground space-y-2">
          @foreach (['Respeto entre todos los miembros', 'No spam ni contenido inapropiado', 'Usa las comunidades correctas', 'Verifica información antes de compartir'] as $rule)
          <li class="flex items-start gap-2">
            <span class="text-primary mt-0.5">•</span>
            <span>{{ $rule }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      <div class="border-t border-border pt-4">
        <h4 class="text-sm mb-2">Estadísticas</h4>
        <div class="space-y-2 text-sm">
          @foreach ([['Miembros activos', number_format($stats['miembros'])], ['Publicaciones hoy', number_format($stats['publicaciones'])], ['Comunidades', number_format($stats['comunidades'])]] as [$label, $val])
          <div class="flex justify-between">
            <span class="text-muted-foreground">{{ $label }}</span>
            <span class="font-medium text-primary">{{ $val }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </aside>
</div>
@endsection