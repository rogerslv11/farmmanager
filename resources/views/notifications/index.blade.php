@extends('layouts.app')
@section('title', 'Notificações')
@section('content')
<div class="anim-fade-up">
  <x-page-header title="Notificações" subtitle="Alertas de estoque, atividades, manutenções e operações." icon="bell" />
  <div class="card !p-0 overflow-hidden relative">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/40 pointer-events-none"></div>
    <div class="relative">
      @forelse(auth()->user()->notifications as $n)
        <div class="flex gap-3.5 px-5 py-4 border-b border-stone-100 last:border-0 dark:border-white/5 {{ is_null($n->read_at) ? 'bg-agro-50/60 dark:bg-agro-500/5' : '' }} hover:bg-stone-50/70 transition dark:hover:bg-white/5">
          <span class="timeline-dot mt-2 {{ is_null($n->read_at) ? 'bg-agro-500 ring-agro-200' : 'bg-stone-300 ring-stone-100' }}"></span>
          <div class="min-w-0 flex-1"><p class="font-bold text-sm flex items-center gap-2">{{ $n->data['title'] ?? 'Aviso' }}@if(is_null($n->read_at))<x-badge color="red">nova</x-badge>@endif</p><p class="text-xs text-stone-500 mt-0.5">{{ $n->data['message'] ?? '' }} · {{ $n->created_at->diffForHumans() }}</p></div>
          @if($n->data['url'] ?? null)<a href="{{ $n->data['url'] }}" class="btn-secondary !py-1.5 !px-3 !text-xs self-center">Ver →</a>@endif
        </div>
      @empty
        <div class="p-12 text-center">
          <div class="relative mx-auto w-20 h-20 mb-3">
            <div class="absolute inset-0 rounded-full bg-agro-100"></div>
            <span class="kpi-tint tint-agro mx-auto relative"><x-icon name="bell" class="w-7 h-7" /></span>
          </div>
          <p class="font-extrabold">Tudo em dia</p>
          <p class="text-sm text-stone-400 mt-1">Você está em dia com todas as operações.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection