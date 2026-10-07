@extends('layouts.app')
@section('title', $property->name)
@section('content')
@php $maxFieldArea = max(1, $property->fields->max('area') ?? 1); @endphp

<div class="space-y-5 anim-fade-up">
<!-- Cover -->
<div class="relative overflow-hidden rounded-2xl text-white p-6 sm:p-7 mb-4 hero-agro">
  <div class="absolute inset-0 deco-stripes opacity-50 pointer-events-none"></div>

  <!-- Decorative landscape -->
  <svg class="absolute -right-12 -bottom-12 w-96 opacity-20 pointer-events-none" viewBox="0 0 200 100" fill="none">
    <g stroke="rgba(176,137,84,0.5)" stroke-width="0.5" fill="none">
      <path d="M 0 50 Q 50 30 100 50 T 200 50"/><path d="M 0 70 Q 50 50 100 70 T 200 70"/><path d="M 0 90 Q 50 70 100 90 T 200 90"/>
      <circle cx="170" cy="30" r="14" stroke="rgba(251,191,36,0.5)" stroke-width="1" fill="rgba(251,191,36,0.2)"/>
    </g>
    <g fill="rgba(176,137,84,0.4)">
      <polygon points="30,80 50,40 70,80"/><polygon points="70,80 95,30 120,80"/><polygon points="120,80 145,50 170,80"/>
    </g>
  </svg>

  <div class="relative flex flex-wrap items-start gap-4">
    <span class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0 shadow-lg shadow-black/20">
      <x-icon name="home" class="w-7 h-7" />
    </span>
    <div class="min-w-0 flex-1">
      <nav class="flex items-center gap-1.5 text-xs text-agro-300 mb-1">
        <a href="{{ route('dashboard') }}" class="hover:text-white flex items-center gap-1"><x-icon name="grid" class="w-3 h-3" />Início</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <a href="{{ route('properties.index') }}" class="hover:text-white">Propriedades</a>
        <x-icon name="chevron-right" class="w-3 h-3" />
        <span class="text-white font-semibold truncate">{{ $property->name }}</span>
      </nav>
      <h1 class="text-3xl sm:text-[34px] font-black tracking-tight text-balance">{{ $property->name }}</h1>
      <p class="text-sm text-agro-100/90 mt-1.5 flex items-center gap-3 flex-wrap">
        <span class="inline-flex items-center gap-1.5"><x-icon name="pin" class="w-4 h-4 text-agro-300" />{{ $property->city }}/{{ $property->state }}</span>
        <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
        <span class="inline-flex items-center gap-1.5"><x-icon name="user" class="w-4 h-4 text-agro-300" />{{ $property->owner_name }}</span>
        <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
        <span class="inline-flex items-center gap-1.5"><x-icon name="building" class="w-4 h-4 text-agro-300" />{{ ucfirst($property->property_type) }}</span>
        <x-badge :color="$property->status === 'ativa' ? 'green' : 'stone'" class="!bg-white/15 !text-white !border !border-white/20">{{ $property->status }}</x-badge>
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      @if($property->latitude)
      <a target="_blank" href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 border border-white/20 backdrop-blur px-3.5 py-2.5 text-xs font-bold hover:bg-white/20 transition">
        <x-icon name="pin" class="w-4 h-4" /> Ver mapa
      </a>
      @endif
      <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white text-agro-900 px-3.5 py-2.5 text-xs font-bold hover:bg-agro-50 transition shadow-lg shadow-black/20">
        <x-icon name="arrow-left" class="w-4 h-4" /> Voltar
      </a>
    </div>
  </div>
  <div class="relative grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur px-4 py-3.5 hover:bg-white/15 transition">
      <p class="text-[11px] font-bold uppercase tracking-wider text-agro-200 flex items-center gap-1.5"><x-icon name="globe" class="w-3 h-3" />Área total</p>
      <p class="mt-1 text-2xl font-black tabular-nums">{{ $property->total_area }} <span class="text-xs font-semibold text-agro-200/80">ha</span></p>
    </div>
    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur px-4 py-3.5 hover:bg-white/15 transition">
      <p class="text-[11px] font-bold uppercase tracking-wider text-agro-200 flex items-center gap-1.5"><x-icon name="leaf" class="w-3 h-3" />Área produtiva</p>
      <p class="mt-1 text-2xl font-black tabular-nums">{{ $property->productive_area }} <span class="text-xs font-semibold text-agro-200/80">ha</span></p>
    </div>
    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur px-4 py-3.5 hover:bg-white/15 transition">
      <p class="text-[11px] font-bold uppercase tracking-wider text-agro-200 flex items-center gap-1.5"><x-icon name="layers" class="w-3 h-3" />Talhões</p>
      <p class="mt-1 text-2xl font-black tabular-nums">{{ $property->fields->count() }}</p>
    </div>
    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur px-4 py-3.5 hover:bg-white/15 transition">
      <p class="text-[11px] font-bold uppercase tracking-wider text-agro-200 flex items-center gap-1.5"><x-icon name="wallet" class="w-3 h-3" />Custos</p>
      <p class="mt-1 text-2xl font-black tabular-nums">R$ {{ number_format($property->expenses->sum('amount'), 0, ',', '.') }}</p>
    </div>
  </div>
</div>

<!-- Talhões -->
<div class="flex items-center justify-between mb-3">
  <h2 class="font-extrabold tracking-tight flex items-center gap-2.5">
    <span class="kpi-tint tint-amber !w-9 !h-9 !rounded-lg"><x-icon name="layers" class="w-4 h-4" /></span>
    <span>Talhões <span class="badge badge-stone ml-1">{{ $property->fields->count() }}</span></span>
  </h2>
  <a href="{{ route('fields.index') }}" class="link-action">Gerenciar talhões →</a>
</div>
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3.5 mb-6">
  @forelse($property->fields as $f)
    <div class="card card-hover !p-4 relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-100/40 pointer-events-none"></div>
      <div class="relative flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="font-extrabold tracking-tight truncate">{{ $f->name }}</p>
          <p class="text-[11px] text-stone-400 font-semibold flex items-center gap-1.5 mt-0.5">
            <x-icon name="pin" class="w-3 h-3" />{{ $f->code }} · {{ $f->soil_type ?? 'solo n/d' }}
          </p>
        </div>
        <p class="text-right shrink-0"><span class="block text-2xl font-black tabular-nums">{{ $f->area }}</span><span class="block text-[10px] font-bold text-stone-400 uppercase">ha</span></p>
      </div>
      <div class="mt-2.5 h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10">
        <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-amber-400" style="width: {{ round($f->area / $maxFieldArea * 100) }}%"></div>
      </div>
      <div class="mt-2 flex items-center justify-between text-xs">
        @if($f->irrigation)
          <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 {{ $f->irrigation !== 'Sequeiro' ? 'bg-sky-100 text-sky-700' : 'bg-stone-100 text-stone-500' }} dark:bg-white/10">
            <x-icon name="droplet" class="w-3 h-3" />{{ $f->irrigation }}
          </span>
        @endif
        @if($f->currentPlanting)
          <span class="inline-flex items-center gap-1 text-agro-700 font-semibold"><x-icon name="sprout" class="w-3 h-3" />{{ $f->currentPlanting->crop?->name }}</span>
        @endif
      </div>
    </div>
  @empty <div class="col-span-full"><x-empty icon="layers" title="Nenhum talhão demarcado" hint="Divida a propriedade em talhões para organizar plantações." /></div> @endforelse
</div>

<!-- Plantações -->
<div class="flex items-center justify-between mb-3">
  <h2 class="font-extrabold tracking-tight flex items-center gap-2.5">
    <span class="kpi-tint tint-emerald !w-9 !h-9 !rounded-lg"><x-icon name="sprout" class="w-4 h-4" /></span>
    Plantações recentes
  </h2>
  <a href="{{ route('plantings.index') }}" class="link-action">Ver todas →</a>
</div>
<div class="table-wrap mb-6"><div class="overflow-x-auto"><table class="w-full">
  <thead><tr><th class="table-th">Talhão</th><th class="table-th">Cultura</th><th class="table-th">Safra</th><th class="table-th text-right">Área</th><th class="table-th">Plantio</th><th class="table-th">Status</th><th class="table-th text-right">Produtividade</th></tr></thead>
  <tbody>
    @forelse($property->plantings()->with(['crop','field','season'])->latest()->limit(8)->get() as $p)
    <tr><td class="table-td font-semibold">{{ $p->field?->name }}</td><td class="table-td">{{ $p->crop?->name }}</td><td class="table-td text-xs text-stone-400">{{ $p->season?->name ?? '—' }}</td><td class="table-td text-right tabular-nums">{{ $p->planted_area }} ha</td><td class="table-td tabular-nums">{{ $p->planted_at?->format('d/m/Y') ?? '—' }}</td><td class="table-td"><x-badge color="green">{{ $p->status }}</x-badge></td><td class="table-td text-right tabular-nums">{{ $p->productivity }}/ha</td></tr>
    @empty <tr><td colspan="7" class="!border-0"><x-empty icon="sprout" title="Nenhuma plantação" hint="Planeje a primeira plantação nos talhões acima." /></td></tr> @endforelse
  </tbody>
</table></div></div>

<div class="grid lg:grid-cols-2 gap-4">
  <div class="card relative overflow-hidden">
    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-earth-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-extrabold tracking-tight text-sm flex items-center gap-2.5"><span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="tool" class="w-4 h-4" /></span>Máquinas na propriedade</h3>
        <a href="{{ route('equipment.index') }}" class="link-action">Ver todas</a>
      </div>
      <div class="mt-2 tl">
        @forelse($property->equipment as $e)
          <div class="tl-item tl-item-done flex items-center justify-between">
            <div class="flex-1">
              <p class="text-sm font-bold">{{ $e->name }}</p>
              <p class="text-xs text-stone-400">{{ $e->brand }} {{ $e->model }}</p>
            </div>
            <x-badge :color="$e->status === 'disponivel' ? 'green' : ($e->status === 'em_manutencao' ? 'red' : 'amber')">{{ $e->status }}</x-badge>
          </div>
        @empty<p class="text-sm text-stone-400 py-2">Nenhuma máquina alocada.</p>@endforelse
      </div>
    </div>
  </div>
  <div class="card relative overflow-hidden">
    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-sky-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-extrabold tracking-tight text-sm flex items-center gap-2.5"><span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="users" class="w-4 h-4" /></span>Equipe na propriedade</h3>
        <a href="{{ route('employees.index') }}" class="link-action">Ver todos</a>
      </div>
      <div class="mt-2 tl">
        @forelse($property->employees as $e)
          <div class="tl-item tl-item-done flex items-center justify-between">
            <span class="flex items-center gap-2.5">
              <span class="avatar w-8 h-8 text-[11px]">{{ strtoupper(substr($e->name, 0, 1)) }}</span>
              <span><span class="block text-sm font-bold leading-tight">{{ $e->name }}</span><span class="block text-[11px] text-stone-400">{{ $e->role_title }}</span></span>
            </span>
            <span class="badge badge-stone">{{ $e->status }}</span>
          </div>
        @empty<p class="text-sm text-stone-400 py-2">Nenhum funcionário alocado.</p>@endforelse
      </div>
    </div>
  </div>
</div>
</div>
@endsection