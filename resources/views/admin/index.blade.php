@extends('layouts.app')

@section('content')

<style>
  .admin-shell {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f4f6f9;
  }

  /* ── TOPBAR ─────────────────────────────────────── */
  .admin-topbar {
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
    flex-shrink: 0;
  }

  .admin-brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .admin-brand-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .admin-brand-name {
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    line-height: 1.2;
  }

  .admin-brand-sub {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.6);
  }

  .admin-topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .admin-topbar-link {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color .15s;
  }

  .admin-topbar-link:hover {
    color: #fff;
  }

  .admin-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 10px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .admin-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.5);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
  }

  .admin-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .admin-avatar .marco-overlay {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 10;
  }

  .admin-user-name {
    font-size: 12px;
    color: #fff;
    line-height: 1.2;
  }

  .admin-user-role {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.6);
  }

  .admin-logout-btn {
    background: none;
    border: none;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: color .15s;
    padding: 0;
  }

  .admin-logout-btn:hover {
    color: #fff;
  }

  /* ── BODY LAYOUT ─────────────────────────────────── */
  .admin-body {
    display: flex;
    flex: 1;
    min-height: 0;
  }

  /* ── SIDEBAR ─────────────────────────────────────── */
  .admin-sidebar {
    width: 220px;
    background: #fff;
    border-right: 1px solid #e5e7eb;
    padding: 20px 0;
    flex-shrink: 0;
    position: sticky;
    top: 56px;
    height: calc(100vh - 56px);
    overflow-y: auto;
  }

  .sidebar-section {
    padding: 0 12px;
    margin-bottom: 24px;
  }

  .sidebar-section-label {
    font-size: 10px;
    color: #9ca3af;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 0 8px;
    margin-bottom: 6px;
    font-weight: 500;
  }

  .sidebar-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 7px;
    text-decoration: none;
    margin-bottom: 2px;
    transition: background .15s;
    border-left: 2px solid transparent;
    position: relative;
  }

  .sidebar-nav-item:hover {
    background: #f3f4f6;
  }

  .sidebar-nav-item.active {
    background: #eff6ff;
    border-left-color: #1e40af;
  }

  .sidebar-nav-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    opacity: .4;
    color: #374151;
  }

  .sidebar-nav-item.active .sidebar-nav-icon {
    opacity: 1;
    color: #1e40af;
  }

  .sidebar-nav-text {
    font-size: 13px;
    color: #6b7280;
    flex: 1;
  }

  .sidebar-nav-item.active .sidebar-nav-text {
    color: #1e40af;
    font-weight: 500;
  }

  .sidebar-badge {
    background: #fee2e2;
    color: #dc2626;
    font-size: 10px;
    padding: 1px 7px;
    border-radius: 20px;
    font-weight: 500;
  }

  /* ── MAIN CONTENT ─────────────────────────────────── */
  .admin-main {
    flex: 1;
    padding: 28px 32px;
    min-width: 0;
    overflow-y: auto;
  }

  /* ── BREADCRUMB ──────────────────────────────────── */
  .admin-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 18px;
  }

  .admin-breadcrumb a {
    color: #9ca3af;
    text-decoration: none;
    transition: color .15s;
  }

  .admin-breadcrumb a:hover {
    color: #1e40af;
  }

  .admin-breadcrumb svg {
    width: 14px;
    height: 14px;
    opacity: .5;
  }

  /* ── PAGE HEADER ─────────────────────────────────── */
  .admin-page-header {
    margin-bottom: 24px;
  }

  .admin-page-title {
    font-size: 22px;
    font-weight: 600;
    color: #111827;
    line-height: 1.2;
  }

  .admin-page-sub {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
  }

  /* ── SUCCESS ALERT ───────────────────────────────── */
  .admin-alert-success {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #16a34a;
    background: #f0fdf4;
    color: #15803d;
    font-size: 13px;
  }

  /* ── STATS GRID ──────────────────────────────────── */
  .admin-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
  }

  .admin-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 14px 16px;
  }

  .admin-stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .admin-stat-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
  }

  .admin-stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .admin-stat-icon svg {
    width: 14px;
    height: 14px;
  }

  .admin-stat-value {
    font-size: 28px;
    font-weight: 600;
    color: #111827;
    line-height: 1;
  }

  /* ── TWO-COLUMN SECTION ──────────────────────────── */
  .admin-section-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
  }

  /* ── CARDS ───────────────────────────────────────── */
  .admin-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 18px 20px;
  }

  .admin-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
  }

  .admin-card-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
  }

  .admin-card-link {
    font-size: 11px;
    color: #1e40af;
    text-decoration: none;
    transition: color .15s;
  }

  .admin-card-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
  }

  /* ── REPORTS LIST ────────────────────────────────── */
  .admin-report-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f3f4f6;
  }

  .admin-report-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .admin-report-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ef4444;
    flex-shrink: 0;
    margin-top: 5px;
  }

  .admin-report-body {
    flex: 1;
    min-width: 0;
  }

  .admin-report-title {
    font-size: 13px;
    color: #111827;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .admin-report-meta {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
  }

  .admin-report-meta .reporter {
    color: #374151;
  }

  .admin-report-meta .reason {
    color: #dc2626;
  }

  .admin-report-time {
    font-size: 11px;
    color: #9ca3af;
    flex-shrink: 0;
    margin-top: 3px;
  }

  /* ── QUICK ACTIONS ───────────────────────────────── */
  .admin-quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 9px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    margin-bottom: 10px;
    transition: border-color .15s, background .15s;
    background: #f9fafb;
  }

  .admin-quick-action:hover {
    border-color: #d1d5db;
    background: #f3f4f6;
  }

  .admin-quick-action:last-child {
    margin-bottom: 0;
  }

  .admin-qa-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .admin-qa-icon svg {
    width: 18px;
    height: 18px;
  }

  .admin-qa-body {
    flex: 1;
    min-width: 0;
  }

  .admin-qa-title {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    line-height: 1.3;
  }

  .admin-qa-sub {
    font-size: 11px;
    color: #6b7280;
    margin-top: 1px;
  }

  .admin-qa-arrow {
    color: #d1d5db;
    font-size: 18px;
    flex-shrink: 0;
  }

  .admin-pending-badge {
    background: #fee2e2;
    color: #dc2626;
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 20px;
    font-weight: 500;
    white-space: nowrap;
  }

  /* ── RESPONSIVE ──────────────────────────────────── */
  @media (max-width: 1100px) {
    .admin-stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 900px) {
    .admin-sidebar {
      display: none;
    }

    .admin-section-row {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .admin-main {
      padding: 20px 16px;
    }

    .admin-stats-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
  }
</style>

<div class="admin-shell">

  {{-- ── TOPBAR ──────────────────────────────────────── --}}
  <header class="admin-topbar">
    <div class="admin-brand">
      <div class="admin-brand-icon">T</div>
      <div>
        <div class="admin-brand-name">TecNow</div>
        <div class="admin-brand-sub">Panel de Administración</div>
      </div>
    </div>

    <div class="admin-topbar-right">
      <a href="{{ route('dashboard') }}" class="admin-topbar-link">← Foro</a>

      <div class="admin-user">
        <div class="admin-avatar">
          <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" />
          @if(Auth::user()->marco)
          <img src="{{ asset('marcos/' . Auth::user()->marco) }}" class="marco-overlay" alt="" />
          @endif
        </div>
        <div class="hidden lg:block">
          <div class="admin-user-name">{{ Auth::user()->name }}</div>
          <div class="admin-user-role">Administrador</div>
        </div>
      </div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="admin-logout-btn">Cerrar sesión</button>
      </form>
    </div>
  </header>

  <div class="admin-body">

    {{-- ── SIDEBAR ─────────────────────────────────────── --}}
    <aside class="admin-sidebar">

      <div class="sidebar-section">
        <div class="sidebar-section-label">General</div>

        <a href="{{ route('admin.index') }}"
          class="sidebar-nav-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
          <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          <span class="sidebar-nav-text">Dashboard</span>
        </a>

        <a href="{{ route('admin.reportes') }}"
          class="sidebar-nav-item {{ request()->routeIs('admin.reportes') ? 'active' : '' }}">
          <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
          </svg>
          <span class="sidebar-nav-text">Reportes</span>
          @if($stats['reportesPendientes'] > 0)
          <span class="sidebar-badge">{{ $stats['reportesPendientes'] }}</span>
          @endif
        </a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-label">Gestión</div>

        <a href="{{ route('admin.usuarios') }}"
          class="sidebar-nav-item {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">
          <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="sidebar-nav-text">Usuarios</span>
        </a>

        <a href="{{ route('admin.posts') }}"
          class="sidebar-nav-item {{ request()->routeIs('admin.posts') ? 'active' : '' }}">
          <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span class="sidebar-nav-text">Posts</span>
        </a>

        <a href="{{ route('admin.posts') }}?fijados=1"
          class="sidebar-nav-item">
          <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
          </svg>
          <span class="sidebar-nav-text">Posts fijados</span>
          @if($stats['postsFijados'] > 0)
          <span style="background:#fef3c7;color:#d97706;font-size:10px;padding:1px 7px;border-radius:20px;">
            {{ $stats['postsFijados'] }}
          </span>
          @endif
        </a>
      </div>

    </aside>

    {{-- ── MAIN ─────────────────────────────────────────── --}}
    <main class="admin-main">

      {{-- Breadcrumb --}}
      <nav class="admin-breadcrumb">
        <a href="{{ route('dashboard') }}">Inicio</a>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span style="color:#9ca3af">Administración</span>
      </nav>

      {{-- Page header --}}
      <div class="admin-page-header">
        <h1 class="admin-page-title">Panel de Administración</h1>
        <p class="admin-page-sub">Gestiona usuarios, publicaciones y reportes de TecNow.</p>
      </div>

      {{-- Alerta de éxito --}}
      @if(session('success'))
      <div class="admin-alert-success">
        {{ session('success') }}
      </div>
      @endif

      {{-- Stats cards --}}
      <div class="admin-stats-grid">

        {{-- Usuarios --}}
        <div class="admin-stat-card">
          <div class="admin-stat-top">
            <span class="admin-stat-label">Usuarios registrados</span>
            <div class="admin-stat-icon" style="background:rgba(30,64,175,.15)">
              <svg fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
          </div>
          <div class="admin-stat-value">{{ $stats['usuarios'] }}</div>
        </div>

        {{-- Posts --}}
        <div class="admin-stat-card">
          <div class="admin-stat-top">
            <span class="admin-stat-label">Publicaciones</span>
            <div class="admin-stat-icon" style="background:rgba(22,101,52,.15)">
              <svg fill="none" stroke="#4ade80" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9" />
              </svg>
            </div>
          </div>
          <div class="admin-stat-value">{{ $stats['posts'] }}</div>
        </div>

        {{-- Reportes pendientes --}}
        <div class="admin-stat-card">
          <div class="admin-stat-top">
            <span class="admin-stat-label">Reportes pendientes</span>
            <div class="admin-stat-icon" style="background:rgba(153,27,27,.15)">
              <svg fill="none" stroke="#f87171" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
          </div>
          <div class="admin-stat-value" style="{{ $stats['reportesPendientes'] > 0 ? 'color:#f87171' : '' }}">
            {{ $stats['reportesPendientes'] }}
          </div>
        </div>

        {{-- Posts fijados --}}
        <div class="admin-stat-card">
          <div class="admin-stat-top">
            <span class="admin-stat-label">Posts fijados</span>
            <div class="admin-stat-icon" style="background:rgba(146,64,14,.15)">
              <svg fill="none" stroke="#fbbf24" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
              </svg>
            </div>
          </div>
          <div class="admin-stat-value">{{ $stats['postsFijados'] }}</div>
        </div>

      </div>

      {{-- Sección de dos columnas: reportes recientes + acciones rápidas --}}
      <div class="admin-section-row">

        {{-- Reportes recientes --}}
        @if($reportesRecientes->isNotEmpty())
        <div class="admin-card">
          <div class="admin-card-header">
            <span class="admin-card-title">Reportes recientes pendientes</span>
            <a href="{{ route('admin.reportes') }}" class="admin-card-link">Ver todos →</a>
          </div>

          @foreach($reportesRecientes as $reporte)
          <div class="admin-report-item">
            <div class="admin-report-dot"></div>
            <div class="admin-report-body">
              <div class="admin-report-title">
                {{ $reporte->post?->title ?? '(Post eliminado)' }}
              </div>
              <div class="admin-report-meta">
                Reportado por
                <span class="reporter">{{ $reporte->usuario?->name }}</span>
                ·
                <span class="reason">{{ $reporte->motivo }}</span>
              </div>
            </div>
            <div class="admin-report-time">{{ $reporte->created_at->diffForHumans() }}</div>
          </div>
          @endforeach
        </div>
        @else
        <div class="admin-card" style="display:flex;align-items:center;justify-content:center;min-height:160px">
          <p style="font-size:13px;color:#9ca3af">No hay reportes pendientes.</p>
        </div>
        @endif

        {{-- Acciones rápidas --}}
        <div class="admin-card">
          <div class="admin-card-header">
            <span class="admin-card-title">Acciones rápidas</span>
          </div>

          <a href="{{ route('admin.reportes') }}" class="admin-quick-action">
            <div class="admin-qa-icon" style="background:rgba(153,27,27,.15)">
              <svg fill="none" stroke="#f87171" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
            <div class="admin-qa-body">
              <div class="admin-qa-title">Gestionar Reportes</div>
              <div class="admin-qa-sub">Revisa y resuelve contenido reportado</div>
            </div>
            @if($stats['reportesPendientes'] > 0)
            <span class="admin-pending-badge">
              {{ $stats['reportesPendientes'] }} pendiente{{ $stats['reportesPendientes'] > 1 ? 's' : '' }}
            </span>
            @else
            <span class="admin-qa-arrow">›</span>
            @endif
          </a>

          <a href="{{ route('admin.usuarios') }}" class="admin-quick-action">
            <div class="admin-qa-icon" style="background:rgba(30,64,175,.15)">
              <svg fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div class="admin-qa-body">
              <div class="admin-qa-title">Gestionar Usuarios</div>
              <div class="admin-qa-sub">Roles, suspensiones y cuentas</div>
            </div>
            <span class="admin-qa-arrow">›</span>
          </a>

          <a href="{{ route('admin.posts') }}" class="admin-quick-action">
            <div class="admin-qa-icon" style="background:rgba(22,101,52,.15)">
              <svg fill="none" stroke="#4ade80" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="admin-qa-body">
              <div class="admin-qa-title">Gestionar Posts</div>
              <div class="admin-qa-sub">Fijar destacados y moderar contenido</div>
            </div>
            <span class="admin-qa-arrow">›</span>
          </a>

        </div>

      </div>

    </main>
  </div>
</div>

@endsection