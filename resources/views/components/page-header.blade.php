@props(['title' => '', 'subtitle' => '', 'icon' => null, 'tint' => 'tint-agro'])
<div class="flex flex-wrap items-center gap-4 mb-5 anim-fade-up">
  @if($icon)
    <span class="kpi-tint {{ $tint }} !w-14 !h-14 !rounded-2xl shadow-md"><x-icon :name="$icon" class="w-7 h-7" /></span>
  @endif
  <div class="min-w-0 flex-1">
    <nav class="flex items-center gap-1.5 text-xs text-stone-400 mb-1">
      <a href="{{ route('dashboard') }}" class="hover:text-agro-600 font-medium flex items-center gap-1"><x-icon name="grid" class="w-3 h-3" />Início</a>
      <x-icon name="chevron-right" class="w-3 h-3" />
      <span class="text-stone-600 font-semibold dark:text-stone-300">{{ $title }}</span>
    </nav>
    <h1 class="page-title text-balance">{{ $title }}</h1>
    @if($subtitle)<p class="page-subtitle">{{ $subtitle }}</p>@endif
  </div>
  @if(trim($actions ?? '') !== '')
    <div class="flex items-center gap-2">{{ $actions }}</div>
  @endif
</div>