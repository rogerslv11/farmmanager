@props(['icon' => 'search', 'title' => 'Nada por aqui', 'hint' => '', 'variant' => 'default'])
@php
    $palette = [
        'default' => ['#e7eee7', '#a3d3a8', '#336e3c'],
        'agro'    => ['#e0efe0', '#67a76f', '#336e3c'],
        'amber'   => ['#fef3c7', '#fcd34d', '#d97706'],
        'sky'     => ['#dbeafe', '#7dd3fc', '#0284c7'],
        'violet'  => ['#ede9fe', '#c4b5fd', '#7c3aed'],
        'rose'    => ['#ffe4e6', '#fda4af', '#e11d48'],
    ][$variant] ?? ['#e7eee7', '#a3d3a8', '#336e3c'];
@endphp
<div class="panel p-10 text-center relative overflow-hidden">
  <div class="absolute inset-0 deco-grid opacity-50 pointer-events-none"></div>
  <div class="relative">
    <div class="relative mx-auto w-24 h-24 mb-4">
      <svg viewBox="0 0 96 96" class="w-full h-full">
        <circle cx="48" cy="48" r="40" fill="{{ $palette[0] }}" opacity="0.5"/>
        <circle cx="48" cy="48" r="28" fill="{{ $palette[1] }}" opacity="0.4"/>
        <circle cx="48" cy="48" r="18" fill="{{ $palette[1] }}" opacity="0.7"/>
        <g transform="translate(48,48)">
          <g fill="none" stroke="{{ $palette[2] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            @switch($icon)
              @case('search')
                <circle cx="-2" cy="-2" r="6"/><line x1="3" y1="3" x2="8" y2="8"/>
                @break
              @case('home')
                <path d="M-8 4l8-7 8 7v9a1 1 0 0 1-1 1H-7a1 1 0 0 1-1-1z"/><polyline points="-3 14 -3 7 3 7 3 14"/>
                @break
              @case('sprout')
                <path d="M0 9V-1"/><path d="M0 1c0-4 3-7 8-7 0 4-3 7-8 7z"/><path d="M0 1c0-4-3-7-8-7 0 4 3 7 8 7z"/>
                @break
              @case('package')
                <path d="M-9 7V-1l9-5 9 5v8l-9 5z"/><polyline points="-9 -1 0 4 9 -1"/><line x1="0" y1="4" x2="0" y2="14"/>
                @break
              @case('database')
                <ellipse cx="0" cy="-7" rx="9" ry="3"/><path d="M9 -7v13c0 1.66-4 3-9 3s-9-1.34-9-3v-13"/><path d="M9 -1c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                @break
              @case('calendar')
                <rect x="-9" y="-7" width="18" height="16" rx="2"/><line x1="-9" y1="-2" x2="9" y2="-2"/><line x1="-4" y1="-10" x2="-4" y2="-4"/><line x1="4" y1="-10" x2="4" y2="-4"/>
                @break
              @case('users')
                <circle cx="-3" cy="-4" r="4"/><path d="M-10 9v-1a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v1"/><circle cx="6" cy="-3" r="3"/><path d="M11 9v-1a4 4 0 0 0-3-3.87"/>
                @break
              @case('bell')
                <path d="M-6 -1a6 6 0 0 1 12 0c0 7 3 9 3 9h-18s3-2 3-9"/><path d="M-2 11a2 2 0 0 0 4 0"/>
                @break
              @case('chart')
                <line x1="-7" y1="8" x2="7" y2="8"/><line x1="-4" y1="8" x2="-4" y2="2"/><line x1="0" y1="8" x2="0" y2="-4"/><line x1="4" y1="8" x2="4" y2="-1"/>
                @break
              @case('tool')
                <path d="M-7 -2l4-4 4 4-3 3-5 5a2 2 0 0 1-3-3l5-5z"/>
                @break
              @default
                <circle cx="0" cy="0" r="8"/>
            @endswitch
          </g>
        </g>
      </svg>
    </div>
    <p class="font-extrabold text-stone-800 dark:text-stone-100 text-base">{{ $title }}</p>
    @if($hint)<p class="mt-1 text-sm text-stone-400 max-w-sm mx-auto">{{ $hint }}</p>@endif
    @if(trim($slot) !== '')<div class="mt-5">{{ $slot }}</div>@endif
  </div>
</div>