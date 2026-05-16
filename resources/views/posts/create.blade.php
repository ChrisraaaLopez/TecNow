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
    <span class="text-gray-700">Nueva publicación</span>
  </div>
</div>

{{-- MAIN --}}
<div class="max-w-[1400px] mx-auto px-6 pb-12">
  <div class="max-w-2xl mx-auto" x-data="postForm()">

    <div class="mb-6">
      <h1 class="text-2xl font-bold">Nueva publicación</h1>
      <p class="text-sm text-gray-400 mt-1">Comparte algo con tu comunidad</p>
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-400/20 border border-red-700 rounded-lg p-4">
      <p class="text-sm text-red-400">{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-400/20 border border-red-700 rounded-lg p-4">
      <p class="text-sm font-semibold text-red-400 mb-2">Por favor corrige los siguientes errores:</p>
      <ul class="text-sm text-red-500 space-y-1 list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <div class="bg-card border border-border rounded-lg overflow-hidden">

        {{-- Info del autor --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-border">
          <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary relative flex-shrink-0">
            <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" />
            @if(Auth::user()->marco)
            <img src="{{ asset('marcos/' . Auth::user()->marco) }}" class="absolute inset-0 w-full h-full object-cover z-10" />
            @endif
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-400">&#64;{{ Auth::user()->username }}</p>
          </div>
        </div>

        <div class="p-5 space-y-4">

          {{-- Título --}}
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-1.5">
              Título <span class="text-red-400">*</span>
            </label>
            <input
              type="text"
              name="title"
              value="{{ old('title') }}"
              placeholder="Dale un título a tu publicación..."
              maxlength="150"
              required
              class="w-full px-4 py-2.5 rounded-lg border bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-primary transition-colors
                     {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300' }}" />
            @error('title')
            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Comunidad --}}
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-1.5">
              Comunidad <span class="text-gray-500 font-normal">(opcional)</span>
            </label>
            @php
              $avisos   = $communities->where('tipo', 'avisos');
              $carreras = $communities->where('tipo', 'carrera');
              $sinComunidades = $carreras->isEmpty() && $avisos->isEmpty();
            @endphp
            @if($sinComunidades && Auth::user()->rol !== 'admin')
              {{-- Sin comunidades unidas --}}
              <div class="flex items-center gap-3 px-4 py-3 rounded-lg border border-yellow-300 bg-yellow-50 text-sm text-yellow-700">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>
                  No perteneces a ninguna comunidad todavía.
                  <a href="{{ route('dashboard') }}" class="underline font-medium hover:text-yellow-900">Únete a una</a>
                  para poder publicar en ella.
                </span>
              </div>
              <input type="hidden" name="community_id" value="">
            @else
              <select
                name="community_id"
                class="w-full px-4 py-2.5 rounded-lg border bg-gray-100 text-gray-800 focus:outline-none focus:border-primary transition-colors
                       {{ $errors->has('community_id') ? 'border-red-500' : 'border-gray-300' }}">
                <option value="">Sin comunidad (foro general)</option>
                @if($avisos->count() && Auth::user()->rol === 'admin')
                <optgroup label="📢 Oficial">
                  @foreach($avisos as $c)
                  <option value="{{ $c->id }}" {{ old('community_id', $selectedCommunityId) == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                  </option>
                  @endforeach
                </optgroup>
                @endif
                @if($carreras->count())
                <optgroup label="🎓 Mis comunidades">
                  @foreach($carreras as $c)
                  <option value="{{ $c->id }}" {{ old('community_id', $selectedCommunityId) == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                  </option>
                  @endforeach
                </optgroup>
                @endif
              </select>
            @endif
            @error('community_id')
            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Contenido --}}
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-1.5">
              Contenido <span class="text-red-400">*</span>
            </label>
            <textarea
              name="content"
              rows="6"
              placeholder="¿Qué quieres compartir con tu comunidad?"
              required
              x-model="content"
              class="w-full px-4 py-2.5 rounded-lg border bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:border-primary transition-colors resize-none
                     {{ $errors->has('content') ? 'border-red-500' : 'border-gray-300' }}">{{ old('content') }}</textarea>
            <div class="flex justify-between mt-1">
              @error('content')
              <p class="text-xs text-red-400">{{ $message }}</p>
              @else
              <span></span>
              @enderror
              <p class="text-xs text-gray-500 text-right" x-text="content.length + ' caracteres'"></p>
            </div>
          </div>

          {{-- Imagen (opcional) --}}
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-1.5">
              Imagen <span class="text-gray-500 font-normal">(opcional)</span>
            </label>
            <div
              class="relative border-2 border-dashed rounded-lg transition-colors cursor-pointer
                     {{ $errors->has('image') ? 'border-red-500' : 'border-gray-300 hover:border-primary' }}"
              @click="$refs.imageInput.click()"
              @dragover.prevent="dragging = true"
              @dragleave.prevent="dragging = false"
              @drop.prevent="handleDrop($event)"
              :class="dragging ? 'border-primary bg-blue-50' : ''">

              {{-- Preview --}}
              <div x-show="preview" class="relative">
                <img :src="preview" class="w-full max-h-64 object-cover rounded-lg" />
                <button type="button" @click.stop="clearImage()"
                  class="absolute top-2 right-2 bg-black/70 hover:bg-black text-white rounded-full p-1.5 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              {{-- Placeholder --}}
              <div x-show="!preview" class="flex flex-col items-center justify-center py-8 px-4 text-center">
                <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm text-gray-400">Arrastra una imagen o <span class="text-primary">haz clic para seleccionar</span></p>
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF, WEBP — máx. 2 MB</p>
              </div>

              <input type="file" name="image" accept="image/*" class="hidden"
                x-ref="imageInput" @change="handleFile($event)" />
            </div>
            @error('image')
            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

        </div>
      </div>

      {{-- Acciones --}}
      <div class="flex items-center justify-between gap-3">
        <a href="{{ route('dashboard') }}"
          class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors text-sm">
          Cancelar
        </a>
        <button type="submit"
          class="px-6 py-2.5 rounded-lg text-white hover:bg-blue-700 transition-colors text-sm font-medium flex items-center gap-2"
          style="background:#1e40af">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Publicar
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  function postForm() {
    return {
      content: `{{ old('content') }}`,
      preview: null,
      dragging: false,

      handleFile(event) {
        const file = event.target.files[0];
        if (file) this.loadPreview(file);
      },

      handleDrop(event) {
        this.dragging = false;
        const file = event.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
          const dt = new DataTransfer();
          dt.items.add(file);
          this.$refs.imageInput.files = dt.files;
          this.loadPreview(file);
        }
      },

      loadPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => { this.preview = e.target.result; };
        reader.readAsDataURL(file);
      },

      clearImage() {
        this.preview = null;
        this.$refs.imageInput.value = '';
      }
    }
  }
</script>

@endsection
