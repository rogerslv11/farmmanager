<div class="anim-fade-up">
  @php $cgR = app(\App\Services\ChartService::class)->topCustomers(); @endphp
  <div class="grid lg:grid-cols-3 gap-3.5 mb-4">
    <x-stat icon="user" label="Clientes" :value="$items->total()" hint="cadastrados" tint="tint-violet" />
    <x-stat icon="check" label="PJ" :value="$items->getCollection()->where('customer_type', 'pj')->count()" hint="pessoas jurídicas" tint="tint-sky" />
    <x-stat icon="trend" label="Receita acumulada" :value="'R$ '.number_format($items->getCollection()->sum('revenue'), 0, ',', '.')" hint="histórico de vendas" tint="tint-emerald" />
  </div>

  @if(!empty($cgR['values']) && array_sum($cgR['values']) > 0)
  <div class="card mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-violet-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center gap-2 mb-3">
        <span class="kpi-tint tint-violet !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
        <div><h3 class="font-extrabold tracking-tight">Top compradores</h3><p class="text-xs text-stone-400">Receita acumulada por cliente</p></div>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($cgR['labels'] as $i => $label)
          @php $val = $cgR['values'][$i] ?? 0; $max = max($cgR['values']); $pct = $max > 0 ? $val / $max * 100 : 0; @endphp
          <div class="rounded-xl border border-stone-200/70 p-3 dark:border-white/10">
            <p class="text-xs font-bold truncate text-stone-700 dark:text-stone-200">{{ $label }}</p>
            <p class="text-lg font-black tabular-nums mt-1.5 text-violet-700">R$ {{ number_format($val, 0, ',', '.') }}</p>
            <div class="h-1 rounded-full bg-stone-100 mt-2 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-violet-400" style="width: {{ $pct }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar cliente…" class="search-input"></div>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo cliente</button>
  </div>
  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $c)
      <div class="card card-hover relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-violet-100/40 pointer-events-none"></div>
        <div class="relative flex items-center gap-3">
          <span class="avatar w-12 h-12 ring-2 ring-white shadow-md bg-gradient-to-br from-violet-500 to-violet-700">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
          <div class="min-w-0">
            <p class="font-black tracking-tight truncate">{{ $c->name }}</p>
            <p class="text-xs text-stone-400 truncate">{{ $c->document ?? '—' }} · {{ $c->phone ?? '—' }}</p>
          </div>
          <x-badge :color="$c->customer_type === 'pj' ? 'sky' : ($c->customer_type === 'coop' ? 'amber' : 'stone')" class="ml-auto">{{ $c->customer_type }}</x-badge>
        </div>
        <div class="relative mt-3 p-3 rounded-xl bg-gradient-to-br from-violet-50 to-white border border-violet-100 dark:from-violet-500/10 dark:to-stone-900 dark:border-violet-500/30">
          <p class="text-[10px] font-bold uppercase tracking-wider text-violet-700">Receita acumulada</p>
          <p class="text-xl font-black tabular-nums mt-1 text-violet-700">R$ {{ number_format($c->revenue ?? 0, 2, ',', '.') }}</p>
        </div>
        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10"><button wire:click="edit({{ $c->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $c->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button></div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="user" title="Nenhum cliente" hint="Cadastre compradores para registrar vendas."><button wire:click="create" class="btn-primary">Novo cliente</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-violet"><x-icon name="user" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Novo' }} cliente</h3><p class="text-xs text-stone-400">Comprador, documento e contato</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Nome *</label><input wire:model="form.name" class="input"></div>
        <div><label class="label">Tipo</label><select wire:model="form.customer_type" class="input"><option value="pf">Pessoa física</option><option value="pj">Pessoa jurídica</option><option value="coop">Cooperativa</option></select></div>
        <div><label class="label">Documento</label><input wire:model="form.document" class="input"></div>
        <div><label class="label">Telefone</label><input wire:model="form.phone" class="input"></div>
        <div><label class="label">E-mail</label><input wire:model="form.email" type="email" class="input"></div>
        <div><label class="label">Endereço</label><input wire:model="form.address" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir cliente?</h3><p class="text-sm text-stone-500 mt-1">Vendas vinculadas serão mantidas no histórico.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>