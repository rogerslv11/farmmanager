<div class="anim-fade-up">
  <div class="card mb-4 relative overflow-hidden">
    <div class="absolute -right-12 -top-12 w-72 h-72 rounded-full bg-agro-100/20 pointer-events-none"></div>
    <div class="relative">
      <div class="flex flex-wrap gap-2 items-end">
        <div class="flex gap-1 bg-stone-100 rounded-xl p-1 dark:bg-white/5">@foreach(['producao' => 'Produção', 'estoque' => 'Estoque', 'financeiro' => 'Financeiro'] as $k => $l)<button wire:click="$set('tab','{{ $k }}')" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition {{ $tab === $k ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400 hover:text-stone-600' }} flex items-center gap-1.5"><x-icon :name="$k === 'producao' ? 'package' : ($k === 'estoque' ? 'database' : 'wallet')" class="w-3.5 h-3.5" />{{ $l }}</button>@endforeach</div>
        <div class="flex gap-2 ml-auto items-end">
          <div><label class="label">De</label><input type="date" wire:model.live="from" class="input !w-40"></div>
          <div><label class="label">Até</label><input type="date" wire:model.live="to" class="input !w-40"></div>
          <div><label class="label">Safra</label><select wire:model.live="seasonId" class="input !w-48"><option value="">Todas</option>@foreach($seasons as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        </div>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4">
        <div class="rounded-2xl bg-gradient-to-br from-agro-50 to-white border border-agro-100 p-3.5 dark:from-agro-500/10 dark:to-stone-900 dark:border-agro-500/30 relative overflow-hidden">
          <svg class="absolute -right-2 -bottom-2 w-12 h-12 opacity-10" viewBox="0 0 40 40" fill="none"><path d="M20 35V18" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="text-agro-700"/><path d="M20 22c0-5 4-9 10-9 0 5-4 9-10 9z" fill="currentColor" class="text-agro-500"/></svg>
          <p class="text-[10px] font-bold uppercase tracking-wider text-agro-700">Produção</p>
          <p class="mt-1.5 text-xl font-black tabular-nums">{{ number_format($totals['production'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-white border border-sky-100 p-3.5 dark:from-sky-500/10 dark:to-stone-900 dark:border-sky-500/30 relative overflow-hidden">
          <svg class="absolute -right-2 -bottom-2 w-12 h-12 opacity-10" viewBox="0 0 40 40" fill="none"><ellipse cx="20" cy="12" rx="14" ry="4" stroke="currentColor" stroke-width="2" class="text-sky-700"/><path d="M6 12v18c0 2.21 6.27 4 14 4s14-1.79 14-4V12" stroke="currentColor" stroke-width="2" class="text-sky-700"/></svg>
          <p class="text-[10px] font-bold uppercase tracking-wider text-sky-700">Estoque</p>
          <p class="mt-1.5 text-xl font-black tabular-nums">R$ {{ number_format($totals['stock_value'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-red-50 to-white border border-red-100 p-3.5 dark:from-red-500/10 dark:to-stone-900 dark:border-red-500/30 relative overflow-hidden">
          <p class="text-[10px] font-bold uppercase tracking-wider text-red-700">Custos</p>
          <p class="mt-1.5 text-xl font-black tabular-nums">R$ {{ number_format($totals['expenses'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-3.5 dark:from-emerald-500/10 dark:to-stone-900 dark:border-emerald-500/30 relative overflow-hidden">
          <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Receita</p>
          <p class="mt-1.5 text-xl font-black tabular-nums">R$ {{ number_format($totals['revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl p-3.5 border relative overflow-hidden {{ $totals['margin'] >= 0 ? 'bg-gradient-to-br from-agro-700 to-agro-900 text-white border-agro-700' : 'bg-gradient-to-br from-red-600 to-red-800 text-white border-red-700' }}">
          <p class="text-[10px] font-bold uppercase tracking-wider opacity-80">Margem</p>
          <p class="mt-1.5 text-xl font-black tabular-nums">R$ {{ number_format($totals['margin'], 0, ',', '.') }}</p>
          @php $marginPct = ($totals['revenue'] ?? 0) > 0 ? round($totals['margin'] / $totals['revenue'] * 100) : 0; @endphp
          <p class="mt-1 text-[11px] opacity-90">{{ $marginPct }}% sobre receita</p>
        </div>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-2 gap-4 mb-4" wire:ignore.self>
    <div class="card {{ $tab === 'producao' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1"><span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="package" class="w-4 h-4" /></span><div><h3 class="font-extrabold tracking-tight">Produção por cultura</h3><p class="text-xs text-stone-400">No período e filtros selecionados</p></div></div>
        <div class="relative h-60 mt-3"><canvas id="rp-crop"></canvas></div>
      </div>
    </div>
    <div class="card {{ $tab === 'producao' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-emerald-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1"><span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span><div><h3 class="font-extrabold tracking-tight">Evolução da produção</h3><p class="text-xs text-stone-400">Volume por mês</p></div></div>
        <div class="relative h-60 mt-3"><canvas id="rp-month"></canvas></div>
      </div>
    </div>
    <div class="card {{ $tab === 'estoque' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-violet-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1"><span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="database" class="w-4 h-4" /></span><div><h3 class="font-extrabold tracking-tight">Valor do estoque por categoria</h3><p class="text-xs text-stone-400">Posição atual · R$ {{ number_format($totals['stock_value'], 0, ',', '.') }}</p></div></div>
        <div class="relative h-60 mt-3"><canvas id="rp-stock"></canvas></div>
      </div>
    </div>
    <div class="card {{ $tab === 'estoque' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-amber-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-2"><span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="check" class="w-4 h-4" /></span><h3 class="font-extrabold tracking-tight text-sm">Como ler este gráfico</h3></div>
        <ul class="mt-3 space-y-2.5 text-sm text-stone-500">
          <li class="flex gap-2.5"><span class="kpi-tint tint-agro !w-6 !h-6 !rounded-md shrink-0"><x-icon name="check" class="w-3 h-3" /></span>Categorias com maior valor imobilizado merecem giro acompanhado de perto.</li>
          <li class="flex gap-2.5"><span class="kpi-tint tint-agro !w-6 !h-6 !rounded-md shrink-0"><x-icon name="check" class="w-3 h-3" /></span>Itens abaixo do mínimo aparecem destacados na tabela abaixo.</li>
          <li class="flex gap-2.5"><span class="kpi-tint tint-agro !w-6 !h-6 !rounded-md shrink-0"><x-icon name="check" class="w-3 h-3" /></span>Use Compras para repor a partir dos fornecedores vinculados.</li>
        </ul>
        <a href="{{ route('inventory.index') }}" class="btn-secondary mt-4 !py-2 !text-xs w-full justify-center">Ir para o estoque →</a>
      </div>
    </div>
    <div class="card {{ $tab === 'financeiro' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1"><span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span><div><h3 class="font-extrabold tracking-tight">Receitas × Custos</h3><p class="text-xs text-stone-400">Evolução mensal no período</p></div></div>
        <div class="relative h-60 mt-3"><canvas id="rp-cash"></canvas></div>
      </div>
    </div>
    <div class="card {{ $tab === 'financeiro' ? '' : 'hidden' }} relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-earth-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1"><span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="wallet" class="w-4 h-4" /></span><div><h3 class="font-extrabold tracking-tight">Custos por categoria</h3><p class="text-xs text-stone-400">Onde o dinheiro está indo</p></div></div>
        <div class="relative h-60 mt-3"><canvas id="rp-expcat"></canvas></div>
      </div>
    </div>
  </div>

  @if($tab === 'producao')
  <div class="card !p-0 overflow-hidden">
    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-stone-100 dark:border-white/10">
      <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="package" class="w-4 h-4" /></span>
      <h3 class="font-extrabold tracking-tight">Registros de produção</h3>
    </div>
    <div class="overflow-x-auto"><table class="w-full"><thead><tr><th class="table-th">Data</th><th class="table-th">Cultura</th><th class="table-th">Safra</th><th class="table-th">Qtd</th><th class="table-th">Lote</th></tr></thead>
    <tbody>@forelse($production as $p)<tr><td class="table-td tabular-nums">{{ $p->produced_at?->format('d/m/Y') }}</td><td class="table-td">{{ $p->crop?->name }}</td><td class="table-td">{{ $p->season?->name }}</td><td class="table-td tabular-nums font-bold">{{ $p->quantity }} {{ $p->unit }}</td><td class="table-td">{{ $p->batch }}</td></tr>@empty<tr><td colspan="5" class="table-td text-center text-stone-400 py-8">Sem dados no período.</td></tr>@endforelse</tbody></table></div>
  </div>
  @elseif($tab === 'estoque')
  <div class="card !p-0 overflow-hidden">
    <div class="flex items-center gap-2.5 px-5 py-4 border-b border-stone-100 dark:border-white/10">
      <span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="database" class="w-4 h-4" /></span>
      <h3 class="font-extrabold tracking-tight">Posição de estoque</h3>
    </div>
    <div class="overflow-x-auto"><table class="w-full"><thead><tr><th class="table-th">Produto</th><th class="table-th">Categoria</th><th class="table-th">Estoque</th><th class="table-th">Mínimo</th><th class="table-th">Valor</th></tr></thead>
    <tbody>@foreach($stock as $i)<tr class="{{ $i->is_low_stock ? 'bg-amber-50/60 dark:bg-amber-500/5' : '' }}"><td class="table-td"><b>{{ $i->name }}</b>@if($i->is_low_stock)<x-badge color="amber" class="ml-2">abaixo do mínimo</x-badge>@endif</td><td class="table-td">{{ $i->category?->name }}</td><td class="table-td"><b class="tabular-nums">{{ $i->current_stock }} {{ $i->unit }}</b></td><td class="table-td tabular-nums">{{ $i->minimum_stock }}</td><td class="table-td tabular-nums font-bold">R$ {{ number_format($i->stock_value, 2, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
  </div>
  @else
  <div class="grid lg:grid-cols-2 gap-4">
    <div class="card !p-0 overflow-hidden">
      <div class="flex items-center gap-2.5 px-5 py-4 border-b border-stone-100 dark:border-white/10"><span class="kpi-tint tint-red !w-8 !h-8 !rounded-lg"><x-icon name="wallet" class="w-4 h-4" /></span><h3 class="font-extrabold tracking-tight">Custos</h3><span class="ml-auto text-sm font-extrabold tabular-nums text-red-600">R$ {{ number_format($expenses->sum('amount'), 2, ',', '.') }}</span></div>
      <div class="overflow-x-auto max-h-96 overflow-y-auto"><table class="w-full"><tbody>@forelse($expenses as $e)<tr><td class="table-td">{{ $e->incurred_at?->format('d/m') }} · {{ $e->description }} <x-badge color="stone" class="ml-1">{{ $e->category }}</x-badge></td><td class="table-td text-right tabular-nums font-bold">R$ {{ number_format($e->amount, 2, ',', '.') }}</td></tr>@empty<tr><td class="table-td text-center text-stone-400 py-6">Sem custos.</td></tr>@endforelse</tbody></table></div>
    </div>
    <div class="card !p-0 overflow-hidden">
      <div class="flex items-center gap-2.5 px-5 py-4 border-b border-stone-100 dark:border-white/10"><span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span><h3 class="font-extrabold tracking-tight">Receitas</h3><span class="ml-auto text-sm font-extrabold tabular-nums text-emerald-700">R$ {{ number_format($sales->sum('total'), 2, ',', '.') }}</span></div>
      <div class="overflow-x-auto max-h-96 overflow-y-auto"><table class="w-full"><tbody>@forelse($sales as $s)<tr><td class="table-td">{{ $s->sold_at?->format('d/m') }} · {{ $s->customer?->name }}</td><td class="table-td text-right tabular-nums font-bold text-emerald-700">R$ {{ number_format($s->total, 2, ',', '.') }}</td></tr>@empty<tr><td class="table-td text-center text-stone-400 py-6">Sem vendas.</td></tr>@endforelse</tbody></table></div>
    </div>
  </div>
  @endif
</div>

@script
<script>
(function () {
  const P = @js($charts);

  const init = () => {
    if (typeof window.fm === 'undefined') return;
    fm.bars('rp-crop', P.prodCrop.labels, P.prodCrop.values);
    fm.area('rp-month', P.prodMonth.labels, [{ label: 'Produção', data: P.prodMonth.values, color: '#336e3c' }]);
    fm.doughnut('rp-stock', P.stockCat.labels, P.stockCat.values, { money: true, centerSub: 'em estoque' });
    fm.area('rp-cash', P.cash.labels, [
      { label: 'Receitas', data: P.cash.revenue, color: '#336e3c' },
      { label: 'Custos', data: P.cash.expenses, color: '#dc2626' },
    ], { money: true, legend: true });
    fm.hbars('rp-expcat', P.expCat.labels, P.expCat.values, { color: '#b08954', money: true });
  };
  init();

  const refresh = (payload) => {
    if (typeof window.fm === 'undefined') return;
    fm.setData('rp-crop', payload.prodCrop.labels, payload.prodCrop.values);
    fm.setData('rp-month', payload.prodMonth.labels, payload.prodMonth.values);
    fm.setData('rp-stock', payload.stockCat.labels, payload.stockCat.values);
    const cash = fm.registry['rp-cash'];
    if (cash) { cash.data.labels = payload.cash.labels; cash.data.datasets[0].data = payload.cash.revenue; cash.data.datasets[1].data = payload.cash.expenses; cash.update(); }
    fm.setData('rp-expcat', payload.expCat.labels, payload.expCat.values);
  };

  if (window.Livewire) Livewire.on('reports-charts', ({ payload }) => refresh(payload));
})();
</script>
@endscript