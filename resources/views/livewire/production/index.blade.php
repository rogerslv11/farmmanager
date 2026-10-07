<div class="anim-fade-up">
  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar por lote…" class="search-input"></div>
    <span class="text-xs text-stone-400 font-medium ml-1">{{ $items->total() }} registros</span>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Registrar produção</button>
  </div>

  @php $cgM = app(\App\Services\ChartService::class)->productionMonthly(); $cgC = app(\App\Services\ChartService::class)->productionByCrop(); $cgQ = app(\App\Services\ChartService::class)->productionByQuality(); @endphp

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-sky-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Produção por mês</h3><p class="text-xs text-stone-400">Últimos 12 meses · volume registrado</p></div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="prod-month"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="package" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Produção por cultura</h3>
        </div>
        <div class="relative h-56 mt-3"><canvas id="prod-crop"></canvas></div>
      </div>
    </div>
  </div>

  @if(!empty($cgQ['values']) && array_sum($cgQ['values']) > 0)
  <div class="card !p-4 mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-violet-100/30 pointer-events-none"></div>
    <div class="relative flex flex-wrap items-center gap-4">
      <span class="kpi-tint tint-violet !w-9 !h-9 !rounded-lg"><x-icon name="star" class="w-5 h-5" /></span>
      <div>
        <h3 class="font-extrabold tracking-tight">Qualidade da produção</h3>
        <p class="text-xs text-stone-400">Distribuição por classificação · {{ collect($cgQ['labels'])->count() }} categorias</p>
      </div>
      <div class="ml-auto flex flex-wrap gap-3">
        @foreach($cgQ['labels'] as $i => $label)
          @php $color = ['Premium' => 'tint-amber', 'Padrao' => 'tint-agro', 'Inferior' => 'tint-red', 'Não classificado' => 'tint-stone'][$label] ?? 'tint-stone'; @endphp
          <div class="flex items-center gap-2"><span class="kpi-tint {{ $color }} !w-7 !h-7 !rounded-md"><b class="text-[11px] tabular-nums">{{ $cgQ['values'][$i] ?? 0 }}</b></span><span class="text-xs font-semibold">{{ $label }}</span></div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  @script
  <script>
    fm.make('prod-month', { type: 'line', data: { labels: @js($cgM['labels']), datasets: [{ data: @js($cgM['values']), borderColor: '#0ea5e9', backgroundColor: (ctx) => { const c = ctx.chart.ctx.createLinearGradient(0,0,0,250); c.addColorStop(0,'rgba(14,165,233,0.3)'); c.addColorStop(1,'rgba(14,165,233,0)'); return c; }, fill: true, tension: 0.42, pointRadius: 0, pointHoverRadius: 5, borderWidth: 2.5 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.countTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true } } } });
    fm.make('prod-crop', { type: 'bar', data: { labels: @js($cgC['labels']), datasets: [{ data: @js($cgC['values']), backgroundColor: fm.palette, borderRadius: 8 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.countTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true } } } });
  </script>
  @endscript

  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Data</th><th class="table-th">Cultura</th><th class="table-th">Safra/Talhão</th><th class="table-th text-right">Qtd</th><th class="table-th">Qualidade</th><th class="table-th">Lote</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $p)<tr>
      <td class="table-td tabular-nums">{{ $p->produced_at?->format('d/m/Y') }}</td>
      <td class="table-td"><div class="flex items-center gap-2"><span class="kpi-tint tint-agro !w-7 !h-7 !rounded-md"><x-icon name="sprout" class="w-3.5 h-3.5" /></span><b>{{ $p->crop?->name }}</b></div></td>
      <td class="table-td text-xs text-stone-400">{{ $p->season?->name }} · {{ $p->field?->name }}</td>
      <td class="table-td text-right font-extrabold tabular-nums">{{ $p->quantity }} {{ $p->unit }}</td>
      <td class="table-td"><x-badge :color="$p->quality === 'premium' ? 'orange' : ($p->quality === 'padrao' ? 'green' : ($p->quality === 'inferior' ? 'red' : 'stone'))">{{ $p->quality ?? '—' }}</x-badge></td>
      <td class="table-td text-xs font-mono">{{ $p->batch }}</td>
      <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $p->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $p->id }})" class="link-danger ml-3">Excluir</button></td></tr>
    @empty <tr><td colspan="7" class="!border-0"><x-empty icon="package" title="Nenhum registro" hint="Comece registrando a produção das suas plantações."><button wire:click="create" class="btn-primary">Registrar produção</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-agro"><x-icon name="package" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Registrar produção</h3><p class="text-xs text-stone-400">Volume, qualidade e lote da produção</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Cultura *</label><select wire:model="form.crop_id" class="input"><option value="">—</option>@foreach($crops as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Data *</label><input type="date" wire:model="form.produced_at" class="input"></div>
        <div><label class="label">Quantidade *</label><input type="number" step="0.001" wire:model="form.quantity" class="input"></div>
        <div><label class="label">Unidade</label><select wire:model="form.unit" class="input"><option value="sc">Sacas</option><option value="kg">Kg</option><option value="ton">Ton</option><option value="L">Litros</option><option value="un">Un</option></select></div>
        <div><label class="label">Safra</label><select wire:model="form.season_id" class="input"><option value="">—</option>@foreach($seasons as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        <div><label class="label">Talhão</label><select wire:model="form.field_id" class="input"><option value="">—</option>@foreach($fields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>
        <div><label class="label">Plantação</label><select wire:model="form.planting_id" class="input"><option value="">—</option>@foreach($plantings as $p)<option value="{{ $p->id }}">#{{ $p->id }} {{ $p->crop?->name }}</option>@endforeach</select></div>
        <div><label class="label">Lote</label><input wire:model="form.batch" class="input"></div>
        <div><label class="label">Qualidade</label><select wire:model="form.quality" class="input"><option value="">—</option><option value="premium">Premium</option><option value="padrao">Padrão</option><option value="inferior">Inferior</option></select></div>
        <div><label class="label">Operador</label><input wire:model="form.operator" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir registro?</h3><p class="text-sm text-stone-500 mt-1">A produção deixará de contar nos relatórios.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>