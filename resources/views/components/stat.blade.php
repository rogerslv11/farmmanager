@props([
    'icon' => 'grid',
    'label' => '',
    'value' => '',
    'hint' => '',
    'tint' => 'tint-agro',
    'delta' => null,
    'deltaGood' => true,
    'spark' => null,        // array of small numbers to draw a tiny trend line
    'accent' => false,      // true → special bordered/elevated look
])
<div class="stat {{ $accent ? 'stat-pro text-' . str_replace('tint-','',$tint) : '' }}">
  <div class="flex items-start justify-between gap-3 relative z-[1]">
    <div class="min-w-0">
      <p class="text-xs font-semibold text-stone-500 dark:text-stone-400 truncate">{{ $label }}</p>
      <p class="mt-1.5 text-[26px] leading-none font-extrabold tracking-tight text-stone-900 tabular-nums dark:text-white">{{ $value }}</p>
      <div class="mt-1.5 flex items-center gap-1.5 min-h-[20px]">
        @if(!is_null($delta))
          <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-extrabold tabular-nums {{ $deltaGood ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-300' }}">
            <svg viewBox="0 0 24 24" class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="{{ $deltaGood ? '6 14 12 8 18 14' : '6 10 12 16 18 10' }}"/></svg>
            {{ abs($delta) }}%
          </span>
          <span class="text-[11px] text-stone-400">vs mês anterior</span>
        @elseif($hint)<span class="text-xs text-stone-400 truncate">{{ $hint }}</span>@endif
      </div>
    </div>
    <span class="kpi-tint {{ $tint }}"><x-icon :name="$icon" class="w-5 h-5" /></span>
  </div>
  @if(is_array($spark) && count($spark) > 1)
    @php
      $max = max($spark); $min = min($spark);
      $range = max(0.0001, $max - $min);
      $w = 100; $h = 28;
      $points = collect($spark)->map(function ($v, $i) use ($spark, $max, $min, $range, $w, $h) {
          $x = $i * ($w / max(1, count($spark) - 1));
          $y = $h - (($v - $min) / $range) * $h;
          return round($x, 1).','.round($y, 1);
      })->implode(' ');
      $area = "0,{$h} {$points} {$w},{$h}";
    @endphp
    <svg class="sparkline mt-3 w-full h-7 relative z-[1]" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none">
      <defs>
        <linearGradient id="sg-{{ md5($label.$value) }}" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="currentColor" stop-opacity="0.35"/>
          <stop offset="100%" stop-color="currentColor" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <polygon class="text-{{ str_replace('tint-','',$tint) }}" fill="url(#sg-{{ md5($label.$value) }})" points="{{ $area }}"/>
      <polyline class="text-{{ str_replace('tint-','',$tint) }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $points }}"/>
    </svg>
  @endif
</div>