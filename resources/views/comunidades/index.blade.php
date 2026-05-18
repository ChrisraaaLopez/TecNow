@extends('layouts.app')

@section('content')
<x-navbar />

<div class="max-w-4xl mx-auto px-4 py-8">

  {{-- Encabezado --}}
  <div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
      <a href="{{ route('dashboard') }}" class="hover:text-blue-400 transition-colors">Inicio</a>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <span class="text-gray-700">Explorar comunidades</span>
    </div>
    <h1 class="text-2xl font-bold">Todas las comunidades</h1>
    <p class="text-sm text-gray-400 mt-1">Únete a las comunidades que te interesen</p>
  </div>

  {{-- Comunidad de Mejoras (destacada) --}}
  @php $mejoras = $communities->where('tipo', 'general')->first(); @endphp
  @if($mejoras)
  <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl p-5 mb-8 text-white">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-3xl bg-white/15">
          {{ $mejoras->icon }}
        </div>
        <div>
          <h2 class="text-lg font-bold">{{ $mejoras->name }}</h2>
          <p class="text-sm text-blue-100 mt-0.5">{{ $mejoras->description }}</p>
          <p class="text-xs text-blue-200 mt-1">{{ $mejoras->users_count }} {{ $mejoras->users_count === 1 ? 'miembro' : 'miembros' }}</p>
        </div>
      </div>
      @if($userCommunityIds->contains($mejoras->id))
        <form action="{{ route('communities.leave', $mejoras) }}" method="POST">
          @csrf
          <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-white/20 hover:bg-white/30 transition-colors border border-white/30">
            ✓ Unido · Salir
          </button>
        </form>
      @else
        <form action="{{ route('communities.join', $mejoras) }}" method="POST">
          @csrf
          <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-white text-blue-700 hover:bg-blue-50 transition-colors">
            + Unirse
          </button>
        </form>
      @endif
    </div>
  </div>
  @endif

  {{-- Avisos institucionales --}}
  @php $avisos = $communities->where('tipo', 'avisos'); @endphp
  @if($avisos->isNotEmpty())
  <div class="mb-8">
    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3 px-1">📢 Oficial</h2>
    <div class="space-y-3">
      @foreach($avisos as $c)
      <div class="bg-card border border-border rounded-xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-4 min-w-0">
          <x-community-icon :community="$c" size="md" />
          <div class="min-w-0">
            <a href="{{ route('communities.show', $c) }}" class="font-semibold hover:text-primary transition-colors">{{ $c->name }}</a>
            @if($c->description)
            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $c->description }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">{{ $c->users_count }} {{ $c->users_count === 1 ? 'miembro' : 'miembros' }}</p>
          </div>
        </div>
        <div class="flex-shrink-0">
          @if($userCommunityIds->contains($c->id))
            <form action="{{ route('communities.leave', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-500 hover:bg-red-50 hover:border-red-300 hover:text-red-500 transition-colors">
                ✓ Unido · Salir
              </button>
            </form>
          @else
            <form action="{{ route('communities.join', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-primary text-white hover:bg-blue-700 transition-colors">
                + Unirse
              </button>
            </form>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Carreras --}}
  @php $carreras = $communities->where('tipo', 'carrera'); @endphp
  @if($carreras->isNotEmpty())
  <div class="mb-8">
    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3 px-1">🎓 Comunidades por carrera</h2>
    <div class="space-y-3">
      @foreach($carreras as $c)
      <div class="bg-card border border-border rounded-xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-4 min-w-0">
          <x-community-icon :community="$c" size="md" />
          <div class="min-w-0">
            <a href="{{ route('communities.show', $c) }}" class="font-semibold hover:text-primary transition-colors">{{ $c->name }}</a>
            @if($c->description)
            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $c->description }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">{{ $c->users_count }} {{ $c->users_count === 1 ? 'miembro' : 'miembros' }}</p>
          </div>
        </div>
        <div class="flex-shrink-0">
          @if($userCommunityIds->contains($c->id))
            <form action="{{ route('communities.leave', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-500 hover:bg-red-50 hover:border-red-300 hover:text-red-500 transition-colors whitespace-nowrap">
                ✓ Unido · Salir
              </button>
            </form>
          @else
            <form action="{{ route('communities.join', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-primary text-white hover:bg-blue-700 transition-colors">
                + Unirse
              </button>
            </form>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Otras comunidades generales (excluyendo mejoras que ya está arriba) --}}
  @php $generales = $communities->where('tipo', 'general')->skip(1); @endphp
  @if($generales->count() > 0)
  <div class="mb-8">
    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3 px-1">💬 Generales</h2>
    <div class="space-y-3">
      @foreach($generales as $c)
      <div class="bg-card border border-border rounded-xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-4 min-w-0">
          <x-community-icon :community="$c" size="md" />
          <div class="min-w-0">
            <a href="{{ route('communities.show', $c) }}" class="font-semibold hover:text-primary transition-colors">{{ $c->name }}</a>
            @if($c->description)
            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $c->description }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">{{ $c->users_count }} {{ $c->users_count === 1 ? 'miembro' : 'miembros' }}</p>
          </div>
        </div>
        <div class="flex-shrink-0">
          @if($userCommunityIds->contains($c->id))
            <form action="{{ route('communities.leave', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-500 hover:bg-red-50 hover:border-red-300 hover:text-red-500 transition-colors">
                ✓ Unido · Salir
              </button>
            </form>
          @else
            <form action="{{ route('communities.join', $c) }}" method="POST">
              @csrf
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-primary text-white hover:bg-blue-700 transition-colors">
                + Unirse
              </button>
            </form>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

</div>
@endsection
