<div class="anim-fade-up">
  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar produto ou SKU…" class="search-input"></div>
    <select wire:model.live="statusFilter" class="input !w-48"><option value="">Todos os produtos</option><option value="baixo">Abaixo do mínimo</option></select>
    <span class="text-xs text-stone-400 font-medium ml-1">{{ $items->total() }} itens</span>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo produto</button>
  </div>

  @php $cgCat = app(\App\Services\ChartService::class)->stockValueByCategory(); $cgIO = app(\App\Services\ChartService::class)->movementsInOut30d(); $cgFlow = app(\App\Services\ChartService::class)->movementsDaily30d(); @endphp
  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-violet-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Valor em estoque por categoria</h3><p class="text-xs text-stone-400">Onde o capital está imobilizado</p></div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="inv-cat"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-sky-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="refresh" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Entradas × Saídas</h3>
        </div>
        <p class="text-xs text-stone-400 mb-1">Últimos 30 dias · por tipo</p>
        <div class="relative h-52 mt-3"><canvas id="inv-io"></canvas></div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('inv-cat', { type: 'bar', data: { labels: @js($cgCat['labels']), datasets: [{ data: @js($cgCat['values']), backgroundColor: fm.palette, borderRadius: 8 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.moneyTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => fm.brlK(v) } } } } });
    fm.make('inv-io', { type: 'doughnut', data: { labels: @js($cgIO['labels']), datasets: [{ data: @js($cgIO['values']), backgroundColor: ['#336e3c','#dc2626','#f59e0b','#0ea5e9'], borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
  </script>
  @endscript

  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Produto</th><th class="table-th">Categoria</th><th class="table-th">Nível</th><th class="table-th text-right">Custo médio</th><th class="table-th text-right">Valor total</th><th class="table-th"><span class="sr-only">Ações</span></th></tr></thead>
    <tbody>@forelse($items as $i)
      @php $pct = $i->minimum_stock > 0 ? min(100, max(4, $i->current_stock / $i->minimum_stock * 100)) : 100; @endphp
      <tr class="{{ $i->is_low_stock ? '!bg-amber-50/70 dark:!bg-amber-500/5' : '' }}">
      <td class="table-td">
        <div class="flex items-center gap-3">
          <span class="kpi-tint {{ $i->is_low_stock ? 'tint-amber' : 'tint-stone' }} !w-9 !h-9 !rounded-lg shrink-0"><x-icon name="package" class="w-4 h-4" /></span>
          <div class="min-w-0"><p class="font-bold text-stone-800 dark:text-stone-100 truncate">{{ $i->name }}</p><p class="text-xs text-stone-400">{{ $i->sku }} · {{ $i->location }}</p></div>
          @if($i->is_low_stock)<x-badge color="amber">estoque baixo</x-badge>@endif
        </div>
      </td>
      <td class="table-td text-xs text-stone-500">{{ $i->category?->name ?? '—' }}</td>
      <td class="table-td"><div class="flex items-center gap-2 min-w-[140px]"><div class="flex-1 h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full {{ $i->is_low_stock ? 'bg-amber-500' : 'bg-agro-500' }}" style="width: {{ $pct }}%"></div></div><div class="text-xs"><b class="font-extrabold tabular-nums {{ $i->is_low_stock ? 'text-amber-700' : '' }}">{{ $i->current_stock }}</b><span class="text-stone-400">/{{ $i->minimum_stock }}</span></div></div></td>
      <td class="table-td text-right tabular-nums">R$ {{ number_format($i->average_cost, 2, ',', '.') }}</td>
      <td class="table-td text-right font-bold tabular-nums">R$ {{ number_format($i->stock_value, 2, ',', '.') }}</td>
      <td class="table-td whitespace-nowrap text-right">
        <button wire:click="openMove({{ $i->id }})" class="btn-secondary !py-1.5 !px-3 !text-xs">Movimentar</button>
        <button wire:click="openHistory({{ $i->id }})" class="btn-ghost">Histórico</button>
        <button wire:click="edit({{ $i->id }})" class="btn-ghost">Editar</button>
      </td></tr>
    @empty <tr><td colspan="6" class="!border-0"><x-empty icon="database" title="Nenhum produto encontrado" hint="Cadastre insumos, combustíveis e materiais."><button wire:click="create" class="btn-primary">Cadastrar produto</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-5">{{ $items->links() }}</div>

  @if($historyItemId)
  <div class="card mt-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-sky-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex justify-between items-center mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-sky !w-9 !h-9 !rounded-lg"><x-icon name="refresh" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight">Histórico de movimentações</h3>
        </div>
        <button wire:click="$set('historyItemId', null)" class="btn-ghost">Fechar ✕</button>
      </div>
      <div class="tl">
        @forelse($history as $m)
          <div class="tl-item {{ $m->type === 'saida' ? 'tl-item-warn' : ($m->type === 'entrada' ? 'tl-item-done' : '') }}">
            <div class="flex items-center gap-3 flex-wrap">
              <x-badge :color="$m->type === 'saida' ? 'red' : ($m->type === 'entrada' ? 'green' : 'sky')">{{ $m->type }}</x-badge>
              <b class="text-sm">{{ $m->quantity }} {{ $m->unit }}</b>
              <span class="text-stone-400 text-xs">{{ $m->reason }}</span>
              <span class="ml-auto text-xs text-stone-400 whitespace-nowrap">{{ $m->moved_at?->format('d/m/Y') }} · {{ $m->user?->name }}</span>
            </div>
          </div>
        @empty<p class="text-stone-400 text-sm">Sem movimentações.</p>@endforelse
      </div>
    </div>
  </div>
  @endif

  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-violet"><x-icon name="package" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Novo' }} produto</h3><p class="text-xs text-stone-400">Insumo ou material de estoque</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2"><label class="label">Nome *</label><input wire:model="form.name" class="input" placeholder="Fertilizante NPK 04-14-08"></div>
        <div><label class="label">SKU</label><input wire:model="form.sku" class="input"></div>
        <div><label class="label">Unidade</label><select wire:model="form.unit" class="input"><option value="un">un</option><option value="kg">kg</option><option value="L">L</option><option value="sc">sc</option><option value="ton">ton</option><option value="cx">cx</option></select></div>
        <div><label class="label">Categoria</label><select wire:model="form.inventory_category_id" class="input"><option value="">—</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Fornecedor</label><select wire:model="form.supplier_id" class="input"><option value="">—</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
        <div><label class="label">Estoque atual</label><input type="number" step="0.001" wire:model="form.current_stock" class="input"></div>
        <div><label class="label">Estoque mínimo</label><input type="number" step="0.001" wire:model="form.minimum_stock" class="input"></div>
        <div><label class="label">Custo médio (R$)</label><input type="number" step="0.01" wire:model="form.average_cost" class="input"></div>
        <div><label class="label">Localização</label><input wire:model="form.location" class="input" placeholder="Galpão A · Prateleira 3"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar produto</button></div>
    </div>
  </div>
  @endif

  @if($showMove)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showMove', false)"></div>
    <div class="modal-panel max-w-md">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-sky"><x-icon name="database" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Movimentar estoque</h3><p class="text-xs text-stone-400">Entrada, saída, ajuste ou transferência</p></div></div>
      <div class="space-y-3.5">
        <div><label class="label">Tipo de movimentação</label><select wire:model="move.type" class="input"><option value="entrada">Entrada</option><option value="saida">Saída</option><option value="ajuste">Ajuste</option><option value="transferencia">Transferência</option></select></div>
        <div class="grid grid-cols-2 gap-3"><div><label class="label">Quantidade *</label><input type="number" step="0.001" wire:model="move.quantity" class="input">@error('move.quantity')<p class="field-error">{{ $message }}</p>@enderror</div><div><label class="label">Custo unit. (R$)</label><input type="number" step="0.01" wire:model="move.unit_cost" class="input"></div></div>
        <div><label class="label">Data</label><input type="date" wire:model="move.moved_at" class="input"></div>
        <div><label class="label">Propriedade</label><select wire:model="move.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Motivo</label><input wire:model="move.reason" class="input" placeholder="Uso na plantação #12"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showMove', false)" class="btn-secondary">Cancelar</button><button wire:click="saveMove" class="btn-primary">Confirmar movimentação</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir produto?</h3><p class="text-sm text-stone-500 mt-1">O histórico de movimentações será mantido.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>