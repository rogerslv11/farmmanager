<div class="anim-fade-up">
  @php $cgR = app(\App\Services\ChartService::class)->employeesByRole(); @endphp
  <div class="grid lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="users" label="Funcionários" :value="$items->total()" hint="cadastrados" tint="tint-sky" />
    <x-stat icon="check" label="Ativos" :value="$items->getCollection()->where('status', 'ativo')->count()" hint="em operação" tint="tint-agro" />
    <x-stat icon="wallet" label="Folha mensal" :value="'R$ '.number_format($items->getCollection()->where('status', 'ativo')->sum('salary'), 0, ',', '.')" hint="salários ativos" tint="tint-emerald" />
    <x-stat icon="user" label="Ticket médio" :value="'R$ '.number_format($items->getCollection()->where('status', 'ativo')->avg('salary') ?? 0, 0, ',', '.')" hint="por funcionário ativo" tint="tint-violet" />
  </div>

  @if(!empty($cgR['values']) && array_sum($cgR['values']) > 0)
  <div class="card mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-sky-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center gap-2 mb-3">
        <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="users" class="w-4 h-4" /></span>
        <div><h3 class="font-extrabold tracking-tight">Equipe por função</h3><p class="text-xs text-stone-400">Top 6 funções mais comuns</p></div>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($cgR['labels'] as $i => $label)
          @php $val = $cgR['values'][$i] ?? 0; $max = max($cgR['values']); $pct = $max > 0 ? $val / $max * 100 : 0; @endphp
          <div class="rounded-xl border border-stone-200/70 p-3 dark:border-white/10">
            <p class="text-xs font-bold truncate text-stone-700 dark:text-stone-200">{{ $label }}</p>
            <p class="text-lg font-black tabular-nums mt-1.5 text-sky-700">{{ $val }} <span class="text-xs font-bold text-stone-400">funcionários</span></p>
            <div class="h-1 rounded-full bg-stone-100 mt-2 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-sky-400" style="width: {{ $pct }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar funcionário…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-40"><option value="">Todos</option><option value="ativo">Ativos</option><option value="inativo">Inativos</option></select>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo funcionário</button>
  </div>
  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $e)
      <div class="card card-hover relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-sky-100/40 pointer-events-none"></div>
        <div class="relative flex items-center gap-3">
          <span class="avatar w-12 h-12 ring-2 ring-white shadow-md">{{ strtoupper(substr($e->name, 0, 1)) }}</span>
          <div class="min-w-0">
            <p class="font-black tracking-tight truncate">{{ $e->name }}</p>
            <p class="text-xs text-stone-400 truncate">{{ $e->role_title ?? 'Sem cargo' }}</p>
          </div>
          <x-badge :color="$e->status === 'ativo' ? 'green' : 'stone'" class="ml-auto">{{ $e->status }}</x-badge>
        </div>
        <div class="relative mt-3 grid grid-cols-2 gap-2 text-center">
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Propriedade</p>
            <p class="text-[11px] font-bold mt-0.5 truncate">{{ $e->property?->name ?? '—' }}</p>
          </div>
          <div class="rounded-xl bg-stone-50 p-2 dark:bg-white/5">
            <p class="text-[9px] font-bold uppercase text-stone-400 tracking-wider">Salário</p>
            <p class="text-[11px] font-bold tabular-nums mt-0.5 text-agro-700">R$ {{ number_format($e->salary ?? 0, 0, ',', '.') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10"><button wire:click="edit({{ $e->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $e->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button></div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="users" title="Nenhum funcionário" hint="Cadastre a equipe, cargos e salários."><button wire:click="create" class="btn-primary">Cadastrar funcionário</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-sky"><x-icon name="users" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Novo' }} funcionário</h3><p class="text-xs text-stone-400">Cargo, alocação e salário</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input"></div>
        <div><label class="label">Cargo</label><input wire:model="form.role_title" class="input" placeholder="Operador de máquinas"></div>
        <div><label class="label">Documento</label><input wire:model="form.document" class="input"></div>
        <div><label class="label">Telefone</label><input wire:model="form.phone" class="input"></div>
        <div><label class="label">E-mail</label><input wire:model="form.email" type="email" class="input"></div>
        <div><label class="label">Propriedade</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Admissão</label><input type="date" wire:model="form.hired_at" class="input"></div>
        <div><label class="label">Salário (R$)</label><input type="number" step="0.01" wire:model="form.salary" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir funcionário?</h3><p class="text-sm text-stone-500 mt-1">Esse registro sai do quadro.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>