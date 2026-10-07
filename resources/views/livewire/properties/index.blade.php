<div class="anim-fade-up">
  <!-- Portfolio summary -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="home" label="Propriedades" :value="$stats['count']" hint="no portfólio" tint="tint-agro" />
    <x-stat icon="globe" label="Área total" :value="number_format($stats['area'], 0, ',', '.').' ha'" hint="somatório cadastrado" tint="tint-earth" />
    <x-stat icon="leaf" label="Área produtiva" :value="number_format($stats['productive'], 0, ',', '.').' ha'" :hint="$stats['productive_pct'].'% do total'" tint="tint-emerald" />
    <x-stat icon="layers" label="Talhões" :value="$stats['fields']" hint="demarcados" tint="tint-amber" />
  </div>

  <!-- Charts -->
  @php $cgArea = app(\App\Services\ChartService::class)->propertiesByArea(); $cgType = app(\App\Services\ChartService::class)->propertiesByType(); @endphp
  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="globe" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Área por propriedade</h3>
            <p class="text-xs text-stone-400">Top 8 em hectares cadastrados</p>
          </div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="prop-area"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-earth-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Por tipo</h3>
            <p class="text-xs text-stone-400">Distribuição do portfólio</p>
          </div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="prop-type"></canvas></div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('prop-area', { type: 'bar', data: { labels: @js($cgArea['labels']), datasets: [{ data: @js($cgArea['values']), backgroundColor: ['#336e3c','#458a4e','#67a76f','#a3d3a8','#b08954','#96744a','#0ea5e9','#f59e0b'], borderRadius: 8, barThickness: 18 }] }, options: { maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false }, tooltip: fm.unitTip('ha') }, scales: { x: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => v + ' ha' } }, y: { grid: { display: false } } } } });
    fm.make('prop-type', { type: 'doughnut', data: { labels: @js($cgType['labels']), datasets: [{ data: @js($cgType['values']), backgroundColor: fm.palette, borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
  </script>
  @endscript

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar propriedade, cidade…" class="search-input"></div>
    <select wire:model.live="statusFilter" class="input !w-40"><option value="">Todos status</option><option value="ativa">Ativas</option><option value="inativa">Inativas</option></select>
    <div class="flex gap-1 bg-stone-100 rounded-xl p-1 dark:bg-white/5">
      <button wire:click="$set('layout','cards')" title="Cartões" class="p-1.5 rounded-lg {{ $layout === 'cards' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400' }}"><x-icon name="grid" class="w-4 h-4" /></button>
      <button wire:click="$set('layout','table')" title="Tabela" class="p-1.5 rounded-lg {{ $layout === 'table' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400' }}"><x-icon name="file" class="w-4 h-4" /></button>
    </div>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova propriedade</button>
  </div>

  @if($layout === 'table')
  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Propriedade</th><th class="table-th">Tipo</th><th class="table-th text-right">Área total</th><th class="table-th">Aproveitamento</th><th class="table-th text-right">Talhões</th><th class="table-th text-right">Plantações</th><th class="table-th">Status</th><th class="table-th"><span class="sr-only">Ações</span></th></tr></thead>
    <tbody>
      @forelse($items as $p)
      @php $pct = $p->total_area > 0 ? round($p->productive_area / $p->total_area * 100, 1) : 0; @endphp
      <tr>
        <td class="table-td"><div class="flex items-center gap-3"><span class="kpi-tint tint-agro !w-9 !h-9 !rounded-lg shrink-0"><x-icon name="home" class="w-4 h-4" /></span><div><a href="{{ route('properties.show', $p) }}" class="font-bold hover:text-agro-700">{{ $p->name }}</a><p class="text-xs text-stone-400 flex items-center gap-1.5"><x-icon name="pin" class="w-3 h-3" />{{ $p->city }}/{{ $p->state }} · {{ $p->owner_name }}</p></div></div></td>
        <td class="table-td text-xs capitalize"><span class="badge badge-stone">{{ $p->property_type }}</span></td>
        <td class="table-td text-right font-extrabold tabular-nums">{{ $p->total_area }} ha</td>
        <td class="table-td"><div class="flex items-center gap-2 min-w-[130px]"><div class="flex-1 h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-agro-600 to-agro-400" style="width: {{ $pct }}%"></div></div><span class="text-xs font-bold tabular-nums">{{ $pct }}%</span></div></td>
        <td class="table-td text-right tabular-nums">{{ $p->fields_count }}</td>
        <td class="table-td text-right tabular-nums">{{ $p->plantings_count }}</td>
        <td class="table-td"><x-badge :color="$p->status === 'ativa' ? 'green' : 'stone'">{{ $p->status }}</x-badge></td>
        <td class="table-td whitespace-nowrap text-right"><a href="{{ route('properties.show', $p) }}" class="link-action">Detalhes</a><button wire:click="edit({{ $p->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $p->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50">Excluir</button></td>
      </tr>
      @empty <tr><td colspan="8" class="!border-0"><x-empty icon="home" title="Nenhuma propriedade encontrada" hint="Ajuste os filtros ou cadastre a primeira." /></td></tr> @endforelse
    </tbody>
  </table></div></div>
  @else
  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $p)
      @php $pct = $p->total_area > 0 ? round($p->productive_area / $p->total_area * 100, 1) : 0; @endphp
      <div class="panel card-hover overflow-hidden !p-0 relative">
        <div class="relative h-28 text-white px-5 pt-4 overflow-hidden hero-agro">
          <div class="absolute inset-0 deco-stripes opacity-50"></div>
          <!-- mini SVG landscape -->
          <svg class="absolute -right-4 -bottom-2 w-32 h-16 opacity-25" viewBox="0 0 100 40" fill="none">
            <path d="M0 30 Q 20 20 40 30 T 80 30 T 120 30" stroke="rgba(255,255,255,0.6)" stroke-width="0.5" fill="none"/>
            <path d="M0 35 Q 25 25 50 35 T 100 35" stroke="rgba(255,255,255,0.4)" stroke-width="0.5" fill="none"/>
          </svg>
          <div class="relative flex items-start justify-between gap-2">
            <div class="min-w-0">
              <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-agro-200 flex items-center gap-1.5">
                <span class="w-1 h-1 rounded-full bg-agro-300"></span>{{ $p->property_type }} · {{ $p->city }}/{{ $p->state }}
              </p>
              <a href="{{ route('properties.show', $p) }}" class="block font-black text-lg tracking-tight truncate hover:underline underline-offset-4">{{ $p->name }}</a>
            </div>
            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $p->status === 'ativa' ? 'bg-white/20 text-white border border-white/20' : 'bg-black/30 text-white/80 border border-white/10' }}">{{ $p->status }}</span>
          </div>
          <p class="relative mt-1.5 text-3xl font-black tabular-nums tracking-tight">{{ $p->total_area }} <span class="text-sm font-semibold text-agro-200/80">ha total</span></p>
        </div>
        <div class="p-5">
          <div class="flex justify-between text-xs font-semibold text-stone-500 mb-1.5"><span>{{ $p->productive_area }} ha produtivos</span><span class="tabular-nums font-bold text-agro-700">{{ $pct }}%</span></div>
          <div class="h-2 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10 relative">
            <div class="h-full rounded-full bg-gradient-to-r from-agro-700 via-agro-500 to-agro-400 progress-stripe anim-gradient" style="width: {{ $pct }}%"></div>
          </div>
          <p class="text-[13px] text-stone-500 mt-3 line-clamp-2 leading-relaxed">{{ $p->description ?? 'Sem descrição cadastrada — adicione contexto sobre solo, clima e operação.' }}</p>
          <div class="flex flex-wrap gap-1.5 mt-3">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300"><x-icon name="layers" class="w-3.5 h-3.5" />{{ $p->fields_count }} talhões</span>
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-agro-50 px-2.5 py-1 text-xs font-bold text-agro-700 dark:bg-agro-500/15 dark:text-agro-300"><x-icon name="sprout" class="w-3.5 h-3.5" />{{ $p->plantings_count }} plantações</span>
            @if($p->latitude)
              <span class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300"><x-icon name="pin" class="w-3.5 h-3.5" />GPS</span>
            @endif
          </div>
          <div class="flex items-center gap-1 mt-4 pt-3.5 border-t border-stone-100 dark:border-white/10">
            <a href="{{ route('properties.show', $p) }}" class="btn-secondary !py-1.5 !px-3 !text-xs !border-0 !shadow-none !bg-agro-50 !text-agro-700 hover:!bg-agro-100">Detalhes →</a>
            <button wire:click="edit({{ $p->id }})" class="btn-ghost">Editar</button>
            <button wire:click="confirmDelete({{ $p->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button>
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full"><x-empty icon="home" title="Nenhuma propriedade encontrada" hint="Cadastre a primeira para começar a gerenciar talhões e safras."><button wire:click="create" class="btn-primary">Cadastrar propriedade</button></x-empty></div>
    @endforelse
  </div>
  @endif
  <div class="mt-5">{{ $items->links() }}</div>

  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5">
        <span class="kpi-tint tint-agro"><x-icon name="home" class="w-5 h-5" /></span>
        <div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} propriedade</h3><p class="text-xs text-stone-400">Dados gerais, localização e área</p></div>
      </div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2"><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Fazenda Santa Esperança">@error('form.name')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div class="sm:col-span-2"><label class="label">Descrição</label><textarea wire:model="form.description" class="input" rows="2"></textarea></div>
        <div><label class="label">Proprietário</label><input wire:model="form.owner_name" class="input"></div>
        <div><label class="label">Documento</label><input wire:model="form.document" class="input"></div>
        <div><label class="label">Endereço</label><input wire:model="form.address" class="input"></div>
        <div class="grid grid-cols-2 gap-2"><div><label class="label">Município</label><input wire:model="form.city" class="input"></div><div><label class="label">UF</label><input wire:model="form.state" maxlength="2" class="input"></div></div>
        <div><label class="label">Área total (ha)</label><input type="number" step="0.01" wire:model="form.total_area" class="input"></div>
        <div><label class="label">Área produtiva (ha)</label><input type="number" step="0.01" wire:model="form.productive_area" class="input"></div>
        <div><label class="label">Latitude</label><input type="number" step="any" wire:model="form.latitude" class="input"></div>
        <div><label class="label">Longitude</label><input type="number" step="any" wire:model="form.longitude" class="input"></div>
        <div><label class="label">Tipo</label><select wire:model="form.property_type" class="input"><option value="fazenda">Fazenda</option><option value="sitio">Sítio</option><option value="chacara">Chácara</option><option value="empresa">Empresa agrícola</option></select></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="ativa">Ativa</option><option value="inativa">Inativa</option></select></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary" wire:loading.attr="disabled"><span wire:loading.remove>Salvar propriedade</span><span wire:loading>Salvando…</span></button></div>
    </div>
  </div>
  @endif

  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center">
      <span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span>
      <h3 class="font-extrabold text-lg mt-3">Excluir propriedade?</h3>
      <p class="text-sm text-stone-500 mt-1">Talhões e plantações vinculados também serão removidos. Essa ação não pode ser desfeita.</p>
      <div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div>
    </div>
  </div>
  @endif
</div>