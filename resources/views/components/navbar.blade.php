<header style="background:#1e40af; border-bottom:1px solid #1d3fa0;" class="sticky top-0 z-50">
  <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between">

    {{-- Logo + búsqueda --}}
    <div class="flex items-center gap-8">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.15)">
          <span class="text-xl font-bold text-white">T</span>
        </div>
        <div>
          <p class="text-lg font-medium leading-tight text-white">TecNow</p>
          <p class="text-xs" style="color:rgba(255,255,255,0.6)">TSJ Lagos</p>
        </div>
      </a>

      {{-- Buscador con autocompletado --}}
      <div class="hidden md:block relative"
           x-data="buscador()"
           @click.outside="cerrar()">

        <form method="GET" action="{{ route('dashboard') }}" @submit="cerrar()" class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
               style="color:rgba(255,255,255,0.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
          </svg>
          <input
            type="text"
            name="q"
            x-model="query"
            value="{{ request('q') }}"
            placeholder="Buscar en TecNow..."
            autocomplete="off"
            @input="debounceSearch()"
            @focus="if(query.length >= 2) buscar()"
            @keydown.escape="cerrar()"
            style="background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.2);"
            class="pl-10 pr-4 py-2 rounded-lg w-80 focus:outline-none focus:border-white/50 placeholder-white/40 text-sm transition-colors" />
        </form>

        {{-- Dropdown de sugerencias --}}
        <div x-show="abierto" x-cloak
             class="absolute left-0 mt-2 w-80 rounded-2xl shadow-2xl overflow-hidden"
             style="background:#fff; border:1px solid #e5e7eb; z-index:999; top:100%">

          {{-- Spinner --}}
          <template x-if="cargando">
            <div class="flex items-center justify-center py-6">
              <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
          </template>

          <template x-if="!cargando && posts.length === 0 && comunidades.length === 0 && usuarios.length === 0">
            <div class="px-4 py-5 text-center">
              <p class="text-sm text-gray-500">Sin resultados para "<span x-text="query"></span>"</p>
            </div>
          </template>

          <template x-if="!cargando && (posts.length > 0 || comunidades.length > 0 || usuarios.length > 0)">
            <div>

              {{-- Usuarios --}}
              <template x-if="usuarios.length > 0">
                <div>
                  <p class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuarios</p>
                  <template x-for="u in usuarios" :key="'u'+u.id">
                    <a :href="u.url" @click="cerrar()"
                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors cursor-pointer">
                      <div class="relative w-9 h-9 rounded-full overflow-hidden border border-gray-200 flex-shrink-0">
                        <img :src="u.avatar" class="w-full h-full object-cover" />
                        <template x-if="u.marco">
                          <img :src="u.marco" class="absolute inset-0 w-full h-full object-cover z-10" />
                        </template>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="u.name"></p>
                        <p class="text-xs text-gray-400 truncate" x-text="'@' + u.username"></p>
                      </div>
                    </a>
                  </template>
                </div>
              </template>

              {{-- Comunidades --}}
              <template x-if="comunidades.length > 0">
                <div :class="usuarios.length > 0 ? 'border-t border-gray-100' : ''">
                  <p class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Comunidades</p>
                  <template x-for="c in comunidades" :key="'c'+c.id">
                    <a :href="c.url" @click="cerrar()"
                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors cursor-pointer">
                      {{-- Badge de color con inicial --}}
                      <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold"
                           :style="`background:${c.bg}; color:${c.color}`"
                           x-text="c.initial"></div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="c.name"></p>
                        <p class="text-xs text-gray-400" x-text="c.members + ' miembros'"></p>
                      </div>
                    </a>
                  </template>
                </div>
              </template>

              {{-- Posts --}}
              <template x-if="posts.length > 0">
                <div :class="(usuarios.length > 0 || comunidades.length > 0) ? 'border-t border-gray-100' : ''">
                  <p class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Publicaciones</p>
                  <template x-for="p in posts" :key="'p'+p.id">
                    <a :href="p.url" @click="cerrar()"
                       class="flex items-start gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors cursor-pointer">
                      <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                           style="background:#eff6ff">
                        <svg class="w-4 h-4" style="color:#1d4ed8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="p.title"></p>
                        <p class="text-xs text-gray-400 truncate" x-text="p.author + ' · ' + p.excerpt"></p>
                      </div>
                    </a>
                  </template>
                </div>
              </template>

              {{-- Ver todos --}}
              <div style="border-top:1px solid #f0f0f0" class="px-4 py-2.5">
                <a :href="'{{ route('dashboard') }}?q=' + encodeURIComponent(query)"
                   @click="cerrar()"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                  Ver todos los resultados →
                </a>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center gap-2" x-data="notificacionesPanel()" @click.outside="open = false">

      {{-- Campana con popup --}}
      <div class="relative">
        <button @click="open = !open; if(open) cargar()"
          class="relative p-2 rounded-lg transition-colors"
          style="color:rgba(255,255,255,0.8)"
          onmouseover="this.style.background='rgba(255,255,255,0.12)'"
          onmouseout="this.style.background='transparent'">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          @php $unread = Auth::user()->unreadNotifications->count(); @endphp
          @if($unread > 0)
          <span class="absolute top-1 right-1 w-4 h-4 rounded-full flex items-center justify-center font-bold"
            style="background:#ef4444; color:#fff; font-size:9px">
            {{ $unread > 9 ? '9+' : $unread }}
          </span>
          @else
          <span class="absolute top-1 right-1 w-2 h-2 rounded-full" style="background:rgba(255,255,255,0.4)"></span>
          @endif
        </button>

        {{-- Panel de notificaciones --}}
        <div x-show="open" x-cloak
          class="absolute right-0 mt-2 w-96 rounded-2xl shadow-2xl overflow-hidden"
          style="background:#fff; border:1px solid #e5e7eb; z-index:999; top:100%">

          {{-- Cabecera --}}
          <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid #f0f0f0">
            <span class="text-lg font-bold text-gray-900">Notificaciones</span>
            <template x-if="sinLeer > 0">
              <button @click="marcarTodas()" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                Marcar todas como leídas
              </button>
            </template>
          </div>

          {{-- Lista --}}
          <div class="overflow-y-auto" style="max-height:480px">

            <template x-if="cargando">
              <div class="flex items-center justify-center py-10">
                <div class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
              </div>
            </template>

            <template x-if="!cargando && notifs.length === 0">
              <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mb-3" style="background:#f3f4f6">
                  <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Todo al día</p>
                <p class="text-xs text-gray-400 mt-1">No tienes notificaciones nuevas</p>
              </div>
            </template>

            <template x-for="n in notifs" :key="n.id">
              <div @click="irA(n)"
                class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors"
                :style="n.read_at ? 'background:#fff' : 'background:#eff6ff'"
                onmouseover="this.style.background='#f9fafb'"
                onmouseout="this.style.background = this.dataset.leida === '1' ? '#fff' : '#eff6ff'"
                :data-leida="n.read_at ? '1' : '0'">

                {{-- Ícono por tipo --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="iconoBg(n)"
                  x-html="iconoSvg(n)">
                </div>

                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 leading-tight" x-text="n.data.titulo"></p>
                  <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="n.data.mensaje"></p>
                  <p class="text-xs mt-1 font-medium" :style="n.read_at ? 'color:#9ca3af' : 'color:#2563eb'"
                    x-text="timeAgo(n.created_at)"></p>
                </div>

                <div class="flex-shrink-0 mt-1">
                  <template x-if="!n.read_at">
                    <div class="w-2.5 h-2.5 rounded-full" style="background:#2563eb"></div>
                  </template>
                </div>
              </div>
            </template>

          </div>

          {{-- Pie --}}
          <div style="border-top:1px solid #f0f0f0">
            <a href="{{ route('notifications.index') }}"
              class="block text-center text-sm font-medium text-blue-600 hover:text-blue-800 py-3 transition-colors">
              Ver todas las notificaciones
            </a>
          </div>
        </div>
      </div>

      {{-- Usuario --}}
      <a href="{{ route('perfil') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors"
        style="color:#fff"
        onmouseover="this.style.background='rgba(255,255,255,0.12)'"
        onmouseout="this.style.background='transparent'">
        <div class="w-8 h-8 rounded-full overflow-hidden border-2 relative" style="border-color:rgba(255,255,255,0.5)">
          <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" />
          @if(Auth::user()->marco)
          <img src="{{ asset('marcos/' . Auth::user()->marco) }}"
            class="absolute inset-0 w-full h-full object-cover z-10" />
          @endif
        </div>
        <div class="hidden lg:block">
          <p class="text-sm leading-tight text-white">{{ Auth::user()->name }}</p>
          <p class="text-xs" style="color:rgba(255,255,255,0.6)">&#64;{{ Auth::user()->username }}</p>
        </div>
      </a>

      {{-- Panel Admin --}}
      @if(Auth::user()->rol === 'admin')
      <a href="{{ route('admin.index') }}"
        class="text-xs hidden lg:flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg font-medium"
        style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3)"
        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
        onmouseout="this.style.background='rgba(255,255,255,0.15)'">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Panel Admin
      </a>
      @endif

      {{-- Cerrar sesión --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
          class="text-xs hidden lg:block transition-colors px-2 py-1 rounded"
          style="color:rgba(255,255,255,0.7)"
          onmouseover="this.style.color='#fff'"
          onmouseout="this.style.color='rgba(255,255,255,0.7)'">
          Cerrar sesión
        </button>
      </form>

    </div>
  </div>
</header>

{{-- Alerta para usuarios suspendidos --}}
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-cloak
  class="fixed bottom-5 right-5 z-[999] max-w-sm"
  x-init="setTimeout(() => show = false, 4000)">
  <div class="flex items-start gap-3 bg-red-600 text-white px-5 py-4 rounded-xl shadow-2xl">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
    </svg>
    <div>
      <p class="text-sm font-semibold">Acción no permitida</p>
      <p class="text-xs mt-0.5 text-red-100">{{ session('error') }}</p>
    </div>
    <button @click="show = false" class="ml-2 text-white/70 hover:text-white">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
</div>
@endif

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-cloak
  class="fixed bottom-5 right-5 z-[999] max-w-sm"
  x-init="setTimeout(() => show = false, 3500)">
  <div class="flex items-start gap-3 bg-green-600 text-white px-5 py-4 rounded-xl shadow-2xl">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <p class="text-sm font-medium">{{ session('success') }}</p>
    <button @click="show = false" class="ml-2 text-white/70 hover:text-white">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
</div>
@endif

<script>
function buscador() {
  return {
    query: @json(request('q', '')),
    abierto: false,
    cargando: false,
    posts: [],
    comunidades: [],
    usuarios: [],
    _timer: null,

    debounceSearch() {
      clearTimeout(this._timer);
      if (this.query.length < 2) { this.cerrar(); return; }
      this._timer = setTimeout(() => this.buscar(), 300);
    },

    async buscar() {
      this.cargando = true;
      this.abierto  = true;
      try {
        const res  = await fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(this.query)}`);
        const data = await res.json();
        this.posts      = data.posts;
        this.comunidades = data.communities;
        this.usuarios    = data.users;
      } catch(e) { console.error(e); }
      this.cargando = false;
    },

    cerrar() {
      this.abierto = false;
    }
  }
}

function notificacionesPanel() {
  return {
    open: false,
    cargando: false,
    notifs: [],
    sinLeer: {{ Auth::user()->unreadNotifications->count() }},

    init() {
      // Escuchar notificaciones en tiempo real via Reverb
      const userId = document.querySelector('meta[name="user-id"]')?.content;
      if (userId && window.Echo) {
        window.Echo.private(`App.Models.User.${userId}`)
          .notification((notification) => {
            this.sinLeer++;
            // Agregar al inicio de la lista si el panel está abierto
            if (this.open) {
              this.notifs.unshift({
                id: notification.id,
                type: notification.type,
                data: notification,
                read_at: null,
                created_at: new Date().toISOString(),
              });
            }
          });
      }
    },

    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch('{{ route('notifications.json') }}');
        const data = await res.json();
        this.notifs = data.notifs;
        this.sinLeer = data.sinLeer;
        if (data.sinLeer > 0) {
          await fetch('{{ route('notifications.readAll') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
          });
          this.sinLeer = 0;
        }
      } catch(e) { console.error(e); }
      this.cargando = false;
    },

    async marcarTodas() {
      await fetch('{{ route('notifications.readAll') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
      });
      this.notifs = this.notifs.map(n => ({ ...n, read_at: new Date().toISOString() }));
      this.sinLeer = 0;
    },

    async irA(n) {
      if (!n.read_at) {
        await fetch(`/notificaciones/${n.id}/leer`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
        n.read_at = new Date().toISOString();
        this.sinLeer = Math.max(0, this.sinLeer - 1);
      }
      if (n.data.url) window.location.href = n.data.url;
      this.open = false;
    },

    // SVG + color de fondo según tipo de notificación
    iconoBg(n) {
      const t = (n.data.titulo || '').toLowerCase();
      if (t.includes('comentario'))                           return 'background:#dbeafe';
      if (t.includes('compartid'))                            return 'background:#e0f2fe';
      if (t.includes('voto') || t.includes('karma'))          return 'background:#fef3c7';
      if (t.includes('reporte'))                              return 'background:#fee2e2';
      if (t.includes('suspendid'))                            return 'background:#fee2e2';
      if (t.includes('reactivad'))                            return 'background:#dcfce7';
      if (t.includes('admin') || t.includes('rol'))           return 'background:#ede9fe';
      return 'background:#eff6ff';
    },

    iconoSvg(n) {
      const t = (n.data.titulo || '').toLowerCase();
      const svg = (color, path) =>
        `<svg width="18" height="18" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="${path}"/></svg>`;

      if (t.includes('comentario'))
        return svg('#1d4ed8', 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z');
      if (t.includes('compartid'))
        return svg('#0369a1', 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z');
      if (t.includes('voto') || t.includes('karma'))
        return svg('#d97706', 'M5 15l7-7 7 7');
      if (t.includes('reporte'))
        return svg('#dc2626', 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H9.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9');
      if (t.includes('suspendid'))
        return svg('#dc2626', 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636');
      if (t.includes('reactivad'))
        return svg('#16a34a', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z');
      if (t.includes('admin') || t.includes('rol'))
        return svg('#7c3aed', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z');
      // default: campana
      return svg('#1d4ed8', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9');
    },

    timeAgo(dateStr) {
      const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
      if (diff < 60) return 'Ahora mismo';
      if (diff < 3600) return `Hace ${Math.floor(diff/60)} min`;
      if (diff < 86400) return `Hace ${Math.floor(diff/3600)} h`;
      return `Hace ${Math.floor(diff/86400)} d`;
    }
  }
}
</script>
