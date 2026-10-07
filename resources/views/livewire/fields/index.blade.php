<div class="anim-fade-up">
  <!-- Summary -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="layers" label="Talhões ativos" :value="$stats['active']" hint="em operação" tint="tint-amber" />
    <x-stat icon="globe" label="Área em talhões" :value="number_format($stats['area'], 0, ',', '.').' ha'" hint="somatório demarcado" tint="tint-earth" />
    <x-stat icon="droplet" label="Com irrigação" :value="$stats['irrigated']" hint="pivô, gotejo ou aspersão" tint="tint-sky" />
    <x-stat icon="sprout" label="Com cultura ativa" :value="$stats['with_crop']" hint="plantação em campo" tint="tint-emerald" />
  </div>

  <!-- Charts -->
  @php $cgStatus = app(\App\Services\ChartService::class)->fieldsByStatus(); $cgIrr = app(\App\Services\ChartService::class)->fieldsByIrrigation(); @endphp
  <div class="grid lg:grid-cols-2 gap-4 mb-4">
    <div class="card relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-amber-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="layers" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Status dos talhões</h3><p class="text-xs text-stone-400">Operação, descanso e inativos</p></div>
        </div>
        <div class="relative h-52 mt-3"><canvas id="fld-status"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-sky-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="droplet" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Tipo de irrigação</h3><p class="text-xs text-stone-400">Sequeiro, pivô, gotejo, aspersão</p></div>
        </div>
        <div class="relative h-52 mt-3"><canvas id="fld-irr"></canvas></div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('fld-status', { type: 'doughnut', data: { labels: @js($cgStatus['labels']), datasets: [{ data: @js($cgStatus['values']), backgroundColor: ['#336e3c','#f59e0b','#78716c'], borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
    fm.make('fld-irr', { type: 'doughnut', data: { labels: @js($cgIrr['labels']), datasets: [{ data: @js($cgIrr['values']), backgroundColor: fm.palette, borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
  </script>
  @endscript

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar talhão…" class="search-input !w-52"></div>
    <select wire:model.live="propertyFilter" class="input !w-56"><option value="">Todas propriedades</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
    <div class="flex gap-1 bg-stone-100 rounded-xl p-1 dark:bg-white/5">
      <button wire:click="$set('layout','cards')" title="Cartões" class="p-1.5 rounded-lg {{ $layout === 'cards' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400' }}"><x-icon name="grid" class="w-4 h-4" /></button>
      <button wire:click="$set('layout','table')" title="Tabela" class="p-1.5 rounded-lg {{ $layout === 'table' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400' }}"><x-icon name="file" class="w-4 h-4" /></button>
    </div>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo talhão</button>
  </div>

  @if($layout === 'table')
  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Talhão</th><th class="table-th">Propriedade</th><th class="table-th text-right">Área</th><th class="table-th">Solo</th><th class="table-th">Irrigação</th><th class="table-th">Cultura atual</th><th class="table-th">Status</th><th class="table-th"><span class="sr-only">Ações</span></th></tr></thead>
    <tbody>
      @forelse($items as $f)
      <tr>
        <td class="table-td"><div class="flex items-center gap-2.5"><span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="layers" class="w-4 h-4" /></span><div><p class="font-bold">{{ $f->name }}</p><p class="text-xs text-stone-400">{{ $f->code }}</p></div></div></td>
        <td class="table-td text-xs">{{ $f->property?->name }}</td>
        <td class="table-td text-right font-extrabold tabular-nums">{{ $f->area }} ha</td>
        <td class="table-td text-xs">{{ $f->soil_type ?? '—' }}</td>
        <td class="table-td">@if($f->irrigation && $f->irrigation !== 'Sequeiro')<x-badge color="sky">{{ $f->irrigation }}</x-badge>@else<span class="text-xs text-stone-400">{{ $f->irrigation ?? '—' }}</span>@endif</td>
        <td class="table-td">@if($f->currentPlanting)<span class="text-xs font-bold">{{ $f->currentPlanting->crop?->name }}</span> <span class="text-xs text-stone-400">· {{ $f->currentPlanting->status }}</span>@else<span class="text-xs text-stone-400">—</span>@endif</td>
        <td class="table-td"><x-badge :color="$f->status === 'ativo' ? 'green' : ($f->status === 'descanso' ? 'amber' : 'stone')">{{ $f->status }}</x-badge></td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $f->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $f->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50">Excluir</button></td>
      </tr>
      @empty <tr><td colspan="8" class="!border-0"><x-empty icon="layers" title="Nenhum talhão encontrado" hint="Ajuste os filtros ou demarque o primeiro talhão." /></td></tr> @endforelse
    </tbody>
  </table></div></div>
  @else
  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $f)
      <div class="panel card-hover overflow-hidden !p-0 relative">
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-100/40 pointer-events-none"></div>
        <div class="relative flex items-start justify-between gap-2 px-5 pt-4">
          <div class="flex items-center gap-1.5 text-xs font-semibold text-stone-400 min-w-0"><x-icon name="home" class="w-3.5 h-3.5 shrink-0" /><span class="truncate">{{ $f->property?->name }}</span></div>
          <x-badge :color="$f->status === 'ativo' ? 'green' : ($f->status === 'descanso' ? 'amber' : 'stone')">{{ $f->status }}</x-badge>
        </div>
        <div class="relative px-5 mt-1 flex items-end justify-between gap-3">
          <div class="min-w-0"><h3 class="font-black text-lg tracking-tight truncate">{{ $f->name }}</h3><p class="text-xs text-stone-400 font-semibold">{{ $f->code }}</p></div>
          <p class="text-right shrink-0"><span class="block text-3xl font-black tabular-nums tracking-tight">{{ $f->area }}</span><span class="block text-[10px] font-bold text-stone-400 uppercase tracking-wider">hectares</span></p>
        </div>
        <div class="px-5 mt-3 flex flex-wrap gap-1.5">
          @if($f->soil_type)<span class="inline-flex items-center gap-1 rounded-lg bg-earth-100 px-2 py-1 text-[11px] font-bold text-earth-700 dark:bg-earth-500/15 dark:text-earth-300"><x-icon name="globe" class="w-3 h-3" />{{ $f->soil_type }}</span>@endif
          @if($f->irrigation)<span class="inline-flex items-center gap-1 rounded-lg {{ $f->irrigation !== 'Sequeiro' ? 'bg-sky-100 text-sky-800 dark:bg-sky-500/15 dark:text-sky-300' : 'bg-stone-200/70 text-stone-500 dark:bg-white/10 dark:text-stone-400' }} px-2 py-1 text-[11px] font-bold"><x-icon name="droplet" class="w-3 h-3" />{{ $f->irrigation }}</span>@endif
        </div>
        <div class="px-5 mt-3">
          @if($f->currentPlanting)
            <a href="{{ route('plantings.index') }}" class="flex items-center gap-2.5 rounded-xl bg-agro-50 border border-agro-200 px-3 py-2.5 hover:border-agro-400 transition dark:bg-agro-500/10 dark:border-agro-500/30 group">
              <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg shrink-0"><x-icon name="sprout" class="w-4 h-4" /></span>
              <span class="min-w-0 flex-1"><span class="block text-[13px] font-extrabold truncate">{{ $f->currentPlanting->crop?->name }}</span><span class="block text-[11px] text-stone-400">{{ $f->currentPlanting->status }}</span></span>
              <span class="text-agro-600 text-sm font-bold group-hover:translate-x-0.5 transition">→</span>
            </a>
          @else
            <div class="rounded-xl border border-dashed border-stone-200 px-3 py-2.5 text-center text-xs text-stone-400 dark:border-white/10">Sem cultura ativa · <button wire:click="create" class="link-action">planejar plantio</button></div>
          @endif
        </div>
        <div class="flex items-center gap-1 mx-5 mt-3 mb-4 pt-3 border-t border-stone-100 dark:border-white/10"><button wire:click="edit({{ $f->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $f->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button></div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="layers" title="Nenhum talhão encontrado" hint="Ajuste os filtros ou demarque o primeiro talhão." /></div> @endforelse
  </div>
  @endif
  <div class="mt-5">{{ $items->links() }}</div>

  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-amber"><x-icon name="layers" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Novo' }} talhão</h3><p class="text-xs text-stone-400">Demarcação, solo e irrigação</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2"><label class="label">Propriedade *</label><select wire:model="form.property_id" class="input"><option value="">Selecione…</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Talhão 7"></div>
        <div><label class="label">Código</label><input wire:model="form.code" class="input" placeholder="T107"></div>
        <div><label class="label">Área (ha)</label><input type="number" step="0.01" wire:model="form.area" class="input"></div>
        <div><label class="label">Tipo de solo</label><select wire:model="form.soil_type" class="input"><option value="">—</option><option>Argiloso</option><option>Arenoso</option><option>Latossolo</option><option>Terra roxa</option><option>Misto</option></select></div>
        <div><label class="label">Irrigação</label><select wire:model="form.irrigation" class="input"><option value="">—</option><option>Sequeiro</option><option>Pivô central</option><option>Gotejamento</option><option>Aspersão</option></select></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="ativo">Ativo</option><option value="descanso">Descanso</option><option value="inativo">Inativo</option></select></div>
        <div class="sm:col-span-2"><label class="label">Observações</label><textarea wire:model="form.notes" class="input" rows="2"></textarea></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar talhão</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir talhão?</h3><p class="text-sm text-stone-500 mt-1">O histórico de plantações será mantido, mas o talhão sairá de operação.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>