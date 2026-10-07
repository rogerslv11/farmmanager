<div x-data="{ open: false }" class="relative">
  <button @click="open = !open" class="icon-btn" aria-label="Notificações">
    <x-icon name="bell" class="w-5 h-5" />
    @if($unread > 0)<span class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-stone-900">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
  </button>
  <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-[340px] panel !p-0 shadow-2xl overflow-hidden z-50" style="display:none; animation: modal-in .18s ease-out">
    <div class="flex items-center justify-between px-4 py-3 border-b border-stone-100 dark:border-white/10">
      <p class="font-bold text-sm">Notificações @if($unread > 0)<span class="badge badge-red ml-1">{{ $unread }} novas</span>@endif</p>
      <button wire:click="markAllRead" class="link-action">Marcar todas</button>
    </div>
    <div class="max-h-80 overflow-y-auto">
      @forelse($notifications as $n)
        <div class="flex gap-3 px-4 py-3 border-b border-stone-100 last:border-0 dark:border-white/5 {{ is_null($n->read_at) ? 'bg-agro-50/70 dark:bg-agro-500/5' : '' }}">
          <span class="timeline-dot {{ is_null($n->read_at) ? 'bg-agro-500 ring-agro-200' : 'bg-stone-300 ring-stone-100' }}"></span>
          <div class="min-w-0 flex-1">
            <p class="text-[13px] font-bold text-stone-800 dark:text-stone-100">{{ $n->data['title'] ?? 'Aviso' }}</p>
            <p class="text-xs text-stone-500 leading-relaxed">{{ $n->data['message'] ?? '' }}</p>
            <div class="flex items-center gap-3 mt-1.5">
              @if(!empty($n->data['url']))<a href="{{ $n->data['url'] }}" class="link-action">Ver detalhes →</a>@endif
              @if(is_null($n->read_at))<button wire:click="markRead('{{ $n->id }}')" class="text-xs font-medium text-stone-400 hover:text-stone-600">Marcar como lida</button>@endif
            </div>
          </div>
        </div>
      @empty
        <div class="p-8 text-center">
          <span class="kpi-tint tint-agro mx-auto"><x-icon name="bell" class="w-5 h-5" /></span>
          <p class="mt-2 text-sm font-semibold text-stone-600 dark:text-stone-300">Tudo em dia</p>
          <p class="text-xs text-stone-400">Nenhuma notificação no momento.</p>
        </div>
      @endforelse
    </div>
    <a href="{{ route('notifications.index') }}" class="block text-center text-xs font-bold text-agro-700 py-2.5 bg-stone-50 hover:bg-agro-50 transition dark:bg-white/5 dark:text-agro-400">Ver todas as notificações</a>
  </div>
</div>
