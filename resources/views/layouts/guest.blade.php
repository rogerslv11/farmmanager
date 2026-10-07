<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FarmManager — @yield('title', 'Entrar')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white">
<div class="min-h-screen grid lg:grid-cols-2">
  <!-- Brand panel -->
  <div class="relative hidden lg:flex flex-col justify-between overflow-hidden text-white p-12 hero-agro">
    <div class="absolute inset-0 deco-stripes opacity-70 pointer-events-none"></div>

    <!-- Floating decorative SVG illustrations -->
    <svg class="absolute top-24 right-12 w-72 opacity-30 pointer-events-none" viewBox="0 0 200 200" fill="none">
      <g stroke="rgba(176,137,84,0.6)" stroke-width="0.6" fill="none">
        <circle cx="100" cy="100" r="90"/><circle cx="100" cy="100" r="72"/><circle cx="100" cy="100" r="54"/><circle cx="100" cy="100" r="36"/>
      </g>
      <g stroke="rgba(176,137,84,0.5)" stroke-width="0.6" fill="none">
        <path d="M 10 100 Q 55 40 100 100 T 190 100"/><path d="M 10 120 Q 55 60 100 120 T 190 120"/><path d="M 10 80 Q 55 20 100 80 T 190 80"/>
      </g>
    </svg>

    <svg class="absolute bottom-12 left-6 w-40 opacity-25 pointer-events-none" viewBox="0 0 100 100" fill="none">
      <g stroke="rgba(255,255,255,0.5)" stroke-width="0.5" fill="none">
        <path d="M0 60 Q 25 40 50 60 T 100 60"/><path d="M0 70 Q 25 50 50 70 T 100 70"/><path d="M0 80 Q 25 60 50 80 T 100 80"/><path d="M0 90 Q 25 70 50 90 T 100 90"/>
      </g>
    </svg>

    <div class="relative flex items-center gap-3">
      <span class="w-12 h-12 rounded-2xl bg-white/10 border border-white/15 backdrop-blur flex items-center justify-center shadow-[0_8px_24px_-8px_rgba(255,255,255,0.4)]">
        <x-icon name="sprout" class="w-7 h-7 text-agro-300" />
      </span>
      <div>
        <span class="block font-extrabold text-lg tracking-tight">FarmManager</span>
        <span class="block text-[11px] text-agro-300/80">Gestão rural profissional</span>
      </div>
    </div>

    <div class="relative">
      <p class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 backdrop-blur px-3.5 py-1.5 text-xs font-bold text-agro-200">
        <span class="w-1.5 h-1.5 rounded-full bg-agro-400 animate-pulse pulse-ring"></span>
        Plataforma SaaS para o agro
      </p>
      <h1 class="mt-4 text-[44px] xl:text-[52px] font-black leading-[1.05] tracking-tight text-balance">
        Toda a fazenda<br>
        <span class="bg-gradient-to-r from-agro-300 via-amber-300 to-earth-300 bg-clip-text text-transparent">em um só lugar.</span>
      </h1>
      <p class="mt-4 text-agro-100/90 max-w-md text-[15px] leading-relaxed">Propriedades, safras, estoque, máquinas, equipe e financeiro com indicadores em tempo real e gráficos interativos.</p>

      <!-- Hero stats -->
      <div class="mt-8 grid grid-cols-3 gap-3 max-w-md">
        <div class="rounded-2xl bg-white/8 border border-white/15 p-3.5 backdrop-blur relative overflow-hidden">
          <div class="absolute -right-4 -top-4 w-12 h-12 rounded-full bg-agro-400/30"></div>
          <p class="text-[26px] font-black tabular-nums relative">18</p>
          <p class="text-[11px] text-agro-200 font-semibold relative">módulos</p>
        </div>
        <div class="rounded-2xl bg-white/8 border border-white/15 p-3.5 backdrop-blur relative overflow-hidden">
          <div class="absolute -right-4 -top-4 w-12 h-12 rounded-full bg-amber-400/30"></div>
          <p class="text-[26px] font-black tabular-nums relative">100%</p>
          <p class="text-[11px] text-agro-200 font-semibold relative">multi-tenant</p>
        </div>
        <div class="rounded-2xl bg-white/8 border border-white/15 p-3.5 backdrop-blur relative overflow-hidden">
          <div class="absolute -right-4 -top-4 w-12 h-12 rounded-full bg-emerald-400/30"></div>
          <p class="text-[26px] font-black tabular-nums relative">24/7</p>
          <p class="text-[11px] text-agro-200 font-semibold relative">alertas</p>
        </div>
      </div>

      <ul class="mt-8 space-y-3 text-[14px] text-agro-50/95">
        <li class="flex items-center gap-3"><span class="kpi-tint tint-agro bg-white/15 border border-white/20"><x-icon name="check" class="w-3.5 h-3.5" /></span> Dashboard com produção, custos e margem por safra</li>
        <li class="flex items-center gap-3"><span class="kpi-tint tint-sky bg-white/15 border border-white/20"><x-icon name="chart" class="w-3.5 h-3.5" /></span> Gráficos interativos de fluxo de caixa e produtividade</li>
        <li class="flex items-center gap-3"><span class="kpi-tint tint-amber bg-white/15 border border-white/20"><x-icon name="shield" class="w-3.5 h-3.5" /></span> Controle de acesso por perfil e auditoria completa</li>
        <li class="flex items-center gap-3"><span class="kpi-tint tint-orange bg-white/15 border border-white/20"><x-icon name="bell" class="w-3.5 h-3.5" /></span> Alertas automáticos de estoque mínimo e manutenções</li>
      </ul>
    </div>

    <p class="relative text-xs text-agro-300/70 flex items-center gap-2">
      <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
      © {{ date('Y') }} FarmManager · Feito para o campo
      <span class="w-1 h-1 rounded-full bg-agro-300/50"></span>
    </p>
  </div>

  <!-- Form panel -->
  <div class="flex items-center justify-center p-6 sm:p-10 bg-[#f4f6f4] relative overflow-hidden">
    <div class="absolute inset-0 deco-dots opacity-40 pointer-events-none"></div>
    <div class="w-full max-w-[420px] relative">
      <div class="lg:hidden flex items-center gap-2.5 mb-6">
        <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-agro-500 to-agro-800 flex items-center justify-center"><x-icon name="sprout" class="w-5 h-5 text-white" /></span>
        <span class="font-extrabold text-lg tracking-tight text-stone-900">FarmManager</span>
      </div>
      <div class="panel-elevated p-7 sm:p-8">
        @yield('content')
      </div>
      <p class="text-center text-xs text-stone-400 mt-5 flex items-center justify-center gap-2">
        <x-icon name="shield" class="w-3.5 h-3.5" />
        Autenticação segura · autorização por perfil · multi-tenant
      </p>
    </div>
  </div>
</div>
</body>
</html>