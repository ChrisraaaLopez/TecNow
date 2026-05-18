@extends('layouts.app')

@section('content')
<x-navbar />

{{-- BREADCRUMB --}}
<div class="max-w-[1400px] mx-auto px-6 py-4">
  <div class="flex items-center gap-2 text-sm text-gray-400">
    <a href="{{ route('dashboard') }}" class="hover:text-blue-400 transition-colors">Inicio</a>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>

    <span class="text-gray-700">Mi Perfil</span>
  </div>
</div>

<div class="max-w-4xl mx-auto px-4 py-8">

  {{-- Tarjeta principal --}}
  <div class="bg-card border border-border rounded-xl overflow-hidden mb-6">

    {{-- Banner --}}
    @php
    $bannerColors = [
    'avatar_amarillo.png' => 'linear-gradient(135deg, #b45309 0%, #fbbf24 100%)',
    'avatar_azul.png' => 'linear-gradient(135deg, #1e40af 0%, #60a5fa 100%)',
    'avatar_gris.png' => 'linear-gradient(135deg, #374151 0%, #9ca3af 100%)',
    'avatar_morado.png' => 'linear-gradient(135deg, #6b21a8 0%, #c084fc 100%)',
    'avatar_naranja.png' => 'linear-gradient(135deg, #c2410c 0%, #fb923c 100%)',
    'avatar_negro.png' => 'linear-gradient(135deg, #111827 0%, #4b5563 100%)',
    'avatar_rojo.png' => 'linear-gradient(135deg, #991b1b 0%, #f87171 100%)',
    'avatar_verde.png' => 'linear-gradient(135deg, #166534 0%, #4ade80 100%)',
    'avatar_default.png' => 'linear-gradient(135deg, #1e40af 0%, #60a5fa 100%)',
    ];
    $bannerStyle =
    $bannerColors[Auth::user()->avatar] ?? 'linear-gradient(135deg, #1e40af 0%, #60a5fa 100%)';
    @endphp

    <div class="h-32" style="background: {{ $bannerStyle }};"></div>


    {{-- Info --}}
    <div class="px-6 pb-6">
      <div class="flex items-end justify-between -mt-12 mb-4 flex-wrap gap-4">
        <div class="relative w-24 h-24">
          <img src="{{ asset('avatars/' . Auth::user()->avatar) }}"
            class="w-full h-full rounded-full border-4 border-white object-cover shadow-md" />
          @if(Auth::user()->marco)
          <img src="{{ asset('marcos/' . Auth::user()->marco) }}" class="absolute inset-0 w-full h-full z-10 pointer-events-none" />
          @endif
        </div>
        <div class="flex items-end justify-between -mt-12 mb-4 flex-wrap gap-10">
          <button onclick="document.getElementById('passModal').classList.remove('hidden')"
            class="hover:scale-105 transition-transform text-sm flex items-center gap-2 px-4 py-2 rounded-lg"
            style="border: 3px solid #1e40af; background-color: #1e40af; color: white;">
            Cambiar contraseña
          </button>
          <button onclick="document.getElementById('editModal').classList.remove('hidden')"
            class="hover:scale-105 transition-transform text-sm flex items-center gap-2 px-4 py-2 rounded-lg"
            style="border: 3px solid #1e40af; background-color: #1e40af; color: white;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 0l.172.172a2 2 0 010 2.828L12 15H9v-3z" />
            </svg>
            Editar perfil
          </button>
        </div>
      </div>

      <h1 class="text-xl font-bold text-foreground">{{ Auth::user()->name }}</h1>
      <p class="text-muted-foreground text-sm mb-1">&#64;{{ Auth::user()->username }}</p>
      <p class="text-muted-foreground text-sm mb-4">{{ Auth::user()->email }}</p>

      <div class="flex items-center gap-2 text-xs text-muted-foreground">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Miembro desde {{ Auth::user()->created_at->format('d/m/Y') }}
      </div>
    </div>
  </div>

  {{-- Stats representativas --}}
  <div class="grid grid-cols-3 gap-4 mb-6">
    @foreach ([[$postsCount, 'Publicaciones'], [$likesCount, 'Likes'], [$commentsCount, 'Comentarios']] as [$num, $label])
    <div class="bg-card border border-border rounded-xl p-4 text-center">
      <p class="text-2xl font-bold text-primary">{{ $num }}</p>
      <p class="text-xs text-muted-foreground mt-1">{{ $label }}</p>
    </div>
    @endforeach
  </div>

  {{-- Mis publicaciones --}}
  <div class="bg-card border border-border rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base font-semibold">Mis publicaciones</h2>
      <a href="{{ route('posts.create') }}"
        class="bg-primary text-gray-500 border border-gray-500 px-3 py-1.5 rounded-lg hover:bg-blue-700 hover:text-white transition-colors flex items-center gap-1.5 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva publicación
      </a>
    </div>

    @if($posts->isEmpty())
    {{-- Estado vacío --}}
    <div class="text-center py-10">
      <svg class="w-10 h-10 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
      </svg>
      <p class="text-muted-foreground text-sm">Aún no tienes publicaciones.</p>
      <p class="text-gray-500 text-sm">¿Comenzamos con una?</p>
      <a href="{{ route('posts.create') }}"
        class="mt-4 inline-flex items-center gap-2 bg-primary text-gray-500 border border-gray-500 px-4 py-2 rounded-lg hover:bg-blue-700 hover:text-white transition-colors text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva publicación
      </a>
    </div>
    @else
    {{-- Lista de posts --}}
    <div class="space-y-4">
      @foreach($posts as $post)
      @php
        $karma    = $post->votes->sum('vote');
        $userVote = $post->votes->where('user_id', Auth::id())->first()?->vote;
        $imageUrls = $post->image_urls;
      @endphp
      <div class="relative border border-border rounded-lg p-4 hover:bg-muted/30 transition-colors"
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
                   headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                   body: JSON.stringify({ vote: value }),
                 });
                 const data = await res.json();
                 this.karma = data.karma;
                 this.userVote = data.user_vote;
               } finally { this.loading = false; }
             }
           }">

        {{-- Editar / Eliminar — posición absoluta, no consumen espacio del layout --}}
        <div class="absolute top-3 right-3 flex items-center gap-1">
          <a href="{{ route('posts.edit', $post) }}"
            class="relative group p-1.5 rounded-lg text-blue-500 hover:bg-blue-500/10 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Editar</span>
          </a>
          <form action="{{ route('posts.destroy', $post) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar esta publicación?')"
              class="relative group p-1.5 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Eliminar</span>
            </button>
          </form>
        </div>

        {{-- Contenido: ocupa todo el ancho disponible --}}
        <div class="mb-3 pr-16">

          {{-- Indicador compartido --}}
          @if($post->shared_from_post_id)
          <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
            Compartido
            @if($post->sharedFrom)
            · <a href="{{ route('posts.show', $post->sharedFrom) }}" class="text-blue-500 hover:underline">Ver original</a>
            @endif
          </div>
          @endif

          {{-- Título + miniatura --}}
          <div class="flex gap-3">
            <div class="flex-1 min-w-0">
              <a href="{{ route('posts.show', $post) }}">
                <h3 class="text-base font-bold mb-1 hover:text-primary transition-colors leading-snug">{{ $post->title }}</h3>
              </a>
              <p class="text-xs text-gray-600 line-clamp-4">{{ $post->content }}</p>
            </div>

            @if(count($imageUrls) > 0)
            <a href="{{ route('posts.show', $post) }}" class="flex-shrink-0 self-start">
              <div class="relative w-24 h-24 rounded-lg overflow-hidden border border-border">
                <img src="{{ $imageUrls[0] }}" alt="" class="w-full h-full object-cover hover:opacity-90 transition-opacity" />
                @if(count($imageUrls) > 1)
                <div class="absolute bottom-1 right-1 bg-black/60 rounded px-1 py-0.5">
                  <span class="text-white text-xs font-medium">+{{ count($imageUrls) - 1 }}</span>
                </div>
                @endif
              </div>
            </a>
            @endif
          </div>

          <p class="text-xs text-muted-foreground mt-2">{{ $post->created_at->diffForHumans() }}</p>
        </div>

        {{-- Barra de acciones --}}
        <div class="flex items-center gap-1">

          {{-- Karma --}}
          <div class="flex items-center bg-gray-100 rounded-lg">
            <button type="button" @click="vote(1)" :disabled="loading"
              :class="userVote === 1 ? 'text-orange-400 bg-orange-500/10' : 'text-gray-400 hover:text-orange-400 hover:bg-orange-500/10'"
              class="relative group p-1.5 rounded-lg transition-colors">
              <svg class="w-4 h-4" :fill="userVote === 1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
              </svg>
              <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Upvote</span>
            </button>
            <span class="text-sm font-semibold min-w-[2rem] text-center"
              :class="karma > 0 ? 'text-orange-400' : karma < 0 ? 'text-blue-400' : 'text-gray-400'"
              x-text="karma"></span>
            <button type="button" @click="vote(-1)" :disabled="loading"
              :class="userVote === -1 ? 'text-blue-400 bg-blue-500/10' : 'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'"
              class="relative group p-1.5 rounded-lg transition-colors">
              <svg class="w-4 h-4" :fill="userVote === -1 ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
              <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Downvote</span>
            </button>
          </div>

          <span class="text-gray-700 mx-1">·</span>

          {{-- Comentarios --}}
          <div class="flex items-center bg-gray-100 rounded-lg p-1">
            <a href="{{ route('posts.show', $post) }}#comentarios"
               class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <span class="text-sm">{{ $post->comments->count() }}</span>
            </a>
          </div>

          {{-- Imágenes --}}
          @if(count($imageUrls) > 0)
          <span class="text-gray-700 mx-1">·</span>
          <div class="flex items-center bg-gray-100 rounded-lg p-1">
            <a href="{{ route('posts.show', $post) }}"
               class="flex items-center gap-1.5 text-gray-400 hover:text-primary transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-sm">{{ count($imageUrls) }}</span>
            </a>
          </div>
          @endif

          {{-- Compartir --}}
          <x-share-button :post="$post" />

        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</div>

{{-- Modal editar perfil --}}
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/60" onclick="document.getElementById('editModal').classList.add('hidden')">
  </div>
  <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md z-10">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-bold text-gray-900 text-lg">Editar perfil</h3>
      <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
        class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}" class="space-y-4">
      @csrf
      @method('PATCH')

      {{-- Avatar y Marco --}}
      <div class="flex flex-col items-center mb-4 gap-4">
        <div class="flex gap-6 items-center">
          <div class="relative text-center">
            <p class="text-xs text-gray-500 mb-1">Avatar</p>
            <div class="relative w-20 h-20">
              <img id="editAvatarPreview" src="{{ asset('avatars/' . Auth::user()->avatar) }}"
                class="w-full h-full rounded-full border-4 border-primary object-cover shadow-md" />
              <button type="button"
                onclick="document.getElementById('avatarPickerModal').classList.remove('hidden')"
                class="absolute bottom-0 right-0 text-white w-7 h-7 rounded-full flex items-center justify-center shadow transition-transform hover:scale-110 z-20"
                style="background-color: #1e40af;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 0l.172.172a2 2 0 010 2.828L12 15H9v-3z" />
                </svg>
              </button>
            </div>
            <input type="hidden" name="avatar" id="editAvatarField" value="{{ Auth::user()->avatar }}" />
          </div>

          <div class="relative text-center">
            <p class="text-xs text-gray-500 mb-1">Marco</p>
            <div class="relative w-20 h-20 bg-gray-100 rounded-full border-4 border-gray-200">
              <img id="editMarcoPreview" src="{{ Auth::user()->marco ? asset('marcos/' . Auth::user()->marco) : '' }}"
                class="w-full h-full object-cover z-10 {{ Auth::user()->marco ? '' : 'hidden' }}" />
              <button type="button"
                onclick="document.getElementById('marcoPickerModal').classList.remove('hidden')"
                class="absolute bottom-0 right-0 text-white w-7 h-7 rounded-full flex items-center justify-center shadow transition-transform hover:scale-110 z-20"
                style="background-color: #1e40af;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 0l.172.172a2 2 0 010 2.828L12 15H9v-3z" />
                </svg>
              </button>
            </div>
            <input type="hidden" name="marco" id="editMarcoField" value="{{ Auth::user()->marco }}" />
          </div>
        </div>
      </div>

      {{-- Username --}}
      <div>
        <label class="block text-sm mb-2 text-gray-700">Username</label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
          <input type="text" name="username" value="{{ Auth::user()->username }}"
            class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none text-gray-900" />
        </div>
      </div>

      @if ($errors->has('username'))
      <p class="text-red-500 text-xs">{{ $errors->first('username') }}</p>
      @endif

      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm transition-colors">
          Cancelar
        </button>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm transition-transform hover:scale-105"
          style="background-color: #1e40af; color: white;">
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal selección de avatar --}}
<div id="avatarPickerModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/60"
    onclick="document.getElementById('avatarPickerModal').classList.add('hidden')"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm z-10">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-gray-900">Elige tu avatar</h3>
      <button type="button" onclick="document.getElementById('avatarPickerModal').classList.add('hidden')"
        class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="grid grid-cols-4 gap-3">
      @foreach (collect(glob(public_path('avatars/*.png')))->map(fn($p) => basename($p)) as $avatar)
      <button type="button" onclick="selectEditAvatar('{{ $avatar }}')"
        class="rounded-full overflow-hidden aspect-square hover:ring-4 hover:ring-primary hover:ring-offset-2 transition-all">
        <img src="/avatars/{{ $avatar }}" class="w-full h-full object-cover" />
      </button>
      @endforeach
    </div>
  </div>
</div>

{{-- Modal selección de marco --}}
<div id="marcoPickerModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/60"
    onclick="document.getElementById('marcoPickerModal').classList.add('hidden')"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm z-10">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-gray-900">Elige tu marco</h3>
      <button type="button" onclick="document.getElementById('marcoPickerModal').classList.add('hidden')"
        class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="grid grid-cols-4 gap-3">
      <button type="button" onclick="selectEditMarco('')" class="rounded-full flex items-center justify-center bg-gray-100 text-gray-500 text-xs aspect-square hover:ring-4 hover:ring-primary hover:ring-offset-2 transition-all">
        Ninguno
      </button>
      @foreach (collect(glob(public_path('marcos/*.png')))->map(fn($p) => basename($p)) as $marco)
      <button type="button" onclick="selectEditMarco('{{ $marco }}')"
        class="rounded-full overflow-hidden aspect-square hover:ring-4 hover:ring-primary hover:ring-offset-2 transition-all bg-gray-100">
        <img src="/marcos/{{ $marco }}" class="w-full h-full object-cover" />
      </button>
      @endforeach
    </div>
  </div>
</div>

{{-- Modal cambiar contraseña --}}
<div id="passModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/60" onclick="document.getElementById('passModal').classList.add('hidden')">
  </div>
  <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md z-10">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-bold text-gray-900 text-lg">Cambiar contraseña</h3>
      <button onclick="document.getElementById('passModal').classList.add('hidden')"
        class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('perfil.password') }}" class="space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="block text-sm mb-2 text-gray-700">Contraseña actual</label>
        <input type="password" name="current_password" placeholder="••••••••"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none text-gray-900" />
      </div>
      <div>
        <label class="block text-sm mb-2 text-gray-700">Nueva contraseña</label>
        <input type="password" name="password" placeholder="Mínimo 8 caracteres"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none text-gray-900" />
      </div>
      <div>
        <label class="block text-sm mb-2 text-gray-700">Confirmar nueva contraseña</label>
        <input type="password" name="password_confirmation" placeholder="Repite tu contraseña"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none text-gray-900" />
      </div>

      @if ($errors->has('current_password'))
      <p class="text-red-500 text-xs">{{ $errors->first('current_password') }}</p>
      @endif

      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('passModal').classList.add('hidden')"
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
          Cancelar
        </button>
        <button type="submit"
          class="px-4 py-2 rounded-lg text-sm text-white transition-transform hover:scale-105"
          style="background-color: #1e40af;">
          Cambiar contraseña
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function selectEditAvatar(filename) {
    document.getElementById('editAvatarPreview').src = '/avatars/' + filename;
    document.getElementById('editAvatarField').value = filename;
    document.getElementById('avatarPickerModal').classList.add('hidden');
  }

  function selectEditMarco(filename) {
    let preview = document.getElementById('editMarcoPreview');
    if (filename) {
      preview.src = '/marcos/' + filename;
      preview.classList.remove('hidden');
    } else {
      preview.src = '';
      preview.classList.add('hidden');
    }
    document.getElementById('editMarcoField').value = filename;
    document.getElementById('marcoPickerModal').classList.add('hidden');
  }
</script>
@endsection
