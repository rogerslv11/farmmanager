<div class="anim-fade-up">
  @php $cgS = app(\App\Services\ChartService::class)->purchasesByStatus(); $cgM = app(\App\Services\ChartService::class)->purchasesMonthly(); @endphp

  <!-- Status pipeline -->
  <div class="card !p-4 mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-amber-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="cart" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Status dos pedidos</h3>
            <p class="text-xs text-stone-400">Fluxo de compras a fornecedores</p>
          </div>
        </div>
      </div>
      <div class="pipeline">
        @foreach($cgS['labels'] as $i => $label)
          @php $val = $cgS['values'][$i] ?? 0; @endphp
          <div class="pipeline-step {{ $val > 0 ? 'pipeline-step-active' : '' }}">
            <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">{{ $label }}</p>
            <p class="pipeline-num mt-0.5 {{ $val > 0 ? 'text-agro-700' : 'text-stone-400' }}">{{ $val }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-amber-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="trend" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Volume de compras</h3><p class="text-xs text-stone-400">Últimos 6 meses · R$</p></div>
        </div>
        <div class="relative h-52 mt-3"><canvas id="pur-month"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-agro-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="check" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Pedidos recebidos</h3>
        </div>
        @php $rec = $cgS['values'][array_search('Recebida', $cgS['labels'])] ?? 0; $totalP = array_sum($cgS['values']); $recPct = $totalP > 0 ? round($rec / $totalP * 100) : 0; @endphp
        <p class="text-[42px] font-black tabular-nums mt-3 text-agro-700 leading-none">{{ $recPct }}<span class="text-2xl">%</span></p>
        <p class="text-xs text-stone-400 mt-1">{{ $rec }} de {{ $totalP }} pedidos</p>
        <div class="mt-3 h-2 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-agro-600 to-agro-400" style="width: {{ $recPct }}%"></div></div>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('pur-month', { type: 'bar', data: { labels: @js($cgM['labels']), datasets: [{ data: @js($cgM['values']), backgroundColor: '#b08954', borderRadius: 8, barThickness: 22 }] }, options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: fm.moneyTip }, scales: { x: { grid: { display: false } }, y: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => fm.brlK(v) } } } } });
  </script>
  @endscript

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar pedido…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-44"><option value="">Todos status</option><option value="rascunho">Rascunho</option><option value="solicitada">Solicitada</option><option value="aprovada">Aprovada</option><option value="recebida">Recebida</option><option value="cancelada">Cancelada</option></select>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Novo pedido</button>
  </div>
  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Pedido</th><th class="table-th">Fornecedor</th><th class="table-th">Itens</th><th class="table-th text-right">Total</th><th class="table-th">Status</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $p)
      <tr>
        <td class="table-td"><div class="flex items-center gap-2.5"><span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg"><x-icon name="cart" class="w-4 h-4" /></span><div><b>{{ $p->number }}</b><br><span class="text-xs text-stone-400">{{ $p->ordered_at?->format('d/m/Y') }}</span></div></div></td>
        <td class="table-td">{{ $p->supplier?->company_name }}</td>
        <td class="table-td tabular-nums"><span class="badge badge-stone">{{ $p->items->count() }} itens</span></td>
        <td class="table-td text-right font-extrabold tabular-nums">R$ {{ number_format($p->total, 2, ',', '.') }}</td>
        <td class="table-td"><x-badge :color="$p->status === 'recebida' ? 'green' : ($p->status === 'cancelada' ? 'stone' : ($p->status === 'aprovada' ? 'sky' : 'amber'))">{{ $p->status }}</x-badge></td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="detail({{ $p->id }})" class="link-action">Ver</button>@if($p->status === 'solicitada')<button wire:click="approve({{ $p->id }})" class="link-action ml-2">Aprovar</button>@endif @if(in_array($p->status, ['solicitada','aprovada']))<button wire:click="receive({{ $p->id }})" class="btn-secondary !py-1.5 !px-3 !text-xs !border-emerald-200 !text-emerald-700 hover:!bg-emerald-50 ml-2">Receber ⤵</button>@endif @if(!in_array($p->status, ['recebida','cancelada']))<button wire:click="cancel({{ $p->id }})" class="link-danger ml-2">Cancelar</button>@endif</td></tr>
    @empty <tr><td colspan="6" class="!border-0"><x-empty icon="cart" title="Nenhum pedido" hint="Crie um pedido para gerar entrada automática no estoque."><button wire:click="create" class="btn-primary">Novo pedido</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>

  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-amber"><x-icon name="cart" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Novo pedido de compra</h3><p class="text-xs text-stone-400">Itens serão recebidos como entrada no estoque</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Fornecedor *</label><select wire:model="form.supplier_id" class="input"><option value="">—</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
        <div><label class="label">Propriedade destino</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Desconto (R$)</label><input type="number" step="0.01" wire:model="form.discount" class="input"></div>
        <div><label class="label">Impostos (R$)</label><input type="number" step="0.01" wire:model="form.taxes" class="input"></div>
      </div>
      <div class="mt-4"><div class="flex justify-between items-center mb-2"><p class="font-semibold text-sm">Itens</p><button wire:click="addLine" class="text-xs text-agro-600 font-bold flex items-center gap-1"><x-icon name="plus" class="w-3.5 h-3.5" /> Adicionar item</button></div>
        @foreach($lines as $i => $l)<div class="grid grid-cols-12 gap-2 mb-2">
          <select wire:model="lines.{{ $i }}.inventory_item_id" class="input col-span-5 !text-xs"><option value="">Produto…</option>@foreach($products as $pr)<option value="{{ $pr->id }}">{{ $pr->name }}</option>@endforeach</select>
          <input type="number" step="0.001" wire:model="lines.{{ $i }}.quantity" class="input col-span-2 !text-xs" placeholder="Qtd">
          <input type="number" step="0.01" wire:model="lines.{{ $i }}.unit_price" class="input col-span-3 !text-xs" placeholder="R$ unit">
          <button wire:click="removeLine({{ $i }})" class="col-span-2 text-xs text-red-500 hover:bg-red-50 rounded-lg transition">remover</button>
        </div>@endforeach
        @error('lines')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Criar pedido</button></div>
    </div>
  </div>
  @endif

  @if($showDetail && $detail)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDetail', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-4">
        <span class="kpi-tint tint-amber"><x-icon name="cart" class="w-5 h-5" /></span>
        <div><h3 class="font-extrabold text-lg tracking-tight">{{ $detail->number }}</h3><p class="text-xs text-stone-400">{{ $detail->supplier?->company_name }}</p></div>
        <x-badge :color="$detail->status === 'recebida' ? 'green' : 'amber'" class="ml-auto">{{ $detail->status }}</x-badge>
      </div>
      <div class="tl">
        @foreach($detail->items as $it)
          <div class="tl-item tl-item-done">
            <div class="flex justify-between"><span class="text-sm">{{ $it->item?->name }} · <b>{{ $it->quantity }} {{ $it->unit }}</b></span><b class="tabular-nums text-sm">R$ {{ number_format($it->subtotal, 2, ',', '.') }}</b></div>
          </div>
        @endforeach
      </div>
      <div class="mt-3 pt-3 border-t border-stone-100 dark:border-white/10 flex justify-between items-center">
        <span class="text-xs text-stone-400 font-semibold uppercase tracking-wider">Total</span>
        <b class="text-xl font-black tabular-nums text-agro-700">R$ {{ number_format($detail->total, 2, ',', '.') }}</b>
      </div>
      <div class="flex justify-end mt-4"><button wire:click="$set('showDetail', false)" class="btn-secondary">Fechar</button></div>
    </div>
  </div>
  @endif
</div>