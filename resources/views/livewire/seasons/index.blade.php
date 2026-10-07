<div class="anim-fade-up">
  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar safra…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-44"><option value="">Todos status</option><option value="planejada">Planejada</option><option value="em_andamento">Em andamento</option><option value="colheita">Colheita</option><option value="finalizada">Finalizada</option><option value="cancelada">Cancelada</option></select>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova safra</button>
  </div>

  @php
    $totalArea = $items->sum('total_area');
    $totalEst = $items->sum('estimated_production');
    $totalCost = $items->sum('estimated_cost');
    $totalRev = $items->sum('estimated_revenue');
  @endphp

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="calendar" label="Safras no portfólio" :value="$items->total()" hint="planejadas e em andamento" tint="tint-agro" />
    <x-stat icon="globe" label="Área total" :value="number_format($totalArea, 0, ',', '.').' ha'" hint="somatório das safras" tint="tint-earth" />
    <x-stat icon="package" label="Produção estimada" :value="number_format($totalEst, 0, ',', '.')" hint="acumulado" tint="tint-orange" />
    <x-stat icon="trend" label="Receita estimada" :value="'R$ '.number_format($totalRev, 0, ',', '.')" :hint="'custos R$ '.number_format($totalCost, 0, ',', '.')" tint="tint-emerald" />
  </div>

  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $s)
      <div class="panel card-hover relative overflow-hidden group">
        <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none group-hover:scale-110 transition"></div>
        <!-- mini progress ring -->
        @php $ring = 2 * pi() * 18; @endphp
        <div class="absolute top-4 right-4 w-12 h-12 pointer-events-none">
          <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90">
            <circle cx="20" cy="20" r="18" fill="none" stroke="rgba(120,113,108,0.15)" stroke-width="4"/>
            <circle cx="20" cy="20" r="18" fill="none" stroke="#336e3c" stroke-width="4" stroke-linecap="round" stroke-dasharray="{{ $ring }}" stroke-dashoffset="{{ $ring * (1 - $s->progress / 100) }}"/>
          </svg>
          <span class="absolute inset-0 flex items-center justify-center text-[10px] font-black tabular-nums">{{ $s->progress }}%</span>
        </div>
        <div class="relative flex justify-between items-start gap-2 pr-12">
          <div class="min-w-0">
            <a href="{{ route('seasons.show', $s) }}" class="font-black tracking-tight text-stone-900 hover:text-agro-700 transition dark:text-white text-lg">{{ $s->name }}</a>
            <p class="text-xs text-stone-400 mt-0.5 flex items-center gap-1.5"><x-icon name="sun" class="w-3.5 h-3.5" />{{ $s->crop?->name }} · {{ $s->property?->name }}</p>
          </div>
          <x-badge :color="$s->status === 'em_andamento' ? 'green' : ($s->status === 'finalizada' ? 'sky' : ($s->status === 'cancelada' ? 'stone' : 'amber'))">{{ $s->status }}</x-badge>
        </div>
        <div class="relative mt-3 grid grid-cols-3 gap-2 text-center">
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Área</p>
            <p class="text-sm font-black tabular-nums text-agro-700">{{ $s->total_area }}<span class="text-[10px] font-bold">ha</span></p>
          </div>
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Prod</p>
            <p class="text-sm font-black tabular-nums text-agro-700">{{ $s->realized_production }}/<span class="text-stone-400">{{ $s->estimated_production }}</span></p>
          </div>
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Margem</p>
            <p class="text-sm font-black tabular-nums text-agro-700">R$ {{ number_format(($s->estimated_revenue ?? 0) - ($s->estimated_cost ?? 0), 0, ',', '.') }}</p>
          </div>
        </div>
        <div class="relative mt-3 h-2 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10">
          <div class="h-full bg-gradient-to-r from-agro-700 via-agro-500 to-agro-400 progress-stripe anim-gradient rounded-full transition-all" style="width: {{ $s->progress }}%"></div>
        </div>
        <p class="text-[11px] text-stone-400 mt-1.5 relative">{{ $s->progress }}% do período · Prod {{ $s->realized_production }}/{{ $s->estimated_production }}</p>
        <div class="relative flex items-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10">
          <a href="{{ route('seasons.show', $s) }}" class="btn-secondary !py-1.5 !px-3 !text-xs !border-0 !shadow-none !bg-agro-50 !text-agro-700 hover:!bg-agro-100">Dashboard →</a>
          <button wire:click="edit({{ $s->id }})" class="btn-ghost">Editar</button>
          <button wire:click="confirmDelete({{ $s->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button>
        </div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="calendar" title="Nenhuma safra" hint="Planeje a primeira safra para começar a acompanhar produção, custos e margem."><button wire:click="create" class="btn-primary">Planejar safra</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-agro"><x-icon name="calendar" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} safra</h3><p class="text-xs text-stone-400">Período, área e metas da safra</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Safra 2026/2027 — Soja"></div>
        <div><label class="label">Ano</label><input wire:model="form.year" class="input" placeholder="2026/27"></div>
        <div><label class="label">Cultura</label><select wire:model="form.crop_id" class="input"><option value="">—</option>@foreach($crops as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Propriedade</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Início</label><input type="date" wire:model="form.starts_at" class="input"></div>
        <div><label class="label">Fim</label><input type="date" wire:model="form.ends_at" class="input"></div>
        <div><label class="label">Área total (ha)</label><input type="number" step="0.01" wire:model="form.total_area" class="input"></div>
        <div><label class="label">Produção estimada</label><input type="number" step="0.01" wire:model="form.estimated_production" class="input"></div>
        <div><label class="label">Custo estimado (R$)</label><input type="number" step="0.01" wire:model="form.estimated_cost" class="input"></div>
        <div><label class="label">Receita estimada (R$)</label><input type="number" step="0.01" wire:model="form.estimated_revenue" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Status</label><select wire:model="form.status" class="input"><option value="planejada">Planejada</option><option value="em_andamento">Em andamento</option><option value="colheita">Colheita</option><option value="finalizada">Finalizada</option><option value="cancelada">Cancelada</option></select></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar safra</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir safra?</h3><p class="text-sm text-stone-500 mt-1">Plantações e produções vinculadas perderão a referência.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>