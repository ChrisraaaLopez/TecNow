@extends('layouts.app')

@section('content')
<x-navbar />

{{-- BODY --}}
<div x-data="{ showCommunityModal: false, showAddAdminModal: false, selectedCommunityId: null }"
  class="flex max-w-[1400px] mx-auto">

  {{-- SIDEBAR IZQUIERDO --}}
  <aside class="hidden lg:block w-64 border-r border-border bg-sidebar px-4 py-6 sticky top-[73px] h-[calc(100vh-73px)] overflow-y-auto">
    <nav class="space-y-1 mb-6">
      <a href="{{ route('dashboard') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span>Inicio</span>
      </a>
      <a href="{{ route('dashboard', ['sort' => 'popular']) }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
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
      </div>
      <div class="space-y-1">
        @php $avisosSidebar = $communities->where('tipo', 'avisos')->first(); @endphp
        @if($avisosSidebar)
        <a href="{{ route('communities.show', $avisosSidebar) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
          <x-community-icon :community="$avisosSidebar" size="sm" />
          <div class="flex-1 min-w-0">
            <p class="text-sm truncate font-medium text-blue-600">{{ $avisosSidebar->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $avisosSidebar->users_count }} miembros</p>
          </div>
        </a>
        @endif
        @foreach ($communities->where('tipo', 'carrera') as $sidebarCommunity)
        <a href="{{ route('communities.show', $sidebarCommunity) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
          <x-community-icon :community="$sidebarCommunity" size="sm" />
          <div class="flex-1 min-w-0">
            <p class="text-sm truncate">{{ $sidebarCommunity->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $sidebarCommunity->users_count }} miembros</p>
          </div>
        </a>
        @endforeach
      </div>
      <a href="{{ route('dashboard') }}" class="block w-full mt-3 px-3 py-2 text-sm text-primary hover:bg-sidebar-accent rounded-lg transition-colors text-center">
        Ver todas las comunidades
      </a>
      @if(Auth::user()->global_role === 'admin')
      <button @click="showCommunityModal = true"
        class="w-full mt-3 px-3 py-2 text-sm bg-primary text-white hover:bg-blue-700 rounded-lg transition-colors">
        + Crear Comunidad
      </button>
      @endif
    </div>
  </aside>

  {{-- MAIN --}}
  <main class="flex-1 px-4 lg:px-6 py-6">

    {{-- Breadcrumb / volver --}}
    <a href="{{ route('dashboard') }}"
      class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-blue-400 transition-colors mb-6">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Ir al Inicio
    </a>

    {{-- Post --}}
    @php
    $karma = $post->votes->sum('vote');
    $userVote = $post->votes->where('user_id', Auth::id())->first()?->vote;
    $isAuthor = $post->user_id === Auth::id();
    $community = $post->communities->first();
    $isForumAdmin = $community
    ? $community->users()->where('user_id', Auth::id())->wherePivot('role', 'admin')->exists()
    : false;
    $isGlobalAdmin = Auth::user()->global_role === 'admin';
    @endphp

    <div class="bg-card border border-border rounded-lg p-6" x-data="{ showReportPost: false }">

      {{-- Autor + acciones --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <a href="{{ route('perfil.show', $post->user->username) }}"
            class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden relative border border-gray-300 flex-shrink-0 block hover:opacity-80 transition-opacity">
            <img src="{{ asset('avatars/'.$post->user->avatar) }}" class="w-full h-full object-cover">
            @if($post->user->marco)
            <img src="{{ asset('marcos/'.$post->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
            @endif
          </a>
          <div>
            <p class="text-sm font-semibold">
              <a href="{{ route('perfil.show', $post->user->username) }}"
                class="hover:text-primary transition-colors {{ $post->user->rol === 'admin' ? 'text-yellow-500' : '' }}">
                {{ $post->user->name }}
                @if($post->user->rol === 'admin')
                <svg class="inline w-3.5 h-3.5 text-yellow-400 ml-0.5 align-middle" viewBox="0 0 24 24" fill="currentColor"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @endif
              </a>
              @if($post->communities->isNotEmpty())
              <span class="text-gray-500 font-normal text-xs">en {{ $post->communities->first()->name }}</span>
              @endif
            </p>
            <p class="text-xs text-muted-foreground">{{ $post->created_at->diffForHumans() }}</p>
          </div>
        </div>

        {{-- Acciones: editar/eliminar (autor/admin) + reportar (otros) --}}
        <div class="flex items-center gap-2">

          @if($isAuthor || $isForumAdmin || $isGlobalAdmin)
          @if($isAuthor)
          <a href="{{ route('posts.edit', $post) }}"
            class="relative group p-1.5 rounded-lg text-blue-500 hover:bg-blue-500/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                         opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
              Editar
            </span>
          </a>
          @endif
          <form action="{{ route('posts.destroy', $post) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar esta publicación?')"
              class="relative group p-1.5 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                           opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Eliminar
              </span>
            </button>
          </form>
          @endif

          {{-- Reportar (solo si NO eres el autor) --}}
          @if(!$isAuthor)
          <button @click="showReportPost = !showReportPost"
            class="relative group p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors"
            title="Reportar publicación">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H9.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
            </svg>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                         opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
              Reportar
            </span>
          </button>
          @endif

        </div>
      </div>

      {{-- Formulario de reporte del post (se despliega al pulsar el botón 🚩) --}}
      @if(!$isAuthor)
      <div x-show="showReportPost" x-cloak class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <form method="POST" action="{{ route('reportes.store') }}" class="flex flex-col gap-2">
          @csrf
          <input type="hidden" name="post_id" value="{{ $post->id }}">
          <select name="motivo" required
            class="w-full px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-900 bg-white">
            <option value="">Motivo del reporte...</option>
            <option value="spam">Spam</option>
            <option value="contenido inapropiado">Contenido inapropiado</option>
            <option value="acoso">Acoso</option>
            <option value="desinformación">Desinformación</option>
            <option value="otro">Otro</option>
          </select>
          <textarea name="descripcion" rows="2" placeholder="Descripción opcional..."
            class="w-full px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-900 bg-white resize-none"></textarea>
          <div class="flex gap-2 justify-end">
            <button type="button" @click="showReportPost = false"
              class="text-xs px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-100">
              Cancelar
            </button>
            <button type="submit"
              class="text-xs px-3 py-1.5 rounded text-white bg-red-500 hover:bg-red-600">
              Enviar reporte
            </button>
          </div>
        </form>
      </div>
      @endif

      {{-- Indicador de post compartido --}}
      @if($post->shared_from_post_id)
      <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-3 pb-3 border-b border-border">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
        </svg>
        Compartido por {{ $post->user->name }}
        @if($post->sharedFrom)
        · <a href="{{ route('posts.show', $post->sharedFrom) }}" class="text-blue-500 hover:underline">Ver publicación original</a>
        @endif
      </div>
      @endif

      {{-- Título del Post--}}
      <h1 class="text-xl font-bold mb-3">{{ $post->title }}</h1>

        {{-- Imágenes del Post --}}
        @php $imageUrls = $post->image_urls; @endphp
        @if(count($imageUrls) === 1)
          <div class="mt-4 rounded-lg overflow-hidden border border-border bg-black" style="max-height:480px">
            <div class="relative w-full" style="max-height:480px; height:480px">
              <img src="{{ $imageUrls[0] }}" alt="" class="absolute inset-0 w-full h-full object-cover scale-110 blur-md opacity-50 pointer-events-none select-none" />
              <img src="{{ $imageUrls[0] }}" alt="" class="relative w-full h-full object-contain z-10" />
            </div>
          </div>
        @elseif(count($imageUrls) > 1)
          {{-- Carrusel --}}
          <div class="mt-4 rounded-lg overflow-hidden border border-border relative"
               x-data="{ current: 0, total: {{ count($imageUrls) }} }">

            {{-- Imágenes --}}
            @foreach($imageUrls as $i => $url)
            <div x-show="current === {{ $i }}" x-cloak class="relative bg-black" style="height:480px">
              <img src="{{ $url }}" alt="" class="absolute inset-0 w-full h-full object-cover scale-110 blur-md opacity-50 pointer-events-none select-none" />
              <img src="{{ $url }}" alt="Imagen {{ $i + 1 }}" class="relative w-full h-full object-contain z-10" />
            </div>
            @endforeach

            {{-- Flecha anterior --}}
            <button type="button"
              @click="current = (current - 1 + total) % total"
              class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full p-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>

            {{-- Flecha siguiente --}}
            <button type="button"
              @click="current = (current + 1) % total"
              class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full p-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
              </svg>
            </button>

            {{-- Puntos indicadores --}}
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
              @foreach($imageUrls as $i => $url)
              <button type="button" @click="current = {{ $i }}"
                :class="current === {{ $i }}
                  ? 'bg-white w-2.5 h-2.5'
                  : 'bg-white/50 hover:bg-white/80 w-2 h-2'"
                class="rounded-full transition-all duration-200">
              </button>
              @endforeach
            </div>

          </div>
        @endif

        {{-- Contenido del Post --}}
      <p class="text-sm text-foreground whitespace-pre-wrap leading-relaxed">{{ $post->content }}</p>


        {{-- Contenedor de Karma y Comentarios --}}
        <div id="vote-bar-{{ $post->id }}" class="flex items-center gap-1 mt-2"
             x-data="{
                            karma: {{ $karma }},
                            userVote: {{ $userVote ?? 'null' }},
                            commentCount: {{ $post->comments->count() }},
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
                        }"
            >
            <div class="flex items-center bg-gray-100 rounded-lg">
                {{-- Upvote --}}
                <button type="button" @click="vote(1)" :disabled="loading"
                        :class="userVote === 1 ?
                                    'text-orange-400 bg-orange-500/10' :
                                    'text-gray-400 hover:text-orange-400 hover:bg-orange-500/10'"
                        class="relative group p-1.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" :fill="userVote === 1 ? 'currentColor' : 'none'"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 15l7-7 7 7" />
                    </svg>
                    <span
                        class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                                             opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                    Upvote
                    </span>
                </button>

                {{-- Contador --}}
                <span class="text-sm font-semibold min-w-[2rem] text-center"
                      :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-400'"
                      x-text="karma">
                </span>

                {{-- Downvote --}}
                <button type="button" @click="vote(-1)" :disabled="loading"
                        :class="userVote === -1 ?
                                    'text-blue-400 bg-blue-500/10' :
                                    'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'"
                        class="relative group p-1.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" :fill="userVote === -1 ? 'currentColor' : 'none'"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7" />
                    </svg>
                    <span
                        class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                                             opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                    Downvote
                    </span>
                </button>
            </div>

            {{-- Separador --}}
            <span class="text-gray-700 mx-1">·</span>

            {{-- Contador de comentarios --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="#comentarios"
                   class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm" x-text="commentCount"></span>
                </a>
            </div>

            {{-- Contador de imágenes --}}
            @if(count($imageUrls) > 0)
            <span class="text-gray-700 mx-1">·</span>
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="#"
                   class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors"
                   @click.prevent="document.querySelector('[x-data*=current]')?.scrollIntoView({ behavior: 'smooth' })">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm">{{ count($imageUrls) }}</span>
                </a>
            </div>
            @endif

            {{-- Botón compartir --}}
            <x-share-button :post="$post" />
        </div>
    </div>

      {{-- SECCIÓN DE COMENTARIOS --}}
      <div id="comentarios" class="mt-4 bg-card border border-border rounded-lg p-6">

          {{-- Encabezado --}}
          <h2 class="text-sm font-semibold mb-4 text-muted-foreground uppercase tracking-wide">
              {{ $comments->count() + $comments->sum(fn($c) => $c->replies->count()) }} comentarios
          </h2>

          {{-- Formulario nuevo comentario --}}
          <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-6">
              @csrf
              <div class="flex gap-3">
                  <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-1">
                      <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" />
                      @if(Auth::user()->marco)
                          <img src="{{ asset('marcos/' . Auth::user()->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
                      @endif
                  </div>
                  <div class="flex-1">
                    <textarea
                        name="content"
                        rows="2"
                        placeholder="Escribe un comentario..."
                        required
                        class="w-full px-3 py-2 rounded-lg border border-border border-gray-200 bg-gray-100 text-gray-700 placeholder-gray-400
                               focus:outline-none focus:border-primary transition-colors resize-none text-sm"
                    ></textarea>
                      <div class="flex justify-end mt-2">
                          <button type="submit"
                                  class="px-4 py-1.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm rounded-lg hover:bg-blue-700 hover:text-white transition-colors">
                              Comentar
                          </button>
                      </div>
                  </div>
              </div>
          </form>

          {{-- Lista de comentarios --}}
          <div class="space-y-4" id="comments-list">
              @forelse($comments as $comment)
                  @php
                      $commentKarma    = $comment->votes->sum('vote');
                      $commentUserVote = $comment->votes->where('user_id', Auth::id())->first()?->vote;
                  @endphp

                  <div class="flex gap-3">
                      {{-- Avatar --}}
                      <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-1">
                          <img src="{{ asset('avatars/' . $comment->user->avatar) }}" class="w-full h-full object-cover" />
                          @if($comment->user->marco)
                              <img src="{{ asset('marcos/' . $comment->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
                          @endif
                      </div>

                      <div class="flex-1 min-w-0">
                          {{-- Autor y fecha --}}
                          <div class="flex items-center gap-2 mb-1">
                              <span class="text-sm font-semibold">{{ $comment->user->name }}</span>
                              <span class="text-xs text-muted-foreground">{{ $comment->created_at->diffForHumans() }}</span>
                          </div>

                          {{-- Contenido --}}
                          <p class="text-sm text-foreground whitespace-pre-wrap">{{ $comment->content }}</p>

                          {{-- Acciones del comentario --}}
                          <div class="flex items-center gap-3 mt-2">

                              {{-- Karma del comentario --}}
                              <div class="flex items-center gap-1"
                                   id="comment-vote-bar-{{ $comment->id }}"
                                   x-data="{ karma: {{ $commentKarma }}, userVote: {{ $commentUserVote ?? 'null' }}, loading: false }">
                                  <button type="button"
                                          @click="window.voteComment({{ $comment->id }}, '{{ route('comments.vote', $comment) }}', 1)"
                                          :disabled="loading"
                                          :class="userVote === 1 ? 'text-orange-400' : 'text-gray-500 hover:text-orange-400'"
                                          class="p-1 rounded transition-colors">
                                      <svg class="w-3.5 h-3.5" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                      </svg>
                                  </button>
                                  <span class="text-xs font-semibold min-w-[1.2rem] text-center"
                                        :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-500'"
                                        x-text="karma"></span>
                                  <button type="button"
                                          @click="window.voteComment({{ $comment->id }}, '{{ route('comments.vote', $comment) }}', -1)"
                                          :disabled="loading"
                                          :class="userVote === -1 ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400'"
                                          class="p-1 rounded transition-colors">
                                      <svg class="w-3.5 h-3.5" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                      </svg>
                                  </button>
                              </div>

                                {{-- Botón responder + formulario unificados --}}
                                <div x-data="{ open: false }">

                                    {{-- Botón responder --}}
                                    <button type="button"
                                            @click="open = !open"
                                            class="text-xs text-gray-500 hover:text-primary transition-colors">
                                        Responder
                                    </button>

                                    {{-- Formulario de respuesta --}}
                                    <div x-show="open" x-cloak class="mt-2">
                                        <form action="{{ route('comments.store', $post) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                                            <div class="flex gap-2">
                                                {{-- Icono del perfil --}}
                                                <div class="w-6 h-6 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-1">
                                                    <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" />
                                                </div>
                                                <div class="flex-1">
                                                    <textarea
                                                        name="content"
                                                        rows="3"
                                                        x-ref="replyInput"
                                                        x-effect="if(open) $nextTick(() => $refs.replyInput.focus())"
                                                        placeholder="Escribe una respuesta..."
                                                        required
                                                        class="w-full md:w-[25rem] px-3 py-2 rounded-lg border border-border border-gray-200 bg-gray-100 placeholder-gray-400
                                                        focus:outline-none focus:border-primary transition-colors resize-none text-sm"
                                                    ></textarea>
                                                    <div class="flex justify-end gap-2 mt-1">
                                                        <button type="button" @click="open = false"
                                                                class="px-3 py-1 text-xs text-gray-400 hover:text-red-400 transition-colors">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit"
                                                                class="px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 text-xs rounded-lg hover:bg-blue-700 hover:text-white transition-colors">
                                                            Responder
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                              {{-- Reportar comentario (solo si no eres el autor) --}}
                              @if($comment->user_id !== Auth::id())
                              <div x-data="{ showReportComment: false }" class="relative">
                                  <button @click="showReportComment = !showReportComment" type="button"
                                          class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                                      Reportar
                                  </button>
                                  <div x-show="showReportComment" x-cloak
                                       class="absolute left-0 top-6 z-50 w-64 p-3 bg-white border border-red-200 rounded-lg shadow-lg">
                                      <form method="POST" action="{{ route('reportes.store') }}" class="flex flex-col gap-2">
                                          @csrf
                                          <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                          <select name="motivo" required class="w-full px-2 py-1.5 rounded border border-gray-300 text-xs text-gray-900 bg-white">
                                              <option value="">Motivo...</option>
                                              <option value="spam">Spam</option>
                                              <option value="contenido inapropiado">Contenido inapropiado</option>
                                              <option value="acoso">Acoso</option>
                                              <option value="otro">Otro</option>
                                          </select>
                                          <div class="flex gap-2 justify-end">
                                              <button type="button" @click="showReportComment = false" class="text-xs px-2 py-1 rounded border border-gray-300 bg-white hover:bg-gray-100">Cancelar</button>
                                              <button type="submit" class="text-xs px-2 py-1 rounded text-white bg-red-500 hover:bg-red-600">Enviar</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                              @endif

                              {{-- Eliminar comentario --}}
                              @if($comment->user_id === Auth::id() || Auth::user()->global_role === 'admin')
                                  <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit"
                                              onclick="return confirm('¿Eliminar este comentario?')"
                                              class="text-xs text-red-500 hover:text-red-400 transition-colors">
                                          Eliminar
                                      </button>
                                  </form>
                              @endif
                          </div>

                          {{-- Formulario de respuesta --}}
                          <div x-data="{ open: false }" class="mt-2">
                              <button type="button" @click="open = !open"
                                      class="text-xs text-gray-500 hover:text-primary transition-colors hidden">
                              </button>
                              <div x-show="open" x-cloak class="mt-2">
                                  <form action="{{ route('comments.store', $post) }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                                      <div class="flex gap-2">
                                          <div class="w-6 h-6 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-1">
                                              <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" />
                                          </div>
                                          <div class="flex-1">
                                            <textarea
                                                name="content"
                                                rows="2"
                                                x-ref="replyInput"
                                                placeholder="Escribe una respuesta..."
                                                required
                                                class="w-full px-3 py-2 rounded-lg border border-border bg-gray-900 text-white placeholder-gray-500
                                                       focus:outline-none focus:border-primary transition-colors resize-none text-sm"
                                            ></textarea>
                                              <div class="flex justify-end gap-2 mt-1">
                                                  <button type="button" @click="open = false"
                                                          class="px-3 py-1 text-xs text-gray-400 hover:text-white transition-colors">
                                                      Cancelar
                                                  </button>
                                                  <button type="submit"
                                                          class="px-3 py-1 bg-primary text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                                      Responder
                                                  </button>
                                              </div>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                          </div>

                          {{-- Respuestas (nivel 2) --}}
                          @if($comment->replies->isNotEmpty())
                              <div class="mt-3 space-y-3 border-l-2 border-border pl-4">
                                  @foreach($comment->replies as $reply)
                                      @php
                                          $replyKarma    = $reply->votes->sum('vote');
                                          $replyUserVote = $reply->votes->where('user_id', Auth::id())->first()?->vote;
                                      @endphp

                                      <div class="flex gap-3">
                                          <div class="w-7 h-7 rounded-full overflow-hidden border border-gray-600 relative flex-shrink-0 mt-0.5">
                                              <img src="{{ asset('avatars/' . $reply->user->avatar) }}" class="w-full h-full object-cover" />
                                              @if($reply->user->marco)
                                                  <img src="{{ asset('marcos/' . $reply->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
                                              @endif
                                          </div>
                                          <div class="flex-1 min-w-0">
                                              <div class="flex items-center gap-2 mb-1">
                                                  <span class="text-sm font-semibold">{{ $reply->user->name }}</span>
                                                  <span class="text-xs text-muted-foreground">{{ $reply->created_at->diffForHumans() }}</span>
                                              </div>
                                              <p class="text-sm text-foreground whitespace-pre-wrap">{{ $reply->content }}</p>

                                              {{-- Acciones reply --}}
                                              <div class="flex items-center gap-3 mt-2">
                                                  {{-- Karma reply --}}
                                                  <div class="flex items-center gap-1"
                                                       id="comment-vote-bar-{{ $reply->id }}"
                                                       x-data="{ karma: {{ $replyKarma }}, userVote: {{ $replyUserVote ?? 'null' }}, loading: false }">
                                                      <button type="button"
                                                              @click="window.voteComment({{ $reply->id }}, '{{ route('comments.vote', $reply) }}', 1)"
                                                              :disabled="loading"
                                                              :class="userVote === 1 ? 'text-orange-400' : 'text-gray-500 hover:text-orange-400'"
                                                              class="p-1 rounded transition-colors">
                                                          <svg class="w-3.5 h-3.5" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                          </svg>
                                                      </button>
                                                      <span class="text-xs font-semibold min-w-[1.2rem] text-center"
                                                            :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-500'"
                                                            x-text="karma"></span>
                                                      <button type="button"
                                                              @click="window.voteComment({{ $reply->id }}, '{{ route('comments.vote', $reply) }}', -1)"
                                                              :disabled="loading"
                                                              :class="userVote === -1 ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400'"
                                                              class="p-1 rounded transition-colors">
                                                          <svg class="w-3.5 h-3.5" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                          </svg>
                                                      </button>
                                                  </div>

                                                  {{-- Eliminar reply --}}
                                                  @if($reply->user_id === Auth::id() || Auth::user()->global_role === 'admin')
                                                      <form action="{{ route('comments.destroy', $reply) }}" method="POST">
                                                          @csrf
                                                          @method('DELETE')
                                                          <button type="submit"
                                                                  onclick="return confirm('¿Eliminar esta respuesta?')"
                                                                  class="text-xs text-red-500 hover:text-red-400 transition-colors">
                                                              Eliminar
                                                          </button>
                                                      </form>
                                                  @endif
                                              </div>
                                          </div>
                                      </div>
                                  @endforeach
                              </div>
                          @endif

                      </div>
                  </div>

                  {{-- Separador entre comentarios --}}
                  @unless($loop->last)
                      <div class="border-t border-border"></div>
                  @endunless

              @empty
                  <div class="text-center py-8">
                      <svg class="w-10 h-10 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                      </svg>
                      <p class="text-sm text-muted-foreground">No hay comentarios todavía.</p>
                      <p class="text-xs text-gray-500 mt-1">¡Sé el primero en comentar!</p>
                  </div>
              @endforelse
          </div>
      </div>

  </main>

  {{-- ASIDE DERECHO --}}
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

{{-- MODAL crear comunidad --}}
<div x-show="showCommunityModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60" @click="showCommunityModal = false"></div>
  <div class="relative bg-card border border-border rounded-lg p-6 w-full max-w-lg mx-4 z-10">
    <div class="flex items-center justify-between mb-4">
      <h3>Crear Comunidad</h3>
      <button @click="showCommunityModal = false" class="text-muted-foreground hover:text-foreground">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <form action="{{ route('communities.store') }}" method="POST" class="space-y-4">
      @csrf
      <input type="text" name="name" placeholder="Nombre del foro" required
        class="w-full px-3 py-2 rounded-lg border border-border bg-muted focus:border-primary focus:outline-none text-gray-900" />
      <textarea rows="2" name="description" placeholder="Descripción breve"
        class="w-full px-3 py-2 rounded-lg border border-border bg-muted focus:border-primary focus:outline-none resize-none text-gray-900"></textarea>
      <div class="flex justify-end gap-3">
        <button type="button" @click="showCommunityModal = false"
          class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors text-sm text-gray-900 bg-white">
          Cancelar
        </button>
        <button type="submit"
          class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-700 transition-colors text-sm">
          Crear
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL añadir admin --}}
<div x-show="showAddAdminModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60" @click="showAddAdminModal = false"></div>
  <div class="relative bg-card border border-border rounded-lg p-6 w-full max-w-sm mx-4 z-10">
    <h3>Añadir Administrador al Foro</h3>
    <form :action="'/communities/' + selectedCommunityId + '/add-admin'" method="POST" class="space-y-4 mt-4">
      @csrf
      <input type="text" name="username" placeholder="Username del usuario" required
        class="w-full px-3 py-2 rounded-lg border border-border bg-muted text-gray-900 focus:outline-none focus:border-primary" />
      <div class="flex justify-end gap-3">
        <button type="button" @click="showAddAdminModal = false"
          class="px-4 py-2 border rounded-lg text-sm bg-white text-black hover:bg-gray-100">Cancelar</button>
        <button type="submit"
          class="px-4 py-2 bg-primary text-white hover:bg-blue-700 rounded-lg text-sm">Añadir</button>
      </div>
    </form>
  </div>
</div>

<script>
// Función global para votar en comentarios — usa Alpine.$data() para garantizar
// actualización reactiva (el patrón async inline pierde contexto en Alpine).
window.voteComment = function(commentId, url, value) {
    var el = document.getElementById('comment-vote-bar-' + commentId);
    if (!el || !window.Alpine || !window.Alpine.$data) return;
    var d = window.Alpine.$data(el);
    if (d.loading) return;
    d.loading = true;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ vote: value }),
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(function(data) {
        d.karma    = data.karma;
        d.userVote = (data.user_vote !== undefined && data.user_vote !== null) ? data.user_vote : null;
    })
    .catch(function(e) { console.error('[CommentVote]', e); })
    .finally(function() { d.loading = false; });
};

(function() {
    const postId = {{ $post->id }};

    function updateVoteBar(karma) {
        const el = document.getElementById('vote-bar-' + postId);
        if (!el || !window.Alpine || !window.Alpine.$data) return;
        window.Alpine.$data(el).karma = karma;
    }

    function updateCommentCount(count) {
        const el = document.getElementById('comment-count-' + postId);
        if (el) el.textContent = count;
    }

    function appendNewComment(commentId) {
        fetch('/comments/' + commentId + '/card', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.ok ? res.text() : null; })
        .then(function(html) {
            if (html) window.RealtimeUtils.prependComment(html);
        })
        .catch(function() {});
    }

    function setupEchoListeners() {
        window.Echo.channel('posts.' + postId)
            .listen('.PostVoted', function(e) {
                updateVoteBar(e.karma);
            })
            .listen('.CommentAdded', function(e) {
                updateCommentCount(e.commentCount);
                // Solo inyectar comentarios de primer nivel (sin parent_id)
                if (e.commentId && !e.parentId) {
                    appendNewComment(e.commentId);
                }
            });
    }

    if (window.Echo) { setupEchoListeners(); }
    else { window.addEventListener('echo-ready', setupEchoListeners, { once: true }); }
})();
</script>
@endsection
