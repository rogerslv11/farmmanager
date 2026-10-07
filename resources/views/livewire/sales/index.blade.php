<div class="anim-fade-up">
  @php $cgM = app(\App\Services\ChartService::class)->salesByMonth(); $cgC = app(\App\Services\ChartService::class)->salesByCrop(); $cgTop = app(\App\Services\ChartService::class)->topCustomers(); @endphp

  <!-- Summary cards -->
  @php
    $totalSales = $items->sum('total');
    $delivered = $items->where('status', 'entregue')->count();
    $totalCount = $items->count();
    $avgTicket = $totalCount > 0 ? $totalSales / $totalCount : 0;
  @endphp
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="trend" label="Receita total" :value="'R$ '.number_format($totalSales, 0, ',', '.')" hint="{{ $totalCount }} vendas" tint="tint-emerald" />
    <x-stat icon="check" label="Entregues" :value="$delivered" :hint="$totalCount > 0 ? round($delivered / $totalCount * 100).'% do total' : '—'" tint="tint-agro" />
    <x-stat icon="package" label="Volume vendido" :value="number_format($items->sum('quantity'), 0, ',', '.')" hint="unidades somadas" tint="tint-orange" />
    <x-stat icon="wallet" label="Ticket médio" :value="'R$ '.number_format($avgTicket, 0, ',', '.')" hint="por venda" tint="tint-violet" />
  </div>

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-emerald-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Receita por mês</h3><p class="text-xs text-stone-400">Últimos 12 meses · R$</p></div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="sal-month"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Receita por cultura</h3>
        </div>
        <p class="text-xs text-stone-400 mb-2">Acumulado · R$</p>
        <div class="relative h-52 mt-1"><canvas id="sal-crop"></canvas></div>
      </div>
    </div>
  </div>

  @if(!empty($cgTop['values']) && array_sum($cgTop['values']) > 0)
  <div class="card mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-violet-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center gap-2 mb-3">
        <span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="user" class="w-4 h-4" /></span>
        <div><h3 class="font-extrabold tracking-tight">Top clientes</h3><p class="text-xs text-stone-400">Receita acumulada por comprador</p></div>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($cgTop['labels'] as $i => $label)
          @php $val = $cgTop['values'][$i] ?? 0; $max = max($cgTop['values']); $pct = $max > 0 ? $val / $max * 100 : 0; @endphp
          <div class="rounded-xl border border-stone-200/70 p-3 dark:border-white/10">
            <p class="text-xs font-bold truncate text-stone-700 dark:text-stone-200">{{ $label }}</p>
            <p class="text-lg font-black tabular-nums mt-1.5 text-violet-700">R$ {{ number_format($val, 0, ',', '.') }}</p>
            <div class="h-1 rounded-full bg-stone-100 mt-2 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-violet-400" style="width: {{ $pct }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  @script
  <script>
    fm.make('sal-month', { type: 'line', data: { labels: @js($cgM['labels']), datasets: [{ data: @js($cgM['values']), borderColor: '#336e3c', backgroundColor: (ctx) => { const c = ctx.chart.ctx.createLinearGradient(0,0,0,250); c.addColorStop(0,'rgba(51,110,60,0.3)'); c.addColorStop(1,'rgba(51,110,60,0)'); return c; }, fill: true, tension: 0.42, pointRadius: 0, pointHoverRadius: 5, borderWidth: 2.5 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.moneyTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => fm.brlK(v) } } } } });
    fm.make('sal-crop', { type: 'doughnut', data: { labels: @js($cgC['labels']), datasets: [{ data: @js($cgC['values']), backgroundColor: fm.palette, borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.moneyTip } } });
  </script>
  @endscript

  <div class="filter-bar"><div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar por lote…" class="search-input !w-52"></div><select wire:model.live="statusFilter" class="input !w-44"><option value="">Todos</option><option value="pendente">Pendente</option><option value="confirmada">Confirmada</option><option value="faturada">Faturada</option><option value="entregue">Entregue</option><option value="cancelada">Cancelada</option></select><button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova venda</button></div>
  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Data</th><th class="table-th">Cliente</th><th class="table-th">Cultura/Lote</th><th class="table-th">Qtd × Preço</th><th class="table-th">Total</th><th class="table-th">Status</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $s)
      <tr>
        <td class="table-td tabular-nums">{{ $s->sold_at?->format('d/m/Y') }}</td>
        <td class="table-td"><div class="flex items-center gap-2.5"><span class="avatar w-8 h-8 text-[10px]">{{ strtoupper(substr($s->customer?->name ?? '—', 0, 1)) }}</span><b class="text-stone-800 dark:text-stone-100">{{ $s->customer?->name }}</b></div></td>
        <td class="table-td text-xs text-stone-400">{{ $s->crop?->name }} · {{ $s->batch }}</td>
        <td class="table-td tabular-nums">{{ $s->quantity }} {{ $s->unit }} × <span class="text-stone-400">R$ {{ number_format($s->unit_price, 2, ',', '.') }}</span></td>
        <td class="table-td font-extrabold tabular-nums text-agro-700">R$ {{ number_format($s->total, 2, ',', '.') }}</td>
        <td class="table-td"><x-badge :color="$s->status === 'entregue' ? 'green' : ($s->status === 'cancelada' ? 'stone' : 'amber')">{{ $s->status }}</x-badge></td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $s->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $s->id }})" class="link-danger ml-3">Excluir</button></td></tr>
    @empty <tr><td colspan="7" class="!border-0"><x-empty icon="trend" title="Nenhuma venda" hint="Registre sua primeira venda para acompanhar receita."><button wire:click="create" class="btn-primary">Nova venda</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-emerald"><x-icon name="trend" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Registrar venda</h3><p class="text-xs text-stone-400">Cliente, cultura e condições</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Cliente *</label><select wire:model="form.customer_id" class="input"><option value="">—</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Cultura</label><select wire:model="form.crop_id" class="input"><option value="">—</option>@foreach($crops as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Quantidade *</label><input type="number" step="0.001" wire:model="form.quantity" class="input"></div>
        <div><label class="label">Preço unit. (R$) *</label><input type="number" step="0.01" wire:model="form.unit_price" class="input"></div>
        <div><label class="label">Unidade</label><select wire:model="form.unit" class="input"><option value="sc">Sacas</option><option value="kg">Kg</option><option value="ton">Ton</option></select></div>
        <div><label class="label">Desconto (R$)</label><input type="number" step="0.01" wire:model="form.discount" class="input"></div>
        <div><label class="label">Data *</label><input type="date" wire:model="form.sold_at" class="input"></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="pendente">Pendente</option><option value="confirmada">Confirmada</option><option value="faturada">Faturada</option><option value="entregue">Entregue</option><option value="cancelada">Cancelada</option></select></div>
        <div class="sm:col-span-2"><label class="label">Lote</label><input wire:model="form.batch" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir venda?</h3><p class="text-sm text-stone-500 mt-1">Receita e estoque serão ajustados.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>