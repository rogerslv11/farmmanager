<div class="anim-fade-up">
  <div class="card mb-4 !p-4 flex flex-wrap items-center gap-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-red-100/30 pointer-events-none"></div>
    <span class="kpi-tint tint-red"><x-icon name="wallet" class="w-5 h-5" /></span>
    <div><p class="text-xs font-semibold text-stone-400">Total filtrado</p><p class="text-[28px] leading-none font-black tracking-tight text-stone-900 tabular-nums dark:text-white">R$ {{ number_format($total, 2, ',', '.') }}</p></div>
    <div class="flex flex-wrap gap-2 ml-auto items-center relative z-[1]">
      <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar custo…" class="search-input !w-48"></div>
      <select wire:model.live="statusFilter" class="input !w-44"><option value="">Todas categorias</option><option value="sementes">Sementes</option><option value="fertilizantes">Fertilizantes</option><option value="defensivos">Defensivos</option><option value="combustivel">Combustível</option><option value="manutencao">Manutenção</option><option value="mao_de_obra">Mão de obra</option><option value="maquinas">Máquinas</option><option value="transporte">Transporte</option><option value="servicos">Serviços</option><option value="outros">Outros</option></select>
      <button wire:click="create" class="btn-primary"><x-icon name="plus" class="w-4 h-4" /> Novo custo</button>
    </div>
  </div>
  @php $cgM = app(\App\Services\ChartService::class)->expensesMonthly(); $cgC = app(\App\Services\ChartService::class)->expensesByCategory(); @endphp
  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-earth-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Despesas por mês</h3><p class="text-xs text-stone-400">Últimos 12 meses · R$</p></div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="exp-month"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-red-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-red !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Despesas por categoria</h3>
        </div>
        <div class="relative h-56 mt-3"><canvas id="exp-cat"></canvas></div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('exp-month', { type: 'bar', data: { labels: @js($cgM['labels']), datasets: [{ data: @js($cgM['values']), backgroundColor: (ctx) => { const c = ctx.chart.ctx.createLinearGradient(0,0,0,250); c.addColorStop(0,'#b08954'); c.addColorStop(1,'rgba(176,137,84,0.4)'); return c; }, borderRadius: 8, barThickness: 18 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.moneyTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => fm.brlK(v) } } } } });
    fm.make('exp-cat', { type: 'doughnut', data: { labels: @js($cgC['labels']), datasets: [{ data: @js($cgC['values']), backgroundColor: fm.palette, borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.moneyTip } } });
  </script>
  @endscript

  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Data</th><th class="table-th">Descrição</th><th class="table-th">Categoria</th><th class="table-th">Propriedade/Safra</th><th class="table-th">Valor</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $e)
      <tr>
        <td class="table-td tabular-nums whitespace-nowrap">{{ $e->incurred_at?->format('d/m/Y') }}</td>
        <td class="table-td font-bold text-stone-800 dark:text-stone-100">{{ $e->description }}</td>
        <td class="table-td"><x-badge color="stone">{{ $e->category }}</x-badge></td>
        <td class="table-td text-xs text-stone-400">{{ $e->property?->name }} · {{ $e->season?->name }}</td>
        <td class="table-td text-right font-extrabold tabular-nums text-red-600">R$ {{ number_format($e->amount, 2, ',', '.') }}</td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $e->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $e->id }})" class="link-danger ml-3">Excluir</button></td></tr>
    @empty <tr><td colspan="6" class="!border-0"><x-empty icon="wallet" title="Nenhum custo" hint="Lance seus primeiros custos para acompanhar a operação."><button wire:click="create" class="btn-primary">Lançar custo</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-red"><x-icon name="wallet" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Lançar custo</h3><p class="text-xs text-stone-400">Vincule à safra ou talhão para rateio</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2"><label class="label">Descrição *</label><input wire:model="form.description" class="input"></div>
        <div><label class="label">Categoria</label><select wire:model="form.category" class="input"><option value="sementes">Sementes</option><option value="fertilizantes">Fertilizantes</option><option value="defensivos">Defensivos</option><option value="combustivel">Combustível</option><option value="manutencao">Manutenção</option><option value="mao_de_obra">Mão de obra</option><option value="maquinas">Máquinas</option><option value="transporte">Transporte</option><option value="servicos">Serviços</option><option value="outros">Outros</option></select></div>
        <div><label class="label">Valor (R$) *</label><input type="number" step="0.01" wire:model="form.amount" class="input"></div>
        <div><label class="label">Data *</label><input type="date" wire:model="form.incurred_at" class="input"></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="pago">Pago</option><option value="pendente">Pendente</option></select></div>
        <div><label class="label">Propriedade</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Safra</label><select wire:model="form.season_id" class="input"><option value="">—</option>@foreach($seasons as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir custo?</h3><p class="text-sm text-stone-500 mt-1">Esse lançamento sairá dos relatórios.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>