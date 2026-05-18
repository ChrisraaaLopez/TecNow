{{-- Partial: tarjeta de post para inyección dinámica vía AJAX --}}
@php
    $isAuthor      = $post->user_id === Auth::id();
    $community     = $post->communities->first();
    $isForumAdmin  = $community
        ? $community->users()->where('user_id', Auth::id())->wherePivot('role', 'admin')->exists()
        : false;
    $isGlobalAdmin = Auth::user()->global_role === 'admin';
@endphp

<div class="bg-card border border-border rounded-lg p-6" x-data="{ showReport: false }">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('perfil.show', $post->user->username) }}"
        class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden relative border border-gray-300 block hover:opacity-80 transition-opacity">
        <img src="{{ asset('avatars/' . $post->user->avatar) }}" class="w-full h-full object-cover">
        @if($post->user->marco)
        <img src="{{ asset('marcos/' . $post->user->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
        @endif
      </a>
      <div>
        <div>
          <a href="{{ route('perfil.show', $post->user->username) }}"
            class="text-sm font-semibold hover:text-primary transition-colors">{{ $post->user->name }}</a>
          @if($community)
          <span class="text-gray-500 font-normal text-xs"> en {{ $community->name }}</span>
          @endif
        </div>
        <p class="text-xs text-muted-foreground">Justo ahora</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      @if($isAuthor || $isForumAdmin || $isGlobalAdmin)
      <form action="{{ route('posts.destroy', $post) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit" onclick="return confirm('¿Eliminar esta publicación?')"
          class="relative group p-1.5 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </form>
      @endif
      @if(!$isAuthor)
      <button @click="showReport = !showReport"
        class="relative group p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H9.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
        </svg>
      </button>
      @endif
    </div>
  </div>

  {{-- Reporte inline --}}
  @if(!$isAuthor)
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
      <div class="flex gap-2 justify-end">
        <button type="button" @click="showReport = false" class="text-xs px-3 py-1.5 rounded border border-gray-300 bg-white hover:bg-gray-100">Cancelar</button>
        <button type="submit" class="text-xs px-3 py-1.5 rounded text-white bg-red-500 hover:bg-red-600">Enviar reporte</button>
      </div>
    </form>
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
      <img src="{{ $imageUrls[0] }}" alt="" class="w-full max-h-64 object-cover hover:opacity-95 transition-opacity" />
    @else
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

  {{-- Barra de votos --}}
  <div id="vote-bar-{{ $post->id }}" class="flex items-center gap-1 mt-2" x-data="{
    karma: {{ $karma }},
    userVote: {{ $userVote ?? 'null' }},
    loading: false,
    async vote(value) {
      if (this.loading) return;
      this.loading = true;
      try {
        const res = await fetch('{{ route('posts.vote', $post) }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ vote: value }),
        });
        const data = await res.json();
        this.karma = data.karma;
        this.userVote = data.user_vote;
      } finally { this.loading = false; }
    }
  }">
    <div class="flex items-center bg-gray-100 rounded-lg">
      <button type="button" @click="vote(1)" :disabled="loading"
        :class="userVote === 1 ? 'text-orange-400 bg-orange-500/10' : 'text-gray-400 hover:text-orange-400 hover:bg-orange-500/10'"
        class="p-1.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
      </button>
      <span class="text-sm font-semibold min-w-[2rem] text-center"
        :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-400'"
        x-text="karma"></span>
      <button type="button" @click="vote(-1)" :disabled="loading"
        :class="userVote === -1 ? 'text-blue-400 bg-blue-500/10' : 'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'"
        class="p-1.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </div>
    <span class="text-gray-700 mx-1">·</span>
    <div class="flex items-center bg-gray-100 rounded-lg p-1">
      <a href="{{ route('posts.show', $post) }}#comentarios"
        class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span id="comment-count-{{ $post->id }}" class="text-sm">{{ $post->comments->count() }}</span>
      </a>
    </div>
  </div>
</div>
