@php
  use Illuminate\Support\Facades\Auth;

  $postCommunityIds = $post->communities->pluck('id');
  $myCommunities = Auth::user()->communities()
      ->whereNotIn('communities.id', $postCommunityIds->toArray())
      ->when(Auth::user()->rol !== 'admin', fn($q) => $q->where('communities.tipo', '!=', 'avisos'))
      ->orderBy('communities.name')
      ->get();

  // ¿Ya compartió este post en su perfil?
  $yaEnPerfil = \App\Models\Post::where('user_id', Auth::id())
      ->where('shared_from_post_id', $post->id)
      ->doesntHave('communities')
      ->exists();

  // Colores por slug para el badge de comunidad en el panel compartir
  $iconColors = [
    'ingenieria-en-sistemas-computacionales' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
    'ingenieria-electromecanica'              => ['bg' => '#fef9c3', 'color' => '#a16207'],
    'ingenieria-civil'                        => ['bg' => '#f1f5f9', 'color' => '#475569'],
    'ingenieria-en-gestion-empresarial'       => ['bg' => '#dcfce7', 'color' => '#15803d'],
    'licenciatura-en-arquitectura'            => ['bg' => '#ede9fe', 'color' => '#6d28d9'],
    'ingenieria-mecatronica'                  => ['bg' => '#ffedd5', 'color' => '#c2410c'],
    'ingenieria-en-industrias-alimentarias'   => ['bg' => '#d1fae5', 'color' => '#065f46'],
    'ingenieria-sistemas-automotrices'        => ['bg' => '#fee2e2', 'color' => '#991b1b'],
    'ingenieria-industrial'                   => ['bg' => '#e0f2fe', 'color' => '#0369a1'],
    'avisos-institucionales'                  => ['bg' => '#1e40af', 'color' => '#ffffff'],
  ];

  $communitiesForJs = $myCommunities->map(function ($c) use ($iconColors) {
    $cfg = $iconColors[$c->slug] ?? ['bg' => 'rgba(30,64,175,0.1)', 'color' => '#1e40af'];
    return ['id' => $c->id, 'name' => $c->name, 'bg' => $cfg['bg'], 'color' => $cfg['color'], 'initial' => mb_strtoupper(mb_substr($c->name, 0, 1))];
  });
@endphp

<div class="relative"
     x-data="{
       open: false,
       loading: false,
       toast: null,
       toastOk: true,
       perfilYaCompartido: {{ $yaEnPerfil ? 'true' : 'false' }},
       communities: {{ Js::from($communitiesForJs) }},

       async copyLink() {
         try {
           await navigator.clipboard.writeText('{{ route('posts.show', $post) }}');
           this.showToast('¡Enlace copiado!', true);
         } catch(e) {
           this.showToast('No se pudo copiar.', false);
         }
         this.open = false;
       },

       async shareToProfile() {
         if (this.loading || this.perfilYaCompartido) return;
         this.loading = true;
         try {
           const res = await fetch('{{ route('posts.share', $post) }}', {
             method: 'POST',
             headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
             body: JSON.stringify({ community_id: null }),
           });
           const data = await res.json();
           if (data.success) {
             this.perfilYaCompartido = true;
             this.showToast(data.message, true);
           } else {
             this.showToast(data.error || 'Error al compartir.', false);
           }
         } catch(e) {
           this.showToast('Error de conexión.', false);
         }
         this.loading = false;
         this.open = false;
       },

       async shareTo(communityId, communityName) {
         if (this.loading) return;
         this.loading = true;
         try {
           const res = await fetch('{{ route('posts.share', $post) }}', {
             method: 'POST',
             headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
             body: JSON.stringify({ community_id: communityId }),
           });
           const data = await res.json();
           if (data.success) {
             this.showToast(data.message, true);
             this.communities = this.communities.filter(c => c.id !== communityId);
           } else {
             this.showToast(data.error || 'Error al compartir.', false);
           }
         } catch(e) {
           this.showToast('Error de conexión.', false);
         }
         this.loading = false;
         this.open = false;
       },

       showToast(msg, ok) {
         this.toast = msg;
         this.toastOk = ok;
         setTimeout(() => this.toast = null, 3000);
       }
     }"
     @click.outside="open = false">

  {{-- Botón compartir --}}
  <button @click="open = !open" type="button"
    class="relative group flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-500/10 transition-colors text-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
    </svg>
    <span class="hidden sm:inline">Compartir</span>
    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded
                 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none sm:hidden">
      Compartir
    </span>
  </button>

  {{-- Panel desplegable --}}
  <div x-show="open" x-cloak
       class="absolute z-50 mt-2 w-72 rounded-2xl shadow-2xl overflow-hidden"
       style="background:#fff; border:1px solid #e5e7eb; bottom: calc(100% + 8px); right:0">

    {{-- Encabezado --}}
    <div class="px-4 py-3" style="border-bottom:1px solid #f0f0f0">
      <p class="text-sm font-bold text-gray-900">Compartir publicación</p>
    </div>

    {{-- Copiar enlace --}}
    <button @click="copyLink()" type="button"
      class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors text-left">
      <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background:#f3f4f6">
        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-medium text-gray-900">Copiar enlace</p>
        <p class="text-xs text-gray-400">Comparte el link directamente</p>
      </div>
    </button>

    {{-- Compartir en mi perfil --}}
    <button @click="shareToProfile()" type="button"
      :disabled="loading || perfilYaCompartido"
      class="w-full flex items-center gap-3 px-4 py-3 transition-colors text-left disabled:opacity-50"
      :class="perfilYaCompartido ? 'cursor-default' : 'hover:bg-gray-50'">
      <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background:#eff6ff">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-medium text-gray-900" x-text="perfilYaCompartido ? 'Ya compartiste en tu perfil' : 'Compartir en mi perfil'"></p>
        <p class="text-xs text-gray-400">Aparece en tu feed y perfil</p>
      </div>
      <template x-if="perfilYaCompartido">
        <svg class="w-4 h-4 text-green-500 ml-auto flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </template>
    </button>

    {{-- Compartir en comunidad --}}
    <div style="border-top:1px solid #f0f0f0">
      <p class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Mis comunidades</p>

      <template x-if="communities.length === 0">
        <p class="px-4 py-3 text-xs text-gray-400 italic">
          No tienes otras comunidades disponibles para compartir.
        </p>
      </template>

      <div class="max-h-48 overflow-y-auto">
        <template x-for="c in communities" :key="c.id">
          <button @click="shareTo(c.id, c.name)" type="button" :disabled="loading"
            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors text-left disabled:opacity-50">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold"
                 :style="`background:${c.bg}; color:${c.color}`"
                 x-text="c.initial"></div>
            <p class="text-sm font-medium text-gray-900 truncate" x-text="c.name"></p>
            <template x-if="loading">
              <div class="ml-auto w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin flex-shrink-0"></div>
            </template>
          </button>
        </template>
      </div>
    </div>
  </div>

  {{-- Toast inline --}}
  <div x-show="toast !== null" x-cloak
       class="fixed bottom-5 right-5 z-[9999] px-4 py-3 rounded-xl shadow-xl text-white text-sm font-medium flex items-center gap-2 pointer-events-none"
       :style="toastOk ? 'background:#16a34a' : 'background:#dc2626'"
       x-text="toast">
  </div>
</div>
