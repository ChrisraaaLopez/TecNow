@extends('layouts.app')

@section('content')
<x-navbar />

<div x-data="{ showModal: false, showAddAdminModal: false, selectedCommunityId: null }"
  class="flex max-w-[1400px] mx-auto">

  {{-- SIDEBAR --}}
  <aside class="hidden lg:block w-64 border-r border-border bg-sidebar px-4 py-6 sticky top-[73px] h-[calc(100vh-73px)] overflow-y-auto">
    <nav class="space-y-1 mb-6">
      <a href="{{ route('dashboard') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sidebar-accent transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
        </svg>
        <span>Inicio</span>
      </a>
    </nav>

    <div class="border-t border-sidebar-border pt-4">
      <p class="text-xs text-muted-foreground px-3 mb-3 uppercase tracking-wider">Comunidades</p>
      <div class="space-y-1">
        @foreach($communities->where('tipo', 'carrera') as $c)
        <a href="{{ route('communities.show', $c) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ $c->id === $community->id ? 'bg-secondary text-secondary-foreground' : 'hover:bg-sidebar-accent' }}">
          <x-community-icon :community="$c" size="xs" />
          <div class="flex-1 min-w-0">
            <p class="text-xs truncate">{{ $c->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $c->users_count }} miembros</p>
          </div>
        </a>
        @endforeach
        @php $avisos = $communities->where('tipo', 'avisos')->first(); @endphp
        @if($avisos)
        <a href="{{ route('communities.show', $avisos) }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ $avisos->id === $community->id ? 'bg-secondary text-secondary-foreground' : 'hover:bg-sidebar-accent' }}">
          <x-community-icon :community="$avisos" size="xs" />
          <div class="flex-1 min-w-0">
            <p class="text-xs truncate">{{ $avisos->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $avisos->users_count }} miembros</p>
          </div>
        </a>
        @endif
      </div>
    </div>
  </aside>

  {{-- MAIN --}}
  <main class="flex-1 px-4 lg:px-6 py-6">

    {{-- Cabecera de la comunidad --}}
    <div class="bg-card border border-border rounded-lg p-6 mb-6">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
          <x-community-icon :community="$community" size="xl" />
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-xl font-bold">{{ $community->name }}</h1>
              @if($community->tipo === 'avisos')
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Oficial</span>
              @endif
            </div>
            <p class="text-sm text-muted-foreground mt-1">{{ $community->description }}</p>
            <p class="text-xs text-muted-foreground mt-1">{{ $community->users_count }} miembros</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          @if($isMember)
          <form method="POST" action="{{ route('communities.leave', $community) }}">
            @csrf
            <button type="submit"
              class="px-4 py-2 rounded-lg border border-border text-sm hover:bg-muted transition-colors">
              Salir
            </button>
          </form>
          @else
          <form method="POST" action="{{ route('communities.join', $community) }}">
            @csrf
            <button type="submit"
              class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors hover:scale-105"
              style="background:#1e40af">
              Unirse
            </button>
          </form>
          @endif
          {{-- Solo miembros (o admins) pueden publicar; en avisos solo admins --}}
          @if(($isMember || Auth::user()->rol === 'admin') && ($community->tipo !== 'avisos' || Auth::user()->rol === 'admin'))
          <a href="{{ route('posts.create', ['community_id' => $community->id]) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-white flex items-center gap-2 hover:scale-105 transition-transform"
            style="background:#1e40af">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Publicar
          </a>
          @endif
        </div>
      </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Posts --}}
    <div class="space-y-4">
      @forelse($posts as $post)
      @php
        $karma = $post->votes->sum('vote');
        $userVote = $post->votes->where('user_id', Auth::id())->first()?->vote;
        $isAuthor = $post->user_id === Auth::id();
        $isAdmin = Auth::user()->rol === 'admin';
      @endphp
      <div class="bg-card border border-border rounded-lg p-6" x-data="{ showReport: false }">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <a href="{{ route('perfil.show', $post->user->username) }}"
              class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden relative border border-gray-300 block hover:opacity-80 transition-opacity">
              <img src="{{ asset('avatars/'.$post->user->avatar) }}" class="w-full h-full object-cover">
              @if($post->user->marco)
              <img src="{{ asset('marcos/'.$post->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10"/>
              @endif
            </a>
            <div>
              <a href="{{ route('perfil.show', $post->user->username) }}" class="text-sm font-semibold hover:text-primary transition-colors">{{ $post->user->name }}</a>
              <p class="text-xs text-muted-foreground">{{ $post->created_at->diffForHumans() }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            @if($isAuthor || $isAdmin)
            <a href="{{ route('posts.edit', $post) }}" class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST">
              @csrf @method('DELETE')
              <button type="submit" onclick="return confirm('¿Eliminar esta publicación?')" class="p-1.5 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </form>
            @endif
            @if(!$isAuthor)
            <button @click="showReport = !showReport" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors" title="Reportar">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H9.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
              </svg>
            </button>
            @endif
          </div>
        </div>

        {{-- Reporte inline --}}
        <div x-show="showReport" x-cloak class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
          <form method="POST" action="{{ route('reportes.store') }}" class="flex flex-col gap-2">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            <select name="motivo" required class="w-full px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-900 bg-white">
              <option value="">Motivo del reporte...</option>
              <option value="spam">Spam</option>
              <option value="contenido inapropiado">Contenido inapropiado</option>
              <option value="acoso">Acoso</option>
              <option value="desinformación">Desinformación</option>
              <option value="otro">Otro</option>
            </select>
            <textarea name="descripcion" rows="2" placeholder="Descripción opcional..." class="w-full px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-900 bg-white resize-none"></textarea>
            <div class="flex gap-2 justify-end">
              <button type="button" @click="showReport = false" class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-100">Cancelar</button>
              <button type="submit" class="text-xs px-3 py-1.5 rounded text-white" style="background:#ef4444">Enviar reporte</button>
            </div>
          </form>
        </div>

        @if($post->shared_from_post_id)
        <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-2">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
          </svg>
          Compartido por {{ $post->user->name }}
        </div>
        @endif

        <a href="{{ route('posts.show', $post) }}">
          <h3 class="text-lg font-bold mb-2 hover:text-primary transition-colors">{{ $post->title }}</h3>
        </a>
        <p class="text-sm text-foreground whitespace-pre-wrap mb-4">{{ $post->content }}</p>
        @php $imageUrls = $post->image_urls; @endphp
        @if(count($imageUrls) > 0)
        <a href="{{ route('posts.show', $post) }}" class="block mb-4 rounded-lg overflow-hidden border border-border">
          @if(count($imageUrls) === 1)
            {{-- Imagen única: fondo borroso + imagen contenida (igual que dashboard y show) --}}
            <div class="relative w-full bg-black" style="height:256px">
              <img src="{{ $imageUrls[0] }}" alt="" class="absolute inset-0 w-full h-full object-cover scale-110 blur-md opacity-50 pointer-events-none select-none" />
              <img src="{{ $imageUrls[0] }}" alt="" class="relative w-full h-full object-contain hover:opacity-95 transition-opacity z-10" />
            </div>
          @else
            {{-- Múltiples imágenes: grid 2x2 con overlay +N --}}
            <div class="grid grid-cols-2 gap-0.5">
              @foreach(array_slice($imageUrls, 0, 4) as $i => $url)
                <div class="relative {{ count($imageUrls) === 3 && $i === 0 ? 'row-span-2' : '' }}">
                  <img src="{{ $url }}" alt="" class="w-full h-32 object-cover hover:opacity-95 transition-opacity" />
                  @if($i === 3 && count($imageUrls) > 4)
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                      <span class="text-white font-semibold text-lg">+{{ count($imageUrls) - 4 }}</span>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @endif
        </a>
        @endif

        <div id="vote-bar-{{ $post->id }}" class="flex items-center gap-1 mt-2" x-data="{
          karma: {{ $karma }}, userVote: {{ $userVote ?? 'null' }}, loading: false,
          async vote(value) {
            if (this.loading) return; this.loading = true;
            try {
              const res = await fetch('{{ route('posts.vote', $post) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ vote: value }),
              });
              const data = await res.json();
              this.karma = data.karma; this.userVote = data.user_vote;
            } finally { this.loading = false; }
          }
        }">
          <div class="flex items-center bg-gray-100 rounded-lg">
            <button type="button" @click="vote(1)" :disabled="loading"
              :class="userVote === 1 ? 'text-orange-400 bg-orange-500/10' : 'text-gray-400 hover:text-orange-400 hover:bg-orange-500/10'"
              class="p-1.5 rounded-lg transition-colors">
              <svg class="w-4 h-4" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
              </svg>
            </button>
            <span class="text-sm font-semibold min-w-[2rem] text-center"
              :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-400'"
              x-text="karma"></span>
            <button type="button" @click="vote(-1)" :disabled="loading"
              :class="userVote === -1 ? 'text-blue-400 bg-blue-500/10' : 'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'"
              class="p-1.5 rounded-lg transition-colors">
              <svg class="w-4 h-4" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
          <span class="text-gray-700 mx-1">·</span>
          <div class="flex items-center bg-gray-100 rounded-lg p-1">
            <a href="{{ route('posts.show', $post) }}" class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
              <span id="comment-count-{{ $post->id }}" class="text-sm">{{ $post->comments->count() }}</span>
            </a>
          </div>

          <span class="text-gray-700 mx-1">·</span>

          {{-- Botón compartir --}}
          <x-share-button :post="$post" />
        </div>
      </div>
      @empty
      <div class="bg-card border border-border rounded-lg p-12 text-center">
        <p class="text-muted-foreground text-sm">No hay publicaciones en esta comunidad todavía.</p>
        @if(($isMember || Auth::user()->rol === 'admin') && ($community->tipo !== 'avisos' || Auth::user()->rol === 'admin'))
        <a href="{{ route('posts.create', ['community_id' => $community->id]) }}" class="mt-4 inline-block px-4 py-2 rounded-lg text-sm text-white" style="background:#1e40af">
          Crear la primera publicación
        </a>
        @elseif(!$isMember)
        <p class="mt-3 text-xs text-gray-400">Únete a la comunidad para poder publicar.</p>
        @endif
      </div>
      @endforelse
    </div>
  </main>

  {{-- ASIDE derecho --}}
  <aside class="hidden xl:block w-72 px-6 py-6">
    <div class="bg-card border border-border rounded-lg p-5 sticky top-24">
      <div class="flex items-center gap-3 mb-4">
        <x-community-icon :community="$community" size="md" />
        <div>
          <h3 class="font-bold text-sm">{{ $community->name }}</h3>
          @if($community->tipo === 'carrera')
          <span class="text-xs text-muted-foreground">Carrera · TSJ Lagos</span>
          @elseif($community->tipo === 'avisos')
          <span class="text-xs text-blue-600 font-medium">Avisos Oficiales</span>
          @endif
        </div>
      </div>
      <p class="text-xs text-muted-foreground mb-4">{{ $community->description }}</p>
      <div class="border-t border-border pt-4 space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-muted-foreground">Miembros</span>
          <span class="font-medium text-primary">{{ $community->users_count }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-muted-foreground">Publicaciones</span>
          <span class="font-medium text-primary">{{ $posts->count() }}</span>
        </div>
      </div>
      @if($community->tipo === 'avisos')
      <div class="mt-4 p-3 bg-blue-50 rounded-lg">
        <p class="text-xs text-blue-700">
          📢 Este espacio es de uso exclusivo para comunicados oficiales del TSJ Lagos.
        </p>
      </div>
      @endif
    </div>
  </aside>

  {{-- MODAL crear publicación (dentro del scope x-data) --}}
  <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/60" @click="showModal = false"></div>
    <div class="relative bg-card border border-border rounded-lg p-6 w-full max-w-lg mx-4 z-10">
      <div class="flex items-center justify-between mb-4">
        <h3>Publicar en {{ $community->name }}</h3>
        <button @click="showModal = false" class="text-muted-foreground hover:text-foreground">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <form action="{{ route('posts.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="community_id" value="{{ $community->id }}">
        <input type="text" name="title" placeholder="Título de la publicación" required
          class="w-full px-3 py-2 rounded-lg border border-border bg-muted focus:border-primary focus:outline-none text-gray-900"/>
        <textarea rows="4" name="content" placeholder="¿Qué quieres compartir?" required
          class="w-full px-3 py-2 rounded-lg border border-border bg-muted focus:border-primary focus:outline-none resize-none text-gray-900"></textarea>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors text-sm text-gray-900 bg-white">Cancelar</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-700 transition-colors text-sm">Publicar</button>
        </div>
      </form>
    </div>
  </div>

</div>
<script>
(function() {
    const postIds = @json($posts->pluck('id'));
    function setup() {
        postIds.forEach(function(postId) {
            window.Echo.channel('posts.' + postId)
                .listen('.PostVoted', function(e) { window.RealtimeUtils.updateVote(postId, e.karma); })
                .listen('.CommentAdded', function(e) { window.RealtimeUtils.updateCommentCount(postId, e.commentCount); });
        });
        // Las vistas de comunidades ordenan por score, no se inyectan nuevos posts aquí
    }
    if (window.Echo) { setup(); }
    else { window.addEventListener('echo-ready', setup, { once: true }); }
})();
</script>
@endsection
