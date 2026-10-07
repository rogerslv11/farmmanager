<div class="anim-fade-up">
  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar cultura…" class="search-input"></div>
    <span class="text-xs text-stone-400 font-medium ml-1">{{ $items->total() }} culturas</span>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova cultura</button>
  </div>

  <!-- Charts -->
  @php $cgC = app(\App\Services\ChartService::class)->cropsByCycle(); @endphp
  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-emerald-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="clock" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Ciclo médio por cultura</h3><p class="text-xs text-stone-400">Dias do plantio à colheita</p></div>
        </div>
        <div class="relative h-52 mt-3"><canvas id="cr-cycle"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="sprout" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Por que cultivar?</h3>
        </div>
        <p class="text-sm text-stone-500 mt-3 leading-relaxed">Culturas com <b class="text-agro-700">ciclo curto</b> permitem <b>mais safras por ano</b> e diluem risco climático. Já as de <b class="text-agro-700">ciclo longo</b> costumam ter <b>maior produtividade por hectare</b>.</p>
        <div class="mt-4 grid grid-cols-2 gap-2 text-center">
          <div class="rounded-xl bg-agro-50 p-3 dark:bg-agro-500/10">
            <p class="text-[10px] font-bold uppercase tracking-wider text-agro-700">Curto</p>
            <p class="text-lg font-black tabular-nums mt-0.5">&lt; 120d</p>
          </div>
          <div class="rounded-xl bg-amber-50 p-3 dark:bg-amber-500/10">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Longo</p>
            <p class="text-lg font-black tabular-nums mt-0.5">&gt; 180d</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('cr-cycle', { type: 'bar', data: { labels: @js($cgC['labels']), datasets: [
      { label: 'Ciclo (dias)', data: @js($cgC['cycles']), backgroundColor: '#67a76f', borderRadius: 8, barThickness: 18, yAxisID: 'y' },
      { label: 'Plantações', data: @js($cgC['plantings']), backgroundColor: '#0ea5e9', borderRadius: 8, barThickness: 18, yAxisID: 'y1' }
    ] }, options: { maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.tip }, scales: { x: { grid: { color: fm.grid }, beginAtZero: true }, y: { grid: { display: false } }, y1: { display: false, beginAtZero: true } } } });
  </script>
  @endscript

  <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @forelse($items as $c)
      <div class="card card-hover relative overflow-hidden text-center group">
        <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-emerald-100/40 pointer-events-none group-hover:scale-110 transition"></div>
        <div class="absolute -left-8 -bottom-8 w-28 h-28 rounded-full bg-agro-100/30 pointer-events-none"></div>
        <!-- mini plant SVG -->
        <svg class="absolute top-3 right-3 w-12 h-12 opacity-20" viewBox="0 0 40 40" fill="none">
          <path d="M20 35V18" stroke="currentColor" stroke-width="1.5" class="text-agro-700" stroke-linecap="round"/>
          <path d="M20 22c0-5 4-9 10-9 0 5-4 9-10 9z" fill="currentColor" class="text-emerald-400" opacity="0.5"/>
          <path d="M20 22c0-5-4-9-10-9 0 5 4 9 10 9z" fill="currentColor" class="text-emerald-600" opacity="0.5"/>
        </svg>
        <div class="relative">
          <span class="kpi-tint tint-emerald !w-14 !h-14 !rounded-2xl mx-auto shadow-md"><x-icon name="sun" class="w-7 h-7" /></span>
          <p class="font-black tracking-tight mt-3 text-lg">{{ $c->name }}</p>
          <p class="text-xs text-stone-500 mt-1 inline-flex items-center gap-1.5"><x-icon name="clock" class="w-3.5 h-3.5" />Ciclo ~{{ $c->average_cycle_days ?? '—' }} dias</p>
          <div class="mt-3 grid grid-cols-2 gap-2 text-center">
            <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
              <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Plantações</p>
              <p class="text-base font-black tabular-nums text-agro-700">{{ $c->plantings_count }}</p>
            </div>
            <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
              <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Esperado/ha</p>
              <p class="text-base font-black tabular-nums text-agro-700">{{ $c->expected_yield ?? '—' }}</p>
            </div>
          </div>
          <p class="text-[10px] text-stone-400 mt-2 uppercase tracking-wider font-semibold">{{ $c->production_unit }}</p>
          <div class="flex justify-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10">
            <button wire:click="edit({{ $c->id }})" class="btn-ghost">Editar</button>
            <button wire:click="confirmDelete({{ $c->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50">Excluir</button>
          </div>
        </div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="sun" title="Nenhuma cultura" hint="Cadastre o catálogo de culturas para começar a planejar plantações."><button wire:click="create" class="btn-primary">Cadastrar cultura</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-emerald"><x-icon name="sun" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} cultura</h3><p class="text-xs text-stone-400">Catálogo com ciclo e produtividade esperada</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Soja"></div>
        <div><label class="label">Código</label><input wire:model="form.code" class="input" placeholder="SOJ"></div>
        <div><label class="label">Ciclo médio (dias)</label><input type="number" wire:model="form.average_cycle_days" class="input"></div>
        <div><label class="label">Unidade</label><select wire:model="form.production_unit" class="input"><option value="sc">Sacas</option><option value="kg">Kg</option><option value="ton">Toneladas</option><option value="L">Litros</option><option value="un">Unidades</option></select></div>
        <div><label class="label">Produtividade esperada (/ha)</label><input type="number" step="0.01" wire:model="form.expected_yield" class="input"></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="ativa">Ativa</option><option value="inativa">Inativa</option></select></div>
        <div class="sm:col-span-2"><label class="label">Descrição</label><textarea wire:model="form.description" class="input" rows="2"></textarea></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar cultura</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir cultura?</h3><p class="text-sm text-stone-500 mt-1">Plantações que usam esta cultura perderão a referência.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>