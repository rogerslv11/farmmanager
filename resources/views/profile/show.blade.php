@extends('layouts.app')
@section('title', 'Meu perfil')
@section('content')
<div class="anim-fade-up">
  <x-page-header title="Meu perfil" subtitle="Dados pessoais, organização e segurança da conta." icon="user" />
  @if(session('status'))<div class="flex items-center gap-2.5 rounded-2xl bg-agro-50 border border-agro-200 text-agro-800 text-sm font-medium p-3.5 mb-4 anim-pop"><x-icon name="check" class="w-4 h-4 shrink-0" />{{ session('status') }}</div>@endif
  <div class="grid lg:grid-cols-3 gap-4">
    <div class="card !p-0 overflow-hidden lg:col-span-1">
      <div class="relative px-5 pt-8 pb-5 text-center hero-agro">
        <div class="absolute inset-0 deco-stripes opacity-30 pointer-events-none"></div>
        <div class="relative">
          <div class="relative mx-auto w-24 h-24">
            <div class="absolute inset-0 rounded-full bg-white/20 blur-xl"></div>
            <span class="avatar relative w-24 h-24 text-3xl ring-4 ring-white/30 shadow-xl mx-auto">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
          </div>
          <p class="mt-3 font-black text-lg text-white">{{ auth()->user()->name }}</p>
          <p class="text-xs text-agro-200">{{ auth()->user()->email }}</p>
          <x-badge class="!bg-white/15 !text-white !border !border-white/20 mt-3">{{ ucfirst(auth()->user()->role) }}</x-badge>
        </div>
      </div>
      <div class="p-5 space-y-3">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Organização</p>
          <p class="text-sm font-bold mt-0.5">{{ auth()->user()->organization?->name }}</p>
        </div>
        <div>
          <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Membro desde</p>
          <p class="text-sm font-bold mt-0.5">{{ auth()->user()->created_at->translatedFormat('d \\d\\e F \\d\\e Y') }}</p>
        </div>
        <div>
          <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Telefone</p>
          <p class="text-sm font-bold mt-0.5">{{ auth()->user()->phone ?? 'Não cadastrado' }}</p>
        </div>
      </div>
    </div>
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-3">
          <span class="kpi-tint tint-agro !w-9 !h-9 !rounded-lg"><x-icon name="user" class="w-4 h-4" /></span>
          <h3 class="font-extrabold">Dados pessoais</h3>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">@csrf @method('PUT')
          <div><label class="label">Nome</label><input name="name" value="{{ auth()->user()->name }}" class="input"></div>
          <div><label class="label">E-mail</label><input value="{{ auth()->user()->email }}" disabled class="input opacity-60"></div>
          <div><label class="label">Telefone</label><input name="phone" value="{{ auth()->user()->phone }}" class="input"></div>
          <button class="btn-primary"><x-icon name="check" class="w-4 h-4" /> Salvar alterações</button>
        </form>
      </div>
    </div>
    <div class="card lg:col-span-3 relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-red-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-3">
          <span class="kpi-tint tint-red !w-9 !h-9 !rounded-lg"><x-icon name="shield" class="w-4 h-4" /></span>
          <h3 class="font-extrabold">Alterar senha</h3>
        </div>
        <form method="POST" action="{{ route('profile.password') }}" class="grid sm:grid-cols-3 gap-3">@csrf @method('PUT')
          <div><label class="label">Senha atual</label><input name="current_password" type="password" class="input"></div>
          <div><label class="label">Nova senha</label><input name="password" type="password" class="input"></div>
          <div><label class="label">Confirmar nova senha</label><input name="password_confirmation" type="password" class="input"></div>
          <div class="sm:col-span-3 flex items-center justify-between">
            @error('password')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            <button class="btn-secondary ml-auto"><x-icon name="check" class="w-4 h-4" /> Alterar senha</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection