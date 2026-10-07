<div class="space-y-4 anim-fade-up">
  <!-- Hero -->
  <div class="card !p-5 mb-4 relative overflow-hidden">
    <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-agro-100/40 pointer-events-none"></div>
    <svg class="absolute right-4 top-4 w-24 h-24 opacity-15 pointer-events-none" viewBox="0 0 100 100" fill="none">
      <g stroke="currentColor" stroke-width="0.6" class="text-agro-700" fill="none">
        <circle cx="50" cy="50" r="40"/><circle cx="50" cy="50" r="30"/><circle cx="50" cy="50" r="20"/>
      </g>
    </svg>
    <div class="relative flex flex-wrap items-center gap-4">
      <span class="kpi-tint tint-agro !w-14 !h-14 !rounded-2xl shadow-md"><x-icon name="calendar" class="w-7 h-7" /></span>
      <div class="min-w-0 flex-1">
        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-agro-600 flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-agro-500"></span>Safra · {{ $season->status }}</p>
        <h1 class="page-title text-balance">{{ $season->name }}</h1>
        <p class="page-subtitle">{{ $season->crop?->name }} · {{ $season->property?->name }}</p>
      </div>
      <a href="{{ route('seasons.index') }}" class="btn-secondary"><x-icon name="arrow-left" class="w-4 h-4" /> Voltar</a>
    </div>
    <div class="relative grid grid-cols-3 gap-3 mt-5">
      <div class="rounded-2xl bg-gradient-to-br from-agro-50 to-white border border-agro-100 p-3.5 dark:from-agro-500/10 dark:to-stone-900 dark:border-agro-500/30">
        <p class="text-[11px] font-bold uppercase tracking-wider text-agro-700 flex items-center gap-1.5"><x-icon name="globe" class="w-3 h-3" />Área</p>
        <p class="text-2xl font-black tabular-nums mt-0.5">{{ $season->total_area }} <span class="text-xs font-bold text-stone-500">ha</span></p>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-orange-50 to-white border border-orange-100 p-3.5 dark:from-orange-500/10 dark:to-stone-900 dark:border-orange-500/30">
        <p class="text-[11px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5"><x-icon name="package" class="w-3 h-3" />Produção</p>
        <p class="text-2xl font-black tabular-nums mt-0.5">{{ $totalProduction }}</p>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-3.5 dark:from-emerald-500/10 dark:to-stone-900 dark:border-emerald-500/30">
        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5"><x-icon name="leaf" class="w-3 h-3" />Produtividade</p>
        <p class="text-2xl font-black tabular-nums mt-0.5">{{ $productivity }}<span class="text-xs font-bold text-stone-500">/ha</span></p>
      </div>
    </div>
    <div class="relative mt-4">
      <div class="flex justify-between text-xs font-semibold text-stone-500 mb-1.5"><span class="inline-flex items-center gap-1.5"><x-icon name="activity" class="w-3.5 h-3.5" />Progresso da safra</span><span class="tabular-nums font-bold text-agro-700">{{ $season->progress }}%</span></div>
      <div class="h-2.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10">
        <div class="h-full bg-gradient-to-r from-agro-700 via-agro-500 to-agro-400 progress-stripe anim-gradient rounded-full transition-all" style="width: {{ $season->progress }}%"></div>
      </div>
    </div>
  </div>

  @php
    $actByStatus = $activities->groupBy('status')->map->count();
    $actLabels = ['concluida' => 'Concluídas', 'em_andamento' => 'Em andamento', 'agendada' => 'Agendadas', 'pendente' => 'Pendentes', 'cancelada' => 'Canceladas'];
    $statusOrder = ['concluida', 'em_andamento', 'agendada', 'pendente', 'cancelada'];
    $orderedStatuses = collect($statusOrder)->filter(fn($s) => isset($actByStatus[$s]))->values();
    $orderedCounts = $orderedStatuses->map(fn($s) => $actByStatus[$s]);
  @endphp

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-earth-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-red !w-8 !h-8 !rounded-lg"><x-icon name="wallet" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Custos por categoria</h3><p class="text-xs text-stone-400">Total R$ {{ number_format($totalCost, 2, ',', '.') }}</p></div>
        </div>
        <div class="relative h-60 mt-3"><canvas id="sea-cost"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-emerald-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="clipboard" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Andamento das atividades</h3><p class="text-xs text-stone-400">{{ $activities->where('status', 'concluida')->count() }} de {{ $activities->count() }} concluídas</p></div>
        </div>
        <div class="relative h-60 mt-3"><canvas id="sea-act"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Resultado projetado</h3>
        </div>
        <div class="space-y-3 mt-4">
          <div>
            <div class="flex justify-between text-xs mb-1"><span class="text-stone-500">Receita estimada</span><b class="tabular-nums text-emerald-700">R$ {{ number_format($season->estimated_revenue ?? 0, 0, ',', '.') }}</b></div>
            <div class="h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-emerald-500" style="width: 75%"></div></div>
          </div>
          <div>
            <div class="flex justify-between text-xs mb-1"><span class="text-stone-500">Custos previstos</span><b class="tabular-nums text-red-600">R$ {{ number_format($season->estimated_cost ?? 0, 0, ',', '.') }}</b></div>
            <div class="h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-red-500" style="width: {{ $season->estimated_revenue > 0 ? min(100, $season->estimated_cost / $season->estimated_revenue * 100) : 0 }}%"></div></div>
          </div>
          <div class="pt-3 border-t border-stone-100 dark:border-white/10">
            <div class="flex justify-between items-baseline"><span class="text-xs text-stone-500 font-semibold">Margem projetada</span><b class="text-xl font-black tabular-nums {{ ($season->estimated_revenue - $season->estimated_cost) >= 0 ? 'text-agro-700' : 'text-red-600' }}">R$ {{ number_format(($season->estimated_revenue ?? 0) - ($season->estimated_cost ?? 0), 0, ',', '.') }}</b></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.doughnut('sea-cost', @js(collect($costsByCategory)->keys()->map(fn ($c) => ucfirst(str_replace('_', ' ', $c)))), @js(collect($costsByCategory)->values()), { money: true, centerSub: 'em custos' });
    fm.doughnut('sea-act', @js($orderedStatuses->map(fn ($s) => $actLabels[$s] ?? $s)), @js($orderedCounts), { colors: ['#336e3c', '#0ea5e9', '#f59e0b', '#a8a29e', '#ef4444'], centerSub: 'atividades' });
  </script>
  @endscript

  <div class="grid lg:grid-cols-2 gap-4">
    <div class="card">
      <div class="flex items-center gap-2.5 mb-3">
        <span class="kpi-tint tint-red !w-9 !h-9 !rounded-lg"><x-icon name="wallet" class="w-4 h-4" /></span>
        <h3 class="font-extrabold tracking-tight">Custos por categoria</h3>
        <span class="ml-auto text-sm font-extrabold tabular-nums">R$ {{ number_format($totalCost, 2, ',', '.') }}</span>
      </div>
      <div class="tl">
        @forelse($costsByCategory as $cat => $val)
          @php $total = max(1, $totalCost); $pct = min(100, $val / $total * 100); @endphp
          <div class="tl-item tl-item-done">
            <div class="flex-1">
              <div class="flex justify-between text-sm mb-1"><span class="capitalize text-stone-700 font-semibold">{{ str_replace('_', ' ', $cat) }}</span><b class="tabular-nums">R$ {{ number_format($val, 2, ',', '.') }}</b></div>
              <div class="h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-red-500 to-red-400" style="width: {{ $pct }}%"></div></div>
            </div>
          </div>
        @empty <p class="text-sm text-stone-400">Nenhum custo lançado. <a href="{{ route('expenses.index') }}" class="link-action">Lançar →</a></p> @endforelse
      </div>
    </div>
    <div class="card">
      <div class="flex items-center gap-2.5 mb-3">
        <span class="kpi-tint tint-emerald !w-9 !h-9 !rounded-lg"><x-icon name="clipboard" class="w-4 h-4" /></span>
        <h3 class="font-extrabold tracking-tight">Atividades</h3>
      </div>
      <div class="space-y-0 text-sm max-h-72 overflow-y-auto">
        @forelse($activities as $a)
          <div class="flex items-center gap-2.5 border-b border-stone-100 last:border-0 py-2.5 dark:border-white/5"><span class="timeline-dot {{ $a->status === 'concluida' ? 'bg-agro-500 ring-agro-100' : 'bg-amber-400 ring-amber-100' }}"></span><span class="flex-1">{{ $a->title }}</span><x-badge :color="$a->status === 'concluida' ? 'green' : 'stone'">{{ $a->status }}</x-badge></div>
        @empty <p class="text-sm text-stone-400">Nenhuma atividade.</p> @endforelse
      </div>
    </div>
    <div class="card lg:col-span-2 !p-0 overflow-hidden">
      <div class="flex items-center gap-2.5 px-5 py-4"><span class="kpi-tint tint-emerald !w-9 !h-9 !rounded-lg"><x-icon name="sprout" class="w-4 h-4" /></span><h3 class="font-extrabold tracking-tight">Plantações da safra</h3></div>
      <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr><th class="table-th">Talhão</th><th class="table-th">Cultura</th><th class="table-th">Área</th><th class="table-th">Status</th><th class="table-th text-right">Produtividade</th></tr></thead>
      <tbody>@foreach($plantings as $p)<tr><td class="table-td font-semibold">{{ $p->field?->name }}</td><td class="table-td">{{ $p->crop?->name }}</td><td class="table-td tabular-nums">{{ $p->planted_area }} ha</td><td class="table-td"><x-badge color="green">{{ $p->status }}</x-badge></td><td class="table-td text-right tabular-nums">{{ $p->productivity }}/ha</td></tr>@endforeach</tbody></table></div>
    </div>
  </div>
</div>