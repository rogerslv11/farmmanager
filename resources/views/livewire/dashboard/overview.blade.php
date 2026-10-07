<div class="space-y-5 anim-fade-up" wire:poll.120s>
  <!-- Hero + Agenda -->
  <div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 relative overflow-hidden rounded-2xl text-white p-6 sm:p-7 shadow-[0_20px_50px_-20px_rgba(15,32,19,0.6)] hero-agro">
      <div class="absolute inset-0 pointer-events-none deco-stripes opacity-50"></div>

      <!-- Decorative SVG field pattern -->
      <svg class="absolute -right-12 -bottom-12 w-80 opacity-20 pointer-events-none" viewBox="0 0 200 200" fill="none">
        <g stroke="rgba(176,137,84,0.6)" stroke-width="0.6" fill="none">
          <path d="M 0 60 Q 50 30 100 60 T 200 60"/><path d="M 0 90 Q 50 60 100 90 T 200 90"/><path d="M 0 120 Q 50 90 100 120 T 200 120"/><path d="M 0 150 Q 50 120 100 150 T 200 150"/>
          <circle cx="100" cy="100" r="70"/><circle cx="100" cy="100" r="55"/>
        </g>
      </svg>

      <!-- Decorative rising sun -->
      <svg class="absolute top-6 right-8 w-24 h-24 opacity-40 pointer-events-none" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="18" fill="rgba(251,191,36,0.5)"/>
        <g stroke="rgba(251,191,36,0.5)" stroke-width="1.5" stroke-linecap="round">
          @for($i = 0; $i < 12; $i++)
            <line x1="50" y1="20" x2="50" y2="14" transform="rotate({{ $i * 30 }} 50 50)"/>
          @endfor
        </g>
      </svg>

      <div class="relative flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
          <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-agro-300 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-agro-400 animate-pulse"></span>
            {{ now()->translatedFormat('l, d \d\e F') }} · {{ auth()->user()->organization?->name }}
          </p>
          <h1 class="mt-2 text-2xl sm:text-[32px] font-black tracking-tight text-balance">Olá, {{ explode(' ', auth()->user()->name)[0] }}<span class="text-agro-300">.</span> <span class="block text-[15px] sm:text-base font-medium text-agro-100/90 mt-1">Bom dia de campo 🌱</span></h1>
          <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-agro-100/90">
            <span class="inline-flex items-center gap-1.5"><x-icon name="home" class="w-4 h-4 text-agro-300" />{{ $indicators['properties'] ?? 0 }} propriedades</span>
            <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="layers" class="w-4 h-4 text-agro-300" />{{ number_format($indicators['total_area'] ?? 0, 0, ',', '.') }} ha</span>
            <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="sprout" class="w-4 h-4 text-agro-300" />{{ $indicators['active_plantings'] ?? 0 }} plantações ativas</span>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('activities.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 border border-white/20 backdrop-blur px-3.5 py-2.5 text-xs font-bold hover:bg-white/20 transition">
            <x-icon name="clipboard" class="w-4 h-4" /> Atividade
          </a>
          <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white text-agro-900 px-3.5 py-2.5 text-xs font-bold hover:bg-agro-50 transition shadow-lg shadow-black/20">
            <x-icon name="trend" class="w-4 h-4" /> Nova venda
          </a>
        </div>
      </div>

      @if($featuredSeason)
      @php $ring = 2 * pi() * 30; @endphp
      <div class="relative mt-5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur p-4 sm:p-5 flex flex-wrap items-center gap-5">
        <div class="relative w-[110px] h-[110px] shrink-0">
          <svg viewBox="0 0 80 80" class="w-full h-full -rotate-90">
            <defs>
              <linearGradient id="ring-grad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#a3d3a8"/>
                <stop offset="100%" stop-color="#67a76f"/>
              </linearGradient>
            </defs>
            <circle cx="40" cy="40" r="30" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="8"/>
            <circle cx="40" cy="40" r="30" fill="none" stroke="url(#ring-grad)" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ $ring }}" stroke-dashoffset="{{ $ring * (1 - $featuredSeason['progress'] / 100) }}"/>
          </svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-xl font-black tabular-nums">{{ $featuredSeason['progress'] }}%</span>
            <span class="text-[9px] font-bold text-agro-200/70 uppercase tracking-wider">progresso</span>
          </div>
        </div>
        <div class="flex-1 min-w-[220px]">
          <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-agro-300 flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-agro-400"></span>Safra em destaque · {{ $featuredSeason['crop'] }}</p>
          <p class="font-black text-lg tracking-tight truncate mt-0.5">{{ $featuredSeason['name'] }}</p>
          <div class="mt-3 space-y-2.5 text-xs">
            <div>
              <div class="flex justify-between text-agro-100/90 mb-1">
                <span>Colheita <b class="tabular-nums text-white">{{ number_format($featuredSeason['production'], 0, ',', '.') }}</b> / {{ number_format($featuredSeason['estimated'], 0, ',', '.') }}</span>
                <span class="font-extrabold tabular-nums text-amber-300">{{ $featuredSeason['harvest_pct'] }}%</span>
              </div>
              <div class="h-1.5 rounded-full bg-white/15 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 progress-stripe anim-gradient" style="width: {{ $featuredSeason['harvest_pct'] }}%"></div>
              </div>
            </div>
            <div class="flex flex-wrap gap-x-5 gap-y-1 text-agro-100/90">
              <span class="inline-flex items-center gap-1"><b class="text-white tabular-nums">{{ $featuredSeason['area'] }} ha</b></span>
              <span class="inline-flex items-center gap-1"><b class="text-white tabular-nums">R$ {{ number_format($featuredSeason['cost'], 0, ',', '.') }}</b></span>
              <span class="inline-flex items-center gap-1"><b class="text-white tabular-nums">{{ $featuredSeason['activities_done'] }}/{{ $featuredSeason['activities_total'] }}</b> atividades</span>
            </div>
          </div>
        </div>
        <a href="{{ route('seasons.show', $featuredSeason['id']) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white text-agro-900 px-4 py-2.5 text-xs font-bold hover:bg-agro-50 transition shadow-lg shadow-black/20 shrink-0">
          Abrir safra <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
      </div>
      @endif
    </div>

    <!-- Agenda interativa -->
    <div class="card !p-0 overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100 dark:border-white/10">
        <div>
          <h3 class="font-extrabold tracking-tight flex items-center gap-2">
            <span class="kpi-tint tint-amber !w-7 !h-7 !rounded-lg"><x-icon name="calendar" class="w-4 h-4" /></span>
            Agenda
          </h3>
          <p class="text-xs text-stone-400 mt-0.5 ml-9">Atrasadas, hoje e próximos 7 dias</p>
        </div>
        <a href="{{ route('activities.index') }}" class="link-action">Todas →</a>
      </div>
      <div class="flex-1 overflow-y-auto max-h-[360px] divide-y divide-stone-100 dark:divide-white/5">
        @forelse($agenda as $a)
          <div class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50/70 transition dark:hover:bg-white/5">
            <button wire:click="completeActivity({{ $a->id }})" wire:loading.attr="disabled" title="Marcar como concluída"
              class="w-5 h-5 rounded-md border-2 {{ $a->is_overdue ? 'border-red-400 hover:bg-red-500' : 'border-stone-300 hover:border-agro-500 hover:bg-agro-500' }} flex items-center justify-center text-white transition shrink-0">
              <x-icon name="check" class="w-3 h-3" />
            </button>
            <div class="min-w-0 flex-1">
              <p class="text-[13px] font-bold text-stone-700 truncate dark:text-stone-200">{{ $a->title }}</p>
              <p class="text-[11px] text-stone-400 truncate">{{ $a->scheduled_at?->isToday() ? 'Hoje' : ($a->scheduled_at?->isTomorrow() ? 'Amanhã' : $a->scheduled_at?->format('d/m')) }} · {{ $a->field?->name ?? $a->property?->name }} · {{ $a->responsible?->name ?? 'sem responsável' }}</p>
            </div>
            @if($a->is_overdue)<x-badge color="red">atrasada</x-badge>@elseif($a->scheduled_at?->isToday())<x-badge color="amber">hoje</x-badge>@endif
          </div>
        @empty
          <div class="p-8 text-center">
            <div class="relative mx-auto w-20 h-20 mb-3">
              <div class="absolute inset-0 rounded-full bg-agro-100 animate-ping opacity-30"></div>
              <span class="kpi-tint tint-agro mx-auto relative"><x-icon name="check" class="w-5 h-5" /></span>
            </div>
            <p class="text-sm font-bold">Agenda livre</p>
            <p class="text-xs text-stone-400">Nenhuma atividade pendente no período.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Alerts -->
  @php
    $alertMeta = [
      'low_stock' => ['Estoque baixo', 'tint-amber', 'inventory.index'],
      'overdue_activities' => ['Atividades atrasadas', 'tint-red', 'activities.index'],
      'overdue_maintenance' => ['Manutenção atrasada', 'tint-red', 'maintenance.index'],
      'near_harvest' => ['Prontas p/ colheita', 'tint-orange', 'plantings.index'],
      'maintenance_soon' => ['Manutenção próxima (30d)', 'tint-sky', 'maintenance.index'],
    ];
    $activeAlerts = collect($alertMeta)->filter(fn ($m, $k) => ($alerts[$k] ?? 0) > 0);
  @endphp
  @if($activeAlerts->isNotEmpty())
  <div class="flex flex-wrap items-center gap-2.5">
    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.1em] text-stone-400"><x-icon name="alert" class="w-4 h-4 text-amber-500" /> Atenção</span>
    @foreach($activeAlerts as $k => [$label, $tint, $route])
      <a href="{{ route($route) }}" class="inline-flex items-center gap-2 rounded-full bg-white border border-stone-200/80 pl-1.5 pr-3 py-1 text-xs font-bold text-stone-600 shadow-sm hover:border-amber-300 hover:shadow transition dark:bg-stone-900 dark:border-white/10 dark:text-stone-300">
        <span class="kpi-tint {{ $tint }} !w-6 !h-6 !rounded-full !text-[11px]">{{ $alerts[$k] }}</span>
        {{ $label }} →
      </a>
    @endforeach
  </div>
  @endif

  <!-- KPIs -->
  @php
    // Sparkline data (last 6 values from the cashflow)
    $sparkRev = array_slice($cashflow['revenue'] ?? [], -6);
    $sparkExp = array_slice($cashflow['expenses'] ?? [], -6);
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3.5">
    <x-stat icon="package" label="Produção realizada" :value="number_format($indicators['realized_production'] ?? 0, 0, ',', '.')" :delta="$deltas['production']['pct'] ?? 0" :deltaGood="$deltas['production']['good'] ?? true" tint="tint-orange" :spark="$sparkExp" />
    <x-stat icon="trend" label="Receita" :value="'R$ '.number_format($indicators['estimated_revenue'] ?? 0, 0, ',', '.')" :delta="$deltas['revenue']['pct'] ?? 0" :deltaGood="$deltas['revenue']['good'] ?? true" tint="tint-emerald" :spark="$sparkRev" />
    <x-stat icon="wallet" label="Custos" :value="'R$ '.number_format($indicators['season_cost'] ?? 0, 0, ',', '.')" :delta="$deltas['expenses']['pct'] ?? 0" :deltaGood="$deltas['expenses']['good'] ?? true" tint="tint-red" :spark="$sparkExp" />
    <x-stat icon="database" label="Valor do estoque" :value="'R$ '.number_format($indicators['stock_value'] ?? 0, 0, ',', '.')" :hint="($indicators['stock_items'] ?? 0).' itens cadastrados'" tint="tint-violet" />
    <x-stat icon="sprout" label="Plantações ativas" :value="$indicators['active_plantings'] ?? 0" :hint="($indicators['active_seasons'] ?? 0).' safras em andamento'" tint="tint-agro" />
    <x-stat icon="home" label="Propriedades" :value="$indicators['properties'] ?? 0" :hint="number_format($indicators['total_area'] ?? 0, 0, ',', '.').' ha no total'" tint="tint-earth" />
    <x-stat icon="layers" label="Talhões ativos" :value="$indicators['active_fields'] ?? 0" :hint="($indicators['active_employees'] ?? 0).' funcionários ativos'" tint="tint-amber" />
    <x-stat icon="calendar" label="Prod. estimada" :value="number_format($indicators['estimated_production'] ?? 0, 0, ',', '.')" hint="safras em andamento" tint="tint-sky" />
    <a href="{{ route('reports.index') }}" class="stat !bg-gradient-to-br !from-agro-700 !to-agro-900 !border-0 group col-span-2 sm:col-span-1 relative overflow-hidden">
      <svg class="absolute -right-4 -top-4 w-32 h-32 opacity-15" viewBox="0 0 100 100" fill="none">
        <g stroke="currentColor" stroke-width="1" fill="none" class="text-white">
          <circle cx="50" cy="50" r="40"/><circle cx="50" cy="50" r="30"/><circle cx="50" cy="50" r="20"/>
        </g>
      </svg>
      <div class="flex items-center justify-between gap-3 h-full relative z-[1]">
        <div><p class="text-xs font-semibold text-agro-200">Margem operacional</p><p class="mt-1.5 text-2xl leading-none font-black text-white tabular-nums">R$ {{ number_format(($indicators['estimated_revenue'] ?? 0) - ($indicators['season_cost'] ?? 0), 0, ',', '.') }}</p><p class="mt-1.5 text-xs text-agro-300 group-hover:text-white transition flex items-center gap-1">Ver relatórios <x-icon name="arrow-right" class="w-3.5 h-3.5" /></p></div>
      </div>
    </a>
    <div class="stat !bg-stone-900 !border-0 col-span-2 sm:col-span-2 xl:col-span-1 text-white relative overflow-hidden">
      <svg class="absolute -right-6 -bottom-6 w-32 h-32 opacity-20" viewBox="0 0 100 100" fill="none">
        <g stroke="currentColor" stroke-width="1" fill="none">
          <path d="M 0 50 Q 25 30 50 50 T 100 50"/><path d="M 0 65 Q 25 45 50 65 T 100 65"/><path d="M 0 80 Q 25 60 50 80 T 100 80"/>
        </g>
      </svg>
      @php $rev = $indicators['estimated_revenue'] ?? 0; $cost = $indicators['season_cost'] ?? 0; $eff = $rev > 0 ? round((1 - $cost / $rev) * 100, 1) : 0; @endphp
      <div class="relative">
        <p class="text-xs font-semibold text-stone-400">Eficiência da operação</p>
        <p class="mt-1.5 text-[28px] leading-none font-black text-white tabular-nums">{{ $eff }}%</p>
        <div class="mt-2 h-1.5 rounded-full bg-white/10 overflow-hidden">
          <div class="h-full rounded-full bg-gradient-to-r from-agro-400 to-agro-600 progress-stripe anim-gradient" style="width: {{ max(0, min(100, $eff)) }}%"></div>
        </div>
        <p class="mt-1.5 text-xs text-stone-400">de cada R$ 1 vendido vira margem</p>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="grid lg:grid-cols-3 gap-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center justify-between mb-1">
          <div class="flex items-center gap-2">
            <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
            <div>
              <h3 class="font-extrabold tracking-tight">Fluxo de caixa</h3>
              <p class="text-xs text-stone-400">Receita vs despesas · últimos 12 meses</p>
            </div>
          </div>
          <div class="flex gap-2 text-[11px] font-bold">
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-agro-600"></span>Receita</span>
            <span class="inline-flex items-center gap-1.5 text-stone-400"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Despesas</span>
          </div>
        </div>
        <div class="relative h-64 mt-3"><canvas id="ch-cash"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-earth-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="wallet" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Custos por safra</h3>
            <p class="text-xs text-stone-400">Onde o dinheiro está indo</p>
          </div>
        </div>
        <div class="relative h-64 mt-3"><canvas id="ch-cost"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-orange-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-orange !w-8 !h-8 !rounded-lg"><x-icon name="package" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Produção por cultura</h3>
            <p class="text-xs text-stone-400">Volume total registrado</p>
          </div>
        </div>
        <div class="relative h-60 mt-3"><canvas id="ch-crop"></canvas></div>
      </div>
    </div>
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-emerald !w-8 !h-8 !rounded-lg"><x-icon name="leaf" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Produtividade por cultura</h3>
            <p class="text-xs text-stone-400">Produção por hectare plantado</p>
          </div>
        </div>
        <div class="relative h-60 mt-3"><canvas id="ch-yield"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Bottom row -->
  <div class="grid lg:grid-cols-3 gap-4">
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="database" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Estoque crítico</h3>
        </div>
        <a href="{{ route('inventory.index') }}" class="link-action">Gerenciar</a>
      </div>
      <div class="mt-2 space-y-3">
        @forelse($criticalStock as $i)
          <div>
            <div class="flex justify-between text-[13px] mb-1">
              <p class="font-bold truncate">{{ $i->name }}</p>
              <p class="tabular-nums text-xs text-stone-400 whitespace-nowrap ml-2">{{ $i->current_stock }}/{{ $i->minimum_stock }} {{ $i->unit }}</p>
            </div>
            @php $pct = $i->minimum_stock > 0 ? min(100, max(4, $i->current_stock / $i->minimum_stock * 100)) : 100; @endphp
            <div class="h-2 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10 relative">
              <div class="h-full rounded-full {{ $i->current_stock <= 0 ? 'bg-red-500 progress-stripe' : 'bg-gradient-to-r from-amber-500 to-amber-400' }}" style="width: {{ $pct }}%"></div>
              <div class="absolute inset-y-0 w-px bg-red-600" style="left: 100%"></div>
            </div>
          </div>
        @empty
          <div class="py-4 text-center">
            <div class="relative mx-auto w-16 h-16 mb-2">
              <div class="absolute inset-0 rounded-full bg-agro-100"></div>
              <span class="kpi-tint tint-agro mx-auto relative"><x-icon name="check" class="w-5 h-5" /></span>
            </div>
            <p class="text-sm font-bold">Estoque saudável</p>
            <p class="text-xs text-stone-400">Nenhum item abaixo do mínimo.</p>
          </div>
        @endforelse
      </div>
    </div>
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="refresh" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Movimentações</h3>
        </div>
        <a href="{{ route('inventory.index') }}" class="link-action">Ver tudo</a>
      </div>
      <div class="tl">
        @forelse($recent['movements'] ?? [] as $m)
          <div class="tl-item {{ $m->type === 'saida' ? 'tl-item-warn' : 'tl-item-done' }}">
            <p class="text-[13px] font-bold text-stone-700 dark:text-stone-200 capitalize">{{ $m->type }} · {{ $m->item?->name }}</p>
            <p class="text-xs text-stone-400">{{ $m->quantity }} {{ $m->unit }} · {{ $m->created_at?->format('d/m H:i') }}</p>
          </div>
        @empty <p class="text-sm text-stone-400">Sem registros.</p> @endforelse
      </div>
    </div>
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Vendas recentes</h3>
        </div>
        <a href="{{ route('sales.index') }}" class="link-action">Ver tudo</a>
      </div>
      <div class="tl">
        @forelse($recent['sales'] ?? [] as $s)
          <div class="tl-item tl-item-done">
            <div class="flex items-center gap-2">
              <div class="min-w-0 flex-1">
                <p class="text-[13px] font-bold text-stone-700 dark:text-stone-200 truncate">{{ $s->customer?->name }}</p>
                <p class="text-xs text-stone-400">{{ $s->crop?->name }} · {{ $s->quantity }} {{ $s->unit }}</p>
              </div>
              <p class="text-[13px] font-extrabold tabular-nums">R$ {{ number_format($s->total, 0, ',', '.') }}</p>
            </div>
          </div>
        @empty <p class="text-sm text-stone-400">Sem registros.</p> @endforelse
      </div>
    </div>
  </div>
</div>

@script
<script>
(function () {
  if (typeof window.fm === 'undefined') return;

  fm.area('ch-cash', @js($cashflow['labels'] ?? []), [
    { label: 'Receita', data: @js($cashflow['revenue'] ?? []), color: '#336e3c' },
    { label: 'Despesas', data: @js($cashflow['expenses'] ?? []), color: '#dc2626' },
  ], { money: true });

  fm.doughnut('ch-cost', @js(collect($costBySeason)->pluck('label')), @js(collect($costBySeason)->pluck('total')),
    { colors: ['#336e3c', '#b08954', '#0ea5e9', '#f59e0b', '#8b5cf6', '#ef4444'], money: true, centerSub: 'em custos' });

  fm.bars('ch-crop', @js(collect($productionByCrop)->pluck('label')), @js(collect($productionByCrop)->pluck('total')),
    { colors: ['#336e3c', '#458a4e', '#67a76f', '#b08954', '#96744a', '#0ea5e9', '#f59e0b'] });

  fm.hbars('ch-yield', @js(collect($productivity)->pluck('label')), @js(collect($productivity)->pluck('value')),
    { color: '#336e3c', suffix: '/ha' });
})();
</script>
@endscript