<div class="anim-fade-up">
  <div class="grid lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="shield" label="Eventos totais" :value="$items->total()" hint="rastros no sistema" tint="tint-stone" />
    <x-stat icon="user" label="Usuários" :value="$items->getCollection()->pluck('user_id')->filter()->unique()->count()" hint="que operaram" tint="tint-sky" />
    <x-stat icon="cog" label="Eventos hoje" :value="$items->getCollection()->filter(fn ($l) => $l->created_at && $l->created_at->isToday())->count()" hint="últimas 24h" tint="tint-agro" />
    <x-stat icon="alert" label="Última hora" :value="$items->getCollection()->filter(fn ($l) => $l->created_at && $l->created_at->gt(now()->subHour()))->count()" hint="atividade recente" tint="tint-orange" />
  </div>

  @php $heat = app(\App\Services\ChartService::class)->auditActivityHeatmap(); $max = max(array_column($heat, 'c') ?: [1]); @endphp
  <div class="card mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-stone-100/50 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-stone !w-9 !h-9 !rounded-lg"><x-icon name="activity" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Mapa de atividade</h3>
            <p class="text-xs text-stone-400">Últimos 60 dias · eventos por dia da semana e hora</p>
          </div>
        </div>
        <div class="flex items-center gap-2 text-[10px] text-stone-400 font-semibold uppercase tracking-wider">
          <span>Menos</span>
          <div class="flex gap-0.5">
            <div class="w-4 h-4 rounded heat-0"></div>
            <div class="w-4 h-4 rounded heat-1"></div>
            <div class="w-4 h-4 rounded heat-2"></div>
            <div class="w-4 h-4 rounded heat-3"></div>
            <div class="w-4 h-4 rounded heat-4"></div>
          </div>
          <span>Mais</span>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="border-separate" style="border-spacing: 3px;">
          <thead>
            <tr>
              <th></th>
              @for($h = 0; $h < 24; $h++)
                <th class="text-[9px] font-bold text-stone-400 text-center w-7" style="transform: translateY(0);">{{ $h % 3 === 0 ? str_pad($h, 2, '0', STR_PAD_LEFT) : '' }}</th>
              @endfor
            </tr>
          </thead>
          <tbody>
            @foreach(['SEG','TER','QUA','QUI','SEX','SAB','DOM'] as $dIdx => $day)
              <tr>
                <td class="text-[10px] font-bold text-stone-400 pr-2 text-right">{{ $day }}</td>
                @for($h = 0; $h < 24; $h++)
                  @php $entry = collect($heat)->firstWhere(fn ($e) => $e['d'] === $dIdx && $e['h'] === $h); $c = $entry['c'] ?? 0; $level = $c === 0 ? 0 : ($c <= max(1, $max * 0.15) ? 1 : ($c <= max(1, $max * 0.4) ? 2 : ($c <= max(1, $max * 0.7) ? 3 : 4))); @endphp
                  <td class="w-7 h-7 rounded heat-{{ $level }} relative group cursor-default" title="{{ $day }} · {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}h · {{ $c }} {{ $c === 1 ? 'evento' : 'eventos' }}">
                    @if($c > 0)<span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold {{ $level >= 3 ? 'text-white' : 'text-stone-600' }} opacity-0 group-hover:opacity-100 transition">{{ $c }}</span>@endif
                  </td>
                @endfor
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar nos logs…" class="search-input"></div>
    <span class="text-xs text-stone-400 font-medium ml-1">quem fez o quê, quando e de onde</span>
  </div>
  <div class="card !p-0 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full">
      <thead><tr><th class="table-th">Quando</th><th class="table-th">Usuário</th><th class="table-th">Ação</th><th class="table-th">Detalhe</th></tr></thead>
      <tbody>@forelse($items as $l)
        <tr>
          <td class="table-td text-xs whitespace-nowrap tabular-nums">{{ $l->created_at?->format('d/m H:i') }}</td>
          <td class="table-td"><div class="flex items-center gap-2"><span class="avatar w-7 h-7 text-[10px]">{{ strtoupper(substr($l->user?->name ?? 'S', 0, 1)) }}</span>{{ $l->user?->name ?? 'Sistema' }}</div></td>
          <td class="table-td"><x-badge :color="$l->action === 'created' ? 'green' : ($l->action === 'deleted' ? 'red' : 'sky')">{{ $l->action }}</x-badge></td>
          <td class="table-td text-xs">{{ $l->description }}@if($l->old_values && $l->new_values)<br><span class="text-stone-400">{{ json_encode($l->old_values) }} → {{ json_encode($l->new_values) }}</span>@endif</td>
        </tr>
      @empty <tr><td colspan="4" class="!border-0"><x-empty icon="shield" title="Nenhum log" hint="Os eventos do sistema aparecerão aqui." /></td></tr> @endforelse</tbody>
    </table></div>
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
</div>