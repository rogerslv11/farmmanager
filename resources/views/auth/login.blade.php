@extends('layouts.guest')
@section('title', 'Entrar')
@section('content')
<div class="anim-fade-up">
<h2 class="text-[26px] font-black tracking-tight text-stone-900 text-balance">Bem-vindo de volta</h2>
<p class="text-sm text-stone-500 mt-1 mb-5">Acesse sua organização para continuar gerenciando o campo.</p>

@if($errors->any())
<div class="flex items-start gap-2.5 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm p-3 mb-4 anim-pop">
  <span class="kpi-tint tint-red !w-7 !h-7 !rounded-md shrink-0"><x-icon name="alert" class="w-3.5 h-3.5" /></span>{{ $errors->first() }}
</div>
@endif

@if(config('farmmanager.demo_login', true))
<form method="POST" action="{{ route('login.demo') }}">
  @csrf
  <button class="btn-primary w-full !py-3 !text-base shadow-lg"><x-icon name="zap" class="w-4 h-4" /> Entrar com conta demo</button>
</form>
<p class="text-[11px] text-stone-400 text-center mt-2">Acesso instantâneo como administrador · sem senha</p>
<div class="divider my-5">ou continue com e-mail</div>
@endif

<form method="POST" action="{{ route('login.attempt') }}" class="space-y-3.5">
  @csrf
  <div><label class="label" for="email">E-mail corporativo</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="voce@fazenda.com" class="input"></div>
  <div><label class="label" for="password">Senha</label><input id="password" type="password" name="password" required placeholder="••••••••" class="input"></div>
  <label class="flex items-center gap-2 text-sm text-stone-600 cursor-pointer"><input type="checkbox" name="remember" class="check"> Manter conectado</label>
  <button class="btn-secondary w-full !py-3 !font-bold"><x-icon name="arrow-right" class="w-4 h-4" /> Entrar na plataforma</button>
</form>

<div class="mt-5 rounded-2xl bg-stone-50 border border-stone-200/70 p-4 text-xs text-stone-500 relative overflow-hidden">
  <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full bg-agro-100/50 pointer-events-none"></div>
  <div class="relative">
    <p class="font-extrabold text-stone-700 mb-2 flex items-center gap-1.5"><x-icon name="zap" class="w-3.5 h-3.5 text-amber-500" /> Contas do ambiente demo</p>
    <div class="grid grid-cols-2 gap-1.5">
      <span class="inline-flex items-center gap-1.5"><span class="badge badge-stone !text-[10px]">admin</span></span>
      <span class="inline-flex items-center gap-1.5"><span class="badge badge-stone !text-[10px]">gestor</span></span>
      <span class="inline-flex items-center gap-1.5"><span class="badge badge-stone !text-[10px]">agro</span></span>
      <span class="inline-flex items-center gap-1.5"><span class="badge badge-stone !text-[10px]">estoque</span></span>
      <span class="inline-flex items-center gap-1.5 col-span-2"><span class="badge badge-stone !text-[10px]">funcionario</span></span>
    </div>
    <p class="mt-2 leading-relaxed">domínio <code class="rounded bg-white border border-stone-200 px-1.5 py-0.5 font-mono text-[10px]">@fazenda.com</code></p>
    <p class="mt-0.5">Senha: <code class="rounded bg-white border border-stone-200 px-1.5 py-0.5 font-mono text-[11px] font-bold">password</code></p>
  </div>
</div>
</div>
@endsection