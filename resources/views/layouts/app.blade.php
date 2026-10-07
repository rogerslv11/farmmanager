<!DOCTYPE html>
<html lang="pt-BR" x-data="{ sidebarOpen: false, dark: localStorage.getItem('theme') === 'dark', userMenu: false, quickOpen: false }" x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); }); if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'FarmManager') — Gestão Rural</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="bg-[#f4f6f4]">
<div class="min-h-screen lg:flex">

  <!-- Mobile overlay -->
  <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
       class="fixed inset-0 z-30 bg-stone-950/40 backdrop-blur-[2px] lg:hidden"
       x-transition.opacity></div>

  <!-- ============== SIDEBAR ============== -->
  <aside x-data="{ hoverIdx: null }"
         class="fixed inset-y-0 left-0 z-40 w-[272px] -translate-x-full lg:translate-x-0 transition-transform duration-200 bg-white border-r border-stone-200/70 flex flex-col dark:bg-stone-900 dark:border-white/10"
         :class="{ '!translate-x-0': sidebarOpen }">

    <!-- Brand -->
    <a href="{{ route('dashboard') }}" class="sidebar-brand group">
      <div class="absolute inset-0 hero-agro"></div>
      <div class="absolute inset-0 deco-stripes opacity-40 pointer-events-none"></div>
      <!-- Floating mini SVG decoration -->
      <svg class="absolute -right-4 -top-4 w-24 h-24 opacity-20 pointer-events-none" viewBox="0 0 100 100" fill="none">
        <g stroke="rgba(255,255,255,0.6)" stroke-width="0.6" fill="none">
          <path d="M 0 60 Q 25 40 60 60 T 100 60"/><path d="M 0 80 Q 25 60 60 80 T 100 80"/>
        </g>
      </svg>
      <div class="relative flex items-center gap-3 min-w-0">
        <span class="w-11 h-11 rounded-2xl bg-white/15 border border-white/30 flex items-center justify-center shadow-[0_6px_18px_-6px_rgba(0,0,0,0.5)] group-hover:scale-105 transition-transform">
          <x-icon name="sprout" class="w-6 h-6 text-white" />
        </span>
        <span class="min-w-0">
          <span class="block font-black text-[17px] tracking-tight text-white leading-none">FarmManager</span>
          <span class="block text-[11px] text-agro-200/90 truncate mt-1 max-w-[180px] font-semibold flex items-center gap-1">
            <span class="w-1 h-1 rounded-full bg-agro-300 animate-pulse"></span>
            {{ auth()->user()?->organization?->name ?? 'SaaS Rural' }}
          </span>
        </span>
      </div>
    </a>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0 scrollbar-thin" style="scrollbar-width: thin;">
      @php
        $groups = [
          'Operação' => [
            'properties' => ['properties.index','home','Propriedades'],
            'fields' => ['fields.index','layers','Talhões'],
            'crops' => ['crops.index','sun','Culturas'],
            'plantings' => ['plantings.index','sprout','Plantações'],
            'seasons' => ['seasons.index','calendar','Safras'],
            'activities' => ['activities.index','clipboard','Atividades'],
            'production' => ['production.index','package','Produção'],
            'harvests' => ['harvests.index','truck','Colheitas'],
          ],
          'Estoque' => [
            'inventory' => ['inventory.index','database','Produtos'],
            'purchases' => ['purchases.index','cart','Compras'],
            'suppliers' => ['suppliers.index','briefcase','Fornecedores'],
          ],
          'Recursos' => [
            'employees' => ['employees.index','users','Funcionários'],
            'equipment' => ['equipment.index','tool','Máquinas'],
            'maintenance' => ['maintenance.index','sliders','Manutenções'],
          ],
          'Comercial' => [
            'customers' => ['customers.index','user','Clientes'],
            'sales' => ['sales.index','trend','Vendas'],
          ],
          'Financeiro' => [
            'expenses' => ['expenses.index','wallet','Custos'],
          ],
          'Sistema' => [
            'reports' => ['reports.index','chart','Relatórios'],
            'audit' => ['audit.index','shield','Auditoria'],
          ],
        ];
        $moduleOf = fn ($route) => explode('.', $route)[0];
        $sidebarCount = function ($module) {
            $orgId = auth()->user()->organization_id;
            return match($module) {
              'properties' => \App\Models\Property::where('organization_id', $orgId)->count(),
              'fields' => \App\Models\Field::where('organization_id', $orgId)->count(),
              'crops' => \App\Models\Crop::where('organization_id', $orgId)->count(),
              'plantings' => \App\Models\Planting::where('organization_id', $orgId)->count(),
              'seasons' => \App\Models\Season::where('organization_id', $orgId)->count(),
              'activities' => \App\Models\AgriculturalActivity::where('organization_id', $orgId)->whereIn('status', ['pendente','agendada','em_andamento'])->count(),
              'production' => \App\Models\Production::where('organization_id', $orgId)->count(),
              'harvests' => \App\Models\Harvest::where('organization_id', $orgId)->count(),
              'inventory' => \App\Models\InventoryItem::where('organization_id', $orgId)->count(),
              'purchases' => \App\Models\Purchase::where('organization_id', $orgId)->count(),
              'suppliers' => \App\Models\Supplier::where('organization_id', $orgId)->count(),
              'employees' => \App\Models\Employee::where('organization_id', $orgId)->count(),
              'equipment' => \App\Models\Equipment::where('organization_id', $orgId)->count(),
              'maintenance' => \App\Models\MaintenanceRecord::where('organization_id', $orgId)->count(),
              'customers' => \App\Models\Customer::where('organization_id', $orgId)->count(),
              'sales' => \App\Models\Sale::where('organization_id', $orgId)->count(),
              'expenses' => \App\Models\Expense::where('organization_id', $orgId)->count(),
              default => 0,
            };
        };
      @endphp

      <!-- Dashboard (always visible) -->
      <a href="{{ route('dashboard') }}" class="nav-link group {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
        <span class="kpi-tint {{ request()->routeIs('dashboard') ? 'tint-white/20' : 'tint-stone' }} !w-7 !h-7 !rounded-lg group-hover:tint-agro"><x-icon name="grid" class="w-4 h-4" /></span>
        <span class="font-semibold">Dashboard</span>
      </a>

      @foreach($groups as $label => $links)
        @php
          $visible = collect($links)->filter(fn ($l) => auth()->user()->canAccess($moduleOf($l[0])));
          $groupCount = $visible->reduce(fn ($c, $l) => $c + $sidebarCount($moduleOf($l[0])), 0);
        @endphp
        @if($visible->isNotEmpty())
        <div>
          <p class="nav-section">
            <span>{{ $label }}</span>
            <span class="nav-section-count">{{ $groupCount }}</span>
          </p>
          <div class="space-y-0.5">
          @foreach($visible as [$route, $icon, $name])
            @php $cnt = $sidebarCount($moduleOf($route)); @endphp
            <a href="{{ route($route) }}" class="nav-link group {{ request()->routeIs($route.'*') || request()->routeIs(str_replace('.index', '.show', $route)) ? 'nav-active' : '' }}">
              <span class="kpi-tint {{ request()->routeIs($route.'*') || request()->routeIs(str_replace('.index', '.show', $route)) ? 'tint-white/20' : 'tint-stone' }} !w-7 !h-7 !rounded-lg group-hover:tint-agro"><x-icon :name="$icon" class="w-4 h-4" /></span>
              <span class="font-semibold flex-1">{{ $name }}</span>
              @if($cnt > 0)<span class="nav-link-count">{{ $cnt }}</span>@endif
            </a>
          @endforeach
          </div>
        </div>
        @endif
      @endforeach
    </nav>

    <!-- Footer: alerts + user -->
    <div class="p-3.5 space-y-3 shrink-0 border-t border-stone-100 dark:border-white/10 bg-gradient-to-b from-transparent to-stone-50/40 dark:to-stone-900">

      @php
        $alerts = [
          'low_stock' => \App\Models\InventoryItem::where('organization_id', auth()->user()->organization_id)->lowStock()->count(),
          'overdue_act' => \App\Models\AgriculturalActivity::where('organization_id', auth()->user()->organization_id)->overdue()->count(),
          'maint_due' => \App\Models\MaintenanceRecord::where('organization_id', auth()->user()->organization_id)->where('next_at', '<', now())->count(),
        ];
        $totalAlerts = array_sum($alerts);
      @endphp

      @if($totalAlerts > 0)
      <a href="{{ route('dashboard') }}" class="sidebar-status group block">
        <div class="relative flex items-center gap-3">
          <div class="relative shrink-0">
            <div class="absolute inset-0 rounded-full bg-amber-400/40 animate-ping"></div>
            <span class="kpi-tint tint-amber !w-10 !h-10 !rounded-full relative">
              <x-icon name="alert" class="w-5 h-5" />
            </span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300">{{ $totalAlerts }} {{ $totalAlerts === 1 ? 'alerta ativo' : 'alertas ativos' }}</p>
            <p class="text-xs text-stone-600 dark:text-stone-300 mt-0.5 truncate">
              @if($alerts['low_stock'] > 0){{ $alerts['low_stock'] }} estoque @endif
              @if($alerts['overdue_act'] > 0)· {{ $alerts['overdue_act'] }} atividade @endif
              @if($alerts['maint_due'] > 0)· {{ $alerts['maint_due'] }} manutenção @endif
            </p>
          </div>
          <x-icon name="chevron-right" class="w-4 h-4 text-amber-600 group-hover:translate-x-0.5 transition" />
        </div>
      </a>
      @endif

      <!-- User card -->
      <div class="relative">
        <button @click="userMenu = !userMenu" class="w-full flex items-center gap-3 rounded-xl px-2.5 py-2 hover:bg-stone-100 transition border border-transparent hover:border-stone-200 dark:hover:bg-white/5 dark:hover:border-white/10">
          <span class="relative shrink-0">
            <span class="avatar w-10 h-10 text-sm ring-2 ring-white shadow-md">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-stone-900"></span>
          </span>
          <span class="min-w-0 flex-1 text-left">
            <span class="block text-[13px] font-bold truncate text-stone-800 dark:text-stone-100">{{ auth()->user()->name }}</span>
            <span class="block text-[11px] text-stone-400 capitalize font-medium">{{ auth()->user()->role }}</span>
          </span>
          <svg :class="userMenu ? 'rotate-180 transition' : 'transition'" class="w-4 h-4 text-stone-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="userMenu" @click.outside="userMenu = false" x-cloak class="profile-pop" style="display:none" x-transition>
          <div class="profile-pop-head">
            <div class="flex items-center gap-3">
              <span class="avatar w-11 h-11 text-sm shadow-md">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
              <div class="min-w-0">
                <p class="text-sm font-bold text-stone-800 dark:text-stone-100 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-stone-500 truncate">{{ auth()->user()->email }}</p>
              </div>
            </div>
            <span class="badge badge-green mt-2.5">{{ ucfirst(auth()->user()->role) }}</span>
          </div>
          <div class="py-1.5">
            <a href="{{ route('profile') }}" class="profile-pop-item"><span class="kpi-tint tint-agro !w-7 !h-7 !rounded-lg"><x-icon name="user" class="w-3.5 h-3.5" /></span>Meu perfil</a>
            <a href="{{ route('notifications.index') }}" class="profile-pop-item"><span class="kpi-tint tint-sky !w-7 !h-7 !rounded-lg"><x-icon name="bell" class="w-3.5 h-3.5" /></span>Notificações</a>
          </div>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="profile-pop-danger"><span class="kpi-tint tint-red !w-7 !h-7 !rounded-lg"><x-icon name="logout" class="w-3.5 h-3.5" /></span>Sair da conta</button>
          </form>
        </div>
      </div>
    </div>
  </aside>

  <!-- ============== MAIN ============== -->
  <div class="flex-1 lg:pl-[272px] min-w-0 flex flex-col min-h-screen">

    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-inner">

        <!-- Mobile menu button -->
        <button class="lg:hidden navbar-icon" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
          <x-icon name="menu" class="w-5 h-5" />
        </button>

        <!-- Breadcrumb -->
        @hasSection('breadcrumb')
          @yield('breadcrumb')
        @else
        <nav class="hidden sm:flex items-center gap-2 min-w-0 flex-1" aria-label="breadcrumb">
          <a href="{{ route('dashboard') }}" class="crumb-link">
            <x-icon name="home" class="w-3.5 h-3.5" />Início
          </a>
          @if(!request()->routeIs('dashboard'))
            <x-icon name="chevron-right" class="crumb-sep" />
            <span class="crumb-current">@yield('title', 'Página')</span>
          @endif
        </nav>
        @endif
        <div class="sm:hidden flex-1"></div>

        <!-- Search trigger -->
        <button @click="$dispatch('open-global-search')" title="Busca global (Ctrl+K)" class="cmd-trigger hidden md:inline-flex">
          <span class="cmd-trigger-icon"><x-icon name="search" class="w-3.5 h-3.5" /></span>
          <span class="cmd-trigger-text">Buscar propriedades, talhões, produtos…</span>
          <span class="cmd-trigger-kbd">⌘ K</span>
        </button>
        <button @click="$dispatch('open-global-search')" class="navbar-icon md:hidden" aria-label="Buscar">
          <x-icon name="search" class="w-5 h-5" />
        </button>

        <!-- Right cluster -->
        <div class="flex items-center gap-1.5 ml-auto">

          <!-- Quick add (FAB-style on mobile, dropdown on desktop) -->
          <div class="relative hidden sm:block" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="navbar-pill" title="Ações rápidas">
              <span class="kpi-tint tint-agro !w-5 !h-5 !rounded-md"><x-icon name="plus" class="w-3 h-3" /></span>
              <span class="hidden lg:inline">Novo</span>
              <x-icon name="chevron-down" class="w-3 h-3 opacity-70" />
            </button>
            <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-2 w-[260px] panel !p-1.5 shadow-xl z-50" style="display:none">
              <p class="px-3 pt-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-stone-400">Ações rápidas</p>
              <a href="{{ route('sales.index') }}" class="nav-link"><span class="kpi-tint tint-emerald !w-7 !h-7 !rounded-md"><x-icon name="trend" class="w-3.5 h-3.5" /></span>Nova venda</a>
              <a href="{{ route('activities.index') }}" class="nav-link"><span class="kpi-tint tint-amber !w-7 !h-7 !rounded-md"><x-icon name="clipboard" class="w-3.5 h-3.5" /></span>Nova atividade</a>
              <a href="{{ route('harvests.index') }}" class="nav-link"><span class="kpi-tint tint-orange !w-7 !h-7 !rounded-md"><x-icon name="truck" class="w-3.5 h-3.5" /></span>Registrar colheita</a>
              <a href="{{ route('purchases.index') }}" class="nav-link"><span class="kpi-tint tint-amber !w-7 !h-7 !rounded-md"><x-icon name="cart" class="w-3.5 h-3.5" /></span>Novo pedido</a>
              <a href="{{ route('expenses.index') }}" class="nav-link"><span class="kpi-tint tint-red !w-7 !h-7 !rounded-md"><x-icon name="wallet" class="w-3.5 h-3.5" /></span>Lançar custo</a>
            </div>
          </div>

          <!-- Theme -->
          <button class="navbar-icon" @click="dark = !dark" title="Alternar tema" aria-label="Tema">
            <span x-show="!dark" x-transition.opacity><x-icon name="moon" class="w-5 h-5" /></span>
            <span x-show="dark" x-cloak x-transition.opacity><x-icon name="sun" class="w-5 h-5 text-amber-400" /></span>
          </button>

          <!-- Notifications -->
          @livewire('notifications.bell')

          <!-- Quick profile (mobile only) -->
          <a href="{{ route('profile') }}" class="navbar-icon sm:hidden" aria-label="Perfil">
            <x-icon name="user" class="w-5 h-5" />
          </a>
        </div>
      </div>

      <!-- Mobile search row -->
      <div class="md:hidden px-4 pb-3">
        <button @click="$dispatch('open-global-search')" class="w-full flex items-center gap-2.5 rounded-xl border border-stone-200 bg-stone-50 pl-3.5 pr-3 py-2.5 text-left hover:border-stone-300 hover:bg-white transition dark:bg-stone-800 dark:border-white/10">
          <x-icon name="search" class="w-4 h-4 text-stone-400 shrink-0" />
          <span class="text-sm text-stone-400 flex-1 truncate">Buscar propriedades, talhões, produtos…</span>
        </button>
      </div>
    </header>

    <!-- Mobile bottom-nav for quick reach -->
    <nav class="lg:hidden sticky top-[68px] z-10 bg-white/85 backdrop-blur border-b border-stone-200/60 dark:bg-stone-900/85 dark:border-white/10">
      <div class="flex items-center gap-1 overflow-x-auto px-2 py-2 scrollbar-thin">
        @php
          $quick = [
            ['dashboard', '/', 'grid', 'Início'],
            ['sales', '/sales', 'trend', 'Vendas'],
            ['inventory', '/inventory', 'database', 'Estoque'],
            ['activities', '/activities', 'clipboard', 'Tarefas'],
            ['reports', '/reports', 'chart', 'Relatórios'],
          ];
        @endphp
        @foreach($quick as [$key, $url, $icon, $label])
          <a href="{{ $url }}" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold whitespace-nowrap transition {{ $url === '/' ? (request()->routeIs('dashboard') ? 'bg-agro-600 text-white' : 'text-stone-600 hover:bg-stone-100') : (request()->routeIs($key.'*') ? 'bg-agro-600 text-white' : 'text-stone-600 hover:bg-stone-100') }}">
            <x-icon :name="$icon" class="w-3.5 h-3.5" /> {{ $label }}
          </a>
        @endforeach
      </div>
    </nav>

    <main class="p-4 sm:p-6 lg:p-8 max-w-[1440px] mx-auto w-full flex-1">
      @yield('content')
    </main>

    <footer class="px-6 pb-6 text-center text-xs text-stone-400 flex items-center justify-center gap-2">
      <span class="w-1 h-1 rounded-full bg-stone-300"></span>
      <span>FarmManager · Gestão rural profissional · {{ auth()->user()?->organization?->name }}</span>
      <span class="w-1 h-1 rounded-full bg-stone-300"></span>
    </footer>
  </div>
</div>

<!-- Busca global (instância única, fora de containers responsivos) -->
@livewire('search.global-search')

<!-- Toasts -->
<div class="fixed bottom-5 right-5 z-[100] space-y-2.5 w-[min(360px,calc(100vw-40px))]" x-data>
  <template x-for="t in $store.toast.items" :key="t.id">
    <div class="toast-card">
      <span class="kpi-tint !w-8 !h-8 !rounded-lg shrink-0" :class="t.type === 'success' ? 'tint-agro' : t.type === 'info' ? 'tint-sky' : 'tint-red'">
        <svg x-show="t.type === 'success'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <svg x-show="t.type !== 'success'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </span>
      <p class="flex-1 font-medium text-stone-700 dark:text-stone-200" x-text="t.message"></p>
      <button @click="$store.toast.remove(t.id)" class="text-stone-300 hover:text-stone-500 transition">✕</button>
    </div>
  </template>
</div>

@livewireScripts
<script>
  document.addEventListener('livewire:init', () => {
    Livewire.on('notify', ({ message, type }) => window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } })));
  });
</script>
@stack('scripts')
</body>
</html>