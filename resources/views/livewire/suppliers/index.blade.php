<div class="anim-fade-up">
  <div class="grid lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="briefcase" label="Fornecedores" :value="$items->total()" hint="cadastrados" tint="tint-sky" />
    <x-stat icon="package" label="Produtos vinculados" :value="$items->getCollection()->sum('items_count')" hint="via estoque" tint="tint-violet" />
    <x-stat icon="cart" label="Pedidos" :value="$items->getCollection()->sum('purchases_count')" hint="histórico" tint="tint-amber" />
    <x-stat icon="trend" label="Volume comprado" :value="'R$ '.number_format(\App\Models\Purchase::whereIn('supplier_id', $items->pluck('id'))->sum('total'), 0, ',', '.')" hint="acumulado" tint="tint-emerald" />
  </div>

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar fornecedor…" class="search-input"></div>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo fornecedor</button>
  </div>

  <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $s)
      <div class="card card-hover relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-sky-100/40 pointer-events-none"></div>
        <div class="relative flex items-start gap-3">
          <span class="kpi-tint tint-sky shrink-0"><x-icon name="briefcase" class="w-5 h-5" /></span>
          <div class="min-w-0">
            <p class="font-black tracking-tight truncate">{{ $s->trade_name ?? $s->company_name }}</p>
            <p class="text-xs text-stone-400 truncate">{{ $s->company_name }} · {{ $s->category }}</p>
          </div>
        </div>
        <p class="text-xs text-stone-500 mt-2.5 flex items-center gap-1.5"><x-icon name="phone" class="w-3.5 h-3.5" />{{ $s->phone ?? '—' }} · {{ $s->email ?? '—' }}</p>
        <div class="flex gap-2 mt-3">
          <span class="inline-flex items-center gap-1 rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700 dark:bg-violet-500/15 dark:text-violet-300"><x-icon name="package" class="w-3.5 h-3.5" />{{ $s->items_count }} produtos</span>
          <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300"><x-icon name="cart" class="w-3.5 h-3.5" />{{ $s->purchases_count }} compras</span>
        </div>
        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-stone-100 dark:border-white/10"><button wire:click="edit({{ $s->id }})" class="btn-ghost">Editar</button><button wire:click="confirmDelete({{ $s->id }})" class="btn-ghost !text-red-500 hover:!bg-red-50 ml-auto">Excluir</button></div>
      </div>
    @empty <div class="col-span-full"><x-empty icon="briefcase" title="Nenhum fornecedor" hint="Cadastre parceiros de insumos, máquinas e serviços."><button wire:click="create" class="btn-primary">Novo fornecedor</button></x-empty></div> @endforelse
  </div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-sky"><x-icon name="briefcase" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Novo' }} fornecedor</h3><p class="text-xs text-stone-400">Razão social, contato e categoria</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Razão social *</label><input wire:model="form.company_name" class="input"></div>
        <div><label class="label">Nome fantasia</label><input wire:model="form.trade_name" class="input"></div>
        <div><label class="label">Documento</label><input wire:model="form.document" class="input"></div>
        <div><label class="label">Categoria</label><select wire:model="form.category" class="input"><option value="">—</option><option value="insumos">Insumos</option><option value="maquinas">Máquinas</option><option value="servicos">Serviços</option><option value="combustivel">Combustível</option></select></div>
        <div><label class="label">Telefone</label><input wire:model="form.phone" class="input"></div>
        <div><label class="label">E-mail</label><input wire:model="form.email" type="email" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Endereço</label><input wire:model="form.address" class="input"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir fornecedor?</h3><p class="text-sm text-stone-500 mt-1">Compras vinculadas permanecem no histórico.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>