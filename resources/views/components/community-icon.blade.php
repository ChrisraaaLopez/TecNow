@props(['community', 'size' => 'md'])

@php
  $slug = $community->slug ?? '';
  $tipo = $community->tipo ?? 'carrera';

  // Tamaños del contenedor y el SVG
  $sizes = [
    'xs' => ['container' => 'w-7 h-7 rounded-lg',       'svg' => 'w-3.5 h-3.5'],
    'sm' => ['container' => 'w-9 h-9 rounded-xl',        'svg' => 'w-4 h-4'],
    'md' => ['container' => 'w-11 h-11 rounded-xl',      'svg' => 'w-5 h-5'],
    'lg' => ['container' => 'w-14 h-14 rounded-2xl',     'svg' => 'w-7 h-7'],
    'xl' => ['container' => 'w-16 h-16 rounded-2xl',     'svg' => 'w-8 h-8'],
  ];
  $s = $sizes[$size] ?? $sizes['md'];

  // Mapa slug → [bg, color, paths]
  $icons = [

    // Terminal/código — representa programación y sistemas
    'ingenieria-en-sistemas-computacionales' => [
      'bg'    => '#dbeafe',
      'color' => '#1e40af',
      'paths' => [
        'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
      ],
    ],

    // Rayo — electricidad y mecánica
    'ingenieria-electromecanica' => [
      'bg'    => '#fef9c3',
      'color' => '#a16207',
      'paths' => [
        'M13 10V3L4 14h7v7l9-11h-7z',
      ],
    ],

    // Edificio con columnas — ingeniería de estructuras y obras
    'ingenieria-civil' => [
      'bg'    => '#f1f5f9',
      'color' => '#334155',
      'paths' => [
        'M3 21h18M3 7h18M6 21V7m4 14V7m4 14V7m4 14V7',
      ],
    ],

    // Gráfica de barras — gestión y análisis empresarial
    'ingenieria-en-gestion-empresarial' => [
      'bg'    => '#dcfce7',
      'color' => '#15803d',
      'paths' => [
        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
      ],
    ],

    // Lápiz/pluma técnica — diseño arquitectónico y planos
    'licenciatura-en-arquitectura' => [
      'bg'    => '#ede9fe',
      'color' => '#6d28d9',
      'paths' => [
        'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
      ],
    ],

    // Microchip/CPU — electrónica + mecánica = mecatrónica
    'ingenieria-mecatronica' => [
      'bg'    => '#ffedd5',
      'color' => '#c2410c',
      'paths' => [
        'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18',
        'M12 9h.01M12 12h.01M12 15h.01',
      ],
    ],

    // Matraz/beaker — ciencia y procesos de alimentos
    'ingenieria-en-industrias-alimentarias' => [
      'bg'    => '#d1fae5',
      'color' => '#065f46',
      'paths' => [
        'M9 3h6v6l3 9H6L9 9V3zM9 3H7m10 0h-2M10 7h4',
      ],
    ],

    // Volante/auto — ingeniería automotriz
    'ingenieria-sistemas-automotrices' => [
      'bg'    => '#fee2e2',
      'color' => '#991b1b',
      'paths' => [
        'M12 2a10 10 0 100 20A10 10 0 0012 2zm0 0v4m0 16v-4m-8-8H2m20 0h-2M5.636 5.636l2.829 2.829m7.07 7.07l2.829 2.829M5.636 18.364l2.829-2.829m7.07-7.07l2.829-2.829',
        'M12 10a2 2 0 100 4 2 2 0 000-4z',
      ],
    ],

    // Fábrica — producción y procesos industriales
    'ingenieria-industrial' => [
      'bg'    => '#e0f2fe',
      'color' => '#0369a1',
      'paths' => [
        'M3 21h18M9 21V8l3-5 3 5v13M3 21V11l3-3m12 13V11l-3-3',
        'M9 12h6',
      ],
    ],

    // Campana — avisos oficiales, fondo sólido para máximo impacto
    'avisos-institucionales' => [
      'bg'    => '#1e40af',
      'color' => '#ffffff',
      'paths' => [
        'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
      ],
    ],

  ];

  // Fallback genérico para comunidades no listadas
  $cfg = $icons[$slug] ?? [
    'bg'    => 'rgba(30,64,175,0.1)',
    'color' => '#1e40af',
    'paths' => [
      'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    ],
  ];
@endphp

<div class="{{ $s['container'] }} flex items-center justify-center flex-shrink-0"
     style="background: {{ $cfg['bg'] }}">
  <svg class="{{ $s['svg'] }}" fill="none" stroke="{{ $cfg['color'] }}"
       stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
       viewBox="0 0 24 24">
    @foreach($cfg['paths'] as $path)
      <path d="{{ $path }}" />
    @endforeach
  </svg>
</div>
