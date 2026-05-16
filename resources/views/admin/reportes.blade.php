@extends('layouts.app')

@section('content')

<style>
  .ar-shell {
    background: #f4f6f9;
    min-height: 100vh;
  }

  /* TOPBAR */
  .ar-topbar {
    background: #1e40af;
    border-bottom: 1px solid #1d3fa0;
    padding: 0 24px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 50;
  }

  .ar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .ar-brand-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
  }

  .ar-brand-name {
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    line-height: 1.2;
  }

  .ar-brand-sub {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.6);
  }

  .ar-topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .ar-topbar-link {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color .15s;
  }

  .ar-topbar-link:hover {
    color: #fff;
  }

  .ar-logout-btn {
    background: none;
    border: none;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: color .15s;
    padding: 0;
  }

  .ar-logout-btn:hover {
    color: #fff;
  }

  /* CONTENT */
  .ar-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 28px 24px;
  }

  /* BREADCRUMB */
  .ar-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 20px;
  }

  .ar-breadcrumb a {
    color: #9ca3af;
    text-decoration: none;
    transition: color .15s;
  }

  .ar-breadcrumb a:hover {
    color: #1e40af;
  }

  .ar-breadcrumb svg {
    width: 14px;
    height: 14px;
    opacity: .5;
  }

  /* PAGE HEADER */
  .ar-page-title {
    font-size: 22px;
    font-weight: 600;
    color: #111827;
  }

  .ar-page-sub {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
  }

  /* ALERTS */
  .ar-alert-success {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #16a34a;
    background: #f0fdf4;
    color: #15803d;
    font-size: 13px;
  }

  /* EMPTY STATE */
  .ar-empty {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 48px 24px;
    text-align: center;
  }

  .ar-empty svg {
    width: 44px;
    height: 44px;
    color: #d1d5db;
    margin: 0 auto 12px;
  }

  .ar-empty p {
    font-size: 13px;
    color: #9ca3af;
  }

  /* REPORTE CARD */
  .ar-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 12px;
    transition: border-color .15s;
  }

  .ar-card:hover {
    border-color: #d1d5db;
  }

  .ar-card.resuelto {
    opacity: .6;
  }

  .ar-card-inner {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .ar-card-body {
    flex: 1;
    min-width: 0;
  }

  /* BADGES */
  .ar-badge {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    padding: 2px 9px;
    border-radius: 20px;
    font-weight: 500;
  }

  .ar-badge-pendiente {
    background: #fef3c7;
    color: #d97706;
  }

  .ar-badge-eliminado {
    background: #fee2e2;
    color: #dc2626;
  }

  .ar-badge-descartado {
    background: #f3f4f6;
    color: #6b7280;
  }

  .ar-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
  }

  .ar-date {
    font-size: 11px;
    color: #9ca3af;
  }

  .ar-post-line {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
  }

  .ar-post-link {
    color: #1e40af;
    text-decoration: none;
  }

  .ar-post-link:hover {
    text-decoration: underline;
  }

  .ar-post-author {
    color: #6b7280;
    font-weight: 400;
  }

  .ar-reporter {
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 10px;
  }

  .ar-reporter span {
    color: #374151;
  }

  .ar-motivo-box {
    padding: 10px 14px;
    border-radius: 8px;
    background: #fef2f2;
    border: 1px solid #fecaca;
  }

  .ar-motivo-label {
    font-size: 11px;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 3px;
  }

  .ar-motivo-desc {
    font-size: 12px;
    color: #6b7280;
  }

  /* ACCIONES */
  .ar-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
  }

  .ar-btn-discard {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #374151;
    cursor: pointer;
    transition: background .15s;
    text-align: center;
  }

  .ar-btn-discard:hover {
    background: #f3f4f6;
  }

  .ar-btn-delete {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    border: none;
    background: #dc2626;
    color: #fff;
    cursor: pointer;
    transition: background .15s;
    text-align: center;
  }

  .ar-btn-delete:hover {
    background: #b91c1c;
  }

  .ar-resolved-label {
    font-size: 12px;
    color: #9ca3af;
    font-style: italic;
    align-self: flex-start;
  }
</style>

<div class="ar-shell">

  {{-- TOPBAR --}}
  <header class="ar-topbar">
    <div class="ar-brand">
      <div class="ar-brand-icon">T</div>
      <div>
        <div class="ar-brand-name">TecNow</div>
        <div class="ar-brand-sub">Panel de Administración</div>
      </div>
    </div>
    <div class="ar-topbar-right">
      <a href="{{ route('admin.index') }}" class="ar-topbar-link">← Dashboard</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="ar-logout-btn">Cerrar sesión</button>
      </form>
    </div>
  </header>

  <div class="ar-content">

    {{-- Breadcrumb --}}
    <nav class="ar-breadcrumb">
      <a href="{{ route('dashboard') }}">Inicio</a>
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <a href="{{ route('admin.index') }}">Administración</a>
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <span style="color:#6b7280">Reportes</span>
    </nav>

    {{-- Page header --}}
    <div style="margin-bottom:20px">
      <h1 class="ar-page-title">Gestión de Reportes</h1>
      <p class="ar-page-sub">Revisa y resuelve el contenido reportado por la comunidad.</p>
    </div>

    {{-- Alerta éxito --}}
    @if(session('success'))
    <div class="ar-alert-success">{{ session('success') }}</div>
    @endif

    {{-- Sin reportes --}}
    @if($reportes->isEmpty())
    <div class="ar-empty">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p>No hay reportes registrados.</p>
    </div>

    @else
    @foreach($reportes as $reporte)
    @php
    $esResuelto = $reporte->estado !== 'pendiente';
    $badgeClass = match($reporte->estado) {
    'pendiente' => 'ar-badge-pendiente',
    'resuelto_eliminado' => 'ar-badge-eliminado',
    'resuelto_descartado' => 'ar-badge-descartado',
    default => 'ar-badge-descartado',
    };
    $badgeTexto = match($reporte->estado) {
    'pendiente' => 'Pendiente',
    'resuelto_eliminado' => 'Eliminado',
    'resuelto_descartado' => 'Descartado',
    default => $reporte->estado,
    };
    @endphp

    <div class="ar-card {{ $esResuelto ? 'resuelto' : '' }}">
      <div class="ar-card-inner">

        <div class="ar-card-body">
          <div class="ar-meta">
            <span class="ar-badge {{ $badgeClass }}">{{ $badgeTexto }}</span>
            <span class="ar-date">{{ $reporte->created_at->format('d/m/Y H:i') }}</span>
          </div>

          {{-- Tipo de reporte --}}
          @if($reporte->comment_id)
          <p class="ar-post-line">
            💬 Comentario reportado:
            @if($reporte->comment)
            <span style="color:#374151;font-weight:400">"{{ \Illuminate\Support\Str::limit($reporte->comment->content, 80) }}"</span>
            <span class="ar-post-author">— por {{ $reporte->comment->user?->name ?? 'Usuario desconocido' }}</span>
            @if($reporte->comment->post_id)
            · <a href="{{ route('posts.show', $reporte->comment->post_id) }}" target="_blank" class="ar-post-link">Ver post</a>
            @endif
            @else
            <span style="color:#9ca3af;font-weight:400;font-style:italic">Comentario eliminado</span>
            @endif
          </p>
          @else
          <p class="ar-post-line">
            📄 Post:
            @if($reporte->post)
            <a href="{{ route('posts.show', $reporte->post) }}" target="_blank" class="ar-post-link">
              {{ $reporte->post->title }}
            </a>
            <span class="ar-post-author">— por {{ $reporte->post->user?->name ?? 'Usuario desconocido' }}</span>
            @else
            <span style="color:#9ca3af;font-weight:400;font-style:italic">Post eliminado</span>
            @endif
          </p>
          @endif

          <p class="ar-reporter">
            Reportado por <span>{{ $reporte->usuario?->name ?? 'Usuario desconocido' }}</span>
            @if($reporte->reportedUser)
            · Usuario reportado: <span>{{ $reporte->reportedUser->name }}</span>
            @if(!$reporte->reportedUser->activo)
            <span style="color:#dc2626;font-size:11px"> (suspendido)</span>
            @endif
            @endif
          </p>

          <div class="ar-motivo-box">
            <p class="ar-motivo-label">Motivo: {{ $reporte->motivo }}</p>
            @if($reporte->descripcion)
            <p class="ar-motivo-desc">{{ $reporte->descripcion }}</p>
            @endif
          </div>
        </div>

        {{-- Acciones --}}
        @if(!$esResuelto && ($reporte->post || $reporte->comment))
        <div class="ar-actions">
          <form action="{{ route('admin.reportes.resolver', $reporte) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="accion" value="descartar">
            <button type="submit" class="ar-btn-discard">Descartar</button>
          </form>
          <form action="{{ route('admin.reportes.resolver', $reporte) }}" method="POST"
            onsubmit="return confirm('¿Eliminar el contenido? Esta acción no se puede deshacer.')">
            @csrf @method('PATCH')
            <input type="hidden" name="accion" value="eliminar">
            <button type="submit" class="ar-btn-delete">Eliminar contenido</button>
          </form>
          @if($reporte->reportedUser && $reporte->reportedUser->activo)
          <form action="{{ route('admin.reportes.resolver', $reporte) }}" method="POST"
            onsubmit="return confirm('¿Suspender a {{ $reporte->reportedUser->name }}?')">
            @csrf @method('PATCH')
            <input type="hidden" name="accion" value="suspender">
            <button type="submit" style="padding:8px 16px;border-radius:8px;font-size:12px;font-weight:500;border:none;background:#7c3aed;color:#fff;cursor:pointer;transition:background .15s;width:100%">
              Suspender usuario
            </button>
          </form>
          @endif
        </div>
        @elseif($esResuelto)
        <p class="ar-resolved-label">Reporte resuelto</p>
        @else
        <p class="ar-resolved-label">Contenido ya eliminado</p>
        @endif

      </div>
    </div>
    @endforeach

    <div class="mt-6">{{ $reportes->links() }}</div>
    @endif

  </div>
</div>
@endsection