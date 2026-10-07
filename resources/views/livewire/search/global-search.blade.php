<div x-data="{ open: false }" @open-global-search.window="open = true; $nextTick(() => $refs.q?.focus())">
  <div x-show="open" x-cloak class="fixed inset-0 z-[90]" style="display:none">
    <div class="absolute inset-0 bg-stone-950/50 backdrop-blur-[2px]" @click="open = false; $wire.set('query', '')"></div>
    <div class="absolute left-1/2 top-[12vh] -translate-x-1/2 w-[min(640px,92vw)] bg-white rounded-2xl shadow-2xl border border-stone-200/70 overflow-hidden dark:bg-stone-900 dark:border-white/10" style="animation: modal-in .2s cubic-bezier(.16,1,.3,1)">
      <div class="flex items-center gap-3 px-5 py-4 border-b border-stone-100 dark:border-white/10">
        <x-icon name="search" class="w-5 h-5 text-agro-600 shrink-0" />
        <input x-ref="q" wire:model.live.debounce.300ms="query" @keydown.escape.window="open = false" type="text" placeholder="Digite para buscar em todos os módulos…" class="w-full text-[15px] outline-none bg-transparent placeholder:text-stone-400">
        <span class="kbd">esc</span>
      </div>
      <div class="max-h-[50vh] overflow-y-auto p-2.5">
        @forelse($results ?? [] as $group => $items)
          @if($items->count())
            <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-[0.1em] text-stone-400">{{ $group }}</p>
            @foreach($items as $r)
              <a href="{{ $r['url'] }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-agro-50 text-sm font-medium text-stone-700 transition dark:hover:bg-white/5 dark:text-stone-200">
                <x-icon name="arrow-left" class="w-3.5 h-3.5 text-stone-300 rotate-180" />{{ $r['label'] }}
              </a>
            @endforeach
          @endif
        @empty
          <div class="p-6 text-center">
            <p class="text-sm font-semibold text-stone-600 dark:text-stone-300">Busca global do FarmManager</p>
            <p class="text-xs text-stone-400 mt-1">Digite ao menos 2 letras para pesquisar propriedades, talhões, safras, produtos, pessoas e máquinas.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
