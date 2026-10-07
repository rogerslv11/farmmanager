<div class="anim-fade-up">
  @php $cgEq = app(\App\Services\ChartService::class)->equipmentByStatus(); @endphp

  <div class="grid lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="tool" label="Máquinas" :value="$items->total()" hint="frota total" tint="tint-earth" />
    <x-stat icon="check" label="Disponíveis" :value="$items->getCollection()->where('status', 'disponivel')->count()" hint="prontas para uso" tint="tint-agro" />
    <x-stat icon="activity" label="Em uso" :value="$items->getCollection()->where('status', 'em_uso')->count()" hint="em operação" tint="tint-sky" />
    <x-stat icon="alert" label="Em manutenção" :value="$items->getCollection()->where('status', 'em_manutencao')->count()" hint="indisponíveis" tint="tint-orange" />
  </div>

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-earth-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Status da frota</h3>
        </div>
        <div class="relative h-44 mt-2"><canvas id="eq-status"></canvas></div>
      </div>
    </div>
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="leaf" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Disponibilidade operacional</h3><p class="text-xs text-stone-400">% da frota pronta para uso</p></div>
        </div>
        @php $total = $items->total(); $disp = $items->getCollection()->where('status', 'disponivel')->count(); $pct = $total > 0 ? round($disp / $total * 100) : 0; @endphp
        <p class="text-[42px] font-black tabular-nums mt-4 text-agro-700 leading-none">{{ $pct }}<span class="text-2xl">%</span></p>
        <div class="mt-3 h-2 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-agro-600 to-agro-400 progress-stripe anim-gradient" style="width: {{ $pct }}%"></div></div>
        <p class="text-xs text-stone-400 mt-2">{{ $disp }} máquinas disponíveis de {{ $total }} no total</p>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('eq-status', { type: 'doughnut', data: { labels: @js($cgEq['labels']), datasets: [{ data: @js($cgEq['values']), backgroundColor: ['#336e3c','#0ea5e9','#f59e0b','#78716c'], borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
  </script>
  @endscript

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar máquina…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-44"><option value="">Todos</option><option value="disponivel">Disponível</option><option value="em_uso">Em uso</option><option value="em_manutencao">Em manutenção</option><option value="inativo">Inativo</option></select>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova máquina</button>
  </div>

  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $e)
      <div class="card card-hover relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-earth-100/40 pointer-events-none"></div>
        <div class="relative flex justify-between gap-2">
          <div class="flex items-start gap-3 min-w-0">
            <span class="kpi-tint tint-earth shrink-0"><x-icon name="tool" class="w-5 h-5" /></span>
            <div class="min-w-0">
              <p class="font-black tracking-tight truncate">{{ $e->name }}</p>
              <p class="text-xs text-stone-400 mt-0.5">{{ $e->brand }} {{ $e->model }} · {{ $e->year }}</p>
            </div>
          </div>
          <x-badge :color="$e->status === 'disponivel' ? 'green' : ($e->status === 'em_manutencao' ? 'red' : ($e->status === 'em_uso' ? 'sky' : 'stone'))">{{ $e->status }}</x-badge>
        </div>
        <div class="relative mt-3 grid grid-cols-3 gap-2 text-center">
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Tipo</p>
            <p class="text-[11px] font-bold mt-0.5 capitalize">{{ $e->category }}</p>
          </div>
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Patrimônio</p>
            <p class="text-[11px] font-bold tabular-nums mt-0.5">{{ $e->asset_code ?? '—' }}</p>
          </div>
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Horímetro</p>
            <p class="text-[11px] font-bold tabular-nums mt-0.5 text-agro-700">{{ $e->hour_meter }}h</p>
          </div>
        </div>
        <p class="text-xs text-stone-500 mt-3 flex items-center gap-1.5"><x-icon name="home" class="w-3.5 h-3.5 text-stone-400" />{{ $e->property?->name ?? 'Sem propriedade' }}</p>
        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10"><button wire:click="edit({{ $e->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $e->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button></div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="tool" title="Nenhuma máquina" hint="Cadastre tratores, colheitadeiras, pulverizadores e implementos."><button wire:click="create" class="btn-primary">Nova máquina</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-earth"><x-icon name="tool" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} máquina</h3><p class="text-xs text-stone-400">Frota, horímetro e alocação</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Trator JD 5075E"></div>
        <div><label class="label">Patrimônio</label><input wire:model="form.asset_code" class="input"></div>
        <div><label class="label">Categoria</label><select wire:model="form.category" class="input"><option value="trator">Trator</option><option value="colheitadeira">Colheitadeira</option><option value="caminhao">Caminhão</option><option value="pulverizador">Pulverizador</option><option value="implemento">Implemento</option><option value="ferramenta">Ferramenta</option></select></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="disponivel">Disponível</option><option value="em_uso">Em uso</option><option value="em_manutencao">Em manutenção</option><option value="inativo">Inativo</option></select></div>
        <div><label class="label">Marca</label><input wire:model="form.brand" class="input"></div>
        <div><label class="label">Modelo</label><input wire:model="form.model" class="input"></div>
        <div><label class="label">Propriedade</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Horímetro</label><input type="number" step="0.1" wire:model="form.hour_meter" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir máquina?</h3><p class="text-sm text-stone-500 mt-1">Histórico de manutenções será mantido.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>