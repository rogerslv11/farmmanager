<div class="anim-fade-up">
  <div class="filter-bar">
    <span class="text-xs text-stone-400 font-medium flex items-center gap-1.5"><x-icon name="truck" class="w-4 h-4" />{{ $items->total() }} colheitas · perdas e qualidade por plantação</span>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Registrar colheita</button>
  </div>

  @php
    $harvestsAll = $items->getCollection();
    $byQuality = $harvestsAll->groupBy(fn ($h) => $h->quality ?? 'não classificada')->map->count();
    $totalLoss = $harvestsAll->sum('losses');
    $totalQty = $harvestsAll->sum('quantity');
    $lossPct = $totalQty > 0 ? round($totalLoss / $totalQty * 100, 1) : 0;
  @endphp

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="truck" label="Colheitas" :value="$items->total()" hint="registros totais" tint="tint-orange" />
    <x-stat icon="package" label="Volume colhido" :value="number_format($totalQty, 0, ',', '.')" hint="unidades somadas" tint="tint-agro" />
    <x-stat icon="alert" label="Perdas totais" :value="number_format($totalLoss, 0, ',', '.')" hint="{{ $lossPct }}% do volume" tint="tint-red" />
    <div class="card !p-5">
      <p class="text-xs font-semibold text-stone-500 dark:text-stone-400">Qualidade média</p>
      @php $premium = $byQuality['premium'] ?? 0; $totalQ = $harvestsAll->count(); @endphp
      <p class="mt-1.5 text-[26px] leading-none font-extrabold tracking-tight text-stone-900 tabular-nums dark:text-white">{{ $totalQ > 0 ? round($premium / $totalQ * 100) : 0 }}%</p>
      <div class="mt-2 flex gap-1">
        @foreach(['premium' => 'tint-amber', 'padrao' => 'tint-agro', 'inferior' => 'tint-red'] as $k => $c)
          @php $cnt = $byQuality[$k] ?? 0; $pct = $totalQ > 0 ? $cnt / $totalQ * 100 : 0; @endphp
          <div class="kpi-tint {{ $c }} !w-7 !h-7 !rounded-md" title="{{ ucfirst($k) }}: {{ $cnt }}" style="flex: 0 0 auto; opacity: {{ $cnt > 0 ? 1 : 0.3 }}"></div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Data</th><th class="table-th">Plantação</th><th class="table-th text-right">Qtd</th><th class="table-th">Perdas</th><th class="table-th">Qualidade</th><th class="table-th">Responsável</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $h)
      @php $hPct = $h->quantity > 0 ? min(100, ($h->losses / $h->quantity) * 100) : 0; @endphp
      <tr>
        <td class="table-td tabular-nums">{{ $h->harvested_at?->format('d/m/Y') }}</td>
        <td class="table-td"><div class="flex items-center gap-2"><span class="kpi-tint tint-orange !w-7 !h-7 !rounded-md"><x-icon name="truck" class="w-3.5 h-3.5" /></span><div><b>#{{ $h->planting_id }}</b> {{ $h->crop?->name }}<br><span class="text-xs text-stone-400">{{ $h->field?->name }}</span></div></div></td>
        <td class="table-td text-right font-extrabold tabular-nums">{{ $h->quantity }} {{ $h->unit }}</td>
        <td class="table-td"><div class="flex items-center gap-2 min-w-[100px]"><div class="flex-1 h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-red-500 to-red-400" style="width: {{ $hPct }}%"></div></div><span class="text-xs tabular-nums font-bold">{{ $h->losses }}</span></div></td>
        <td class="table-td"><x-badge :color="$h->quality === 'premium' ? 'orange' : ($h->quality === 'padrao' ? 'green' : ($h->quality === 'inferior' ? 'red' : 'stone'))">{{ $h->quality ?? '—' }}</x-badge></td>
        <td class="table-td">{{ $h->responsible?->name ?? '—' }}</td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $h->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $h->id }})" class="link-danger ml-2">Excluir</button></td></tr>
    @empty <tr><td colspan="7" class="!border-0"><x-empty icon="truck" title="Nenhuma colheita registrada" hint="Registre a primeira colheita para acompanhar perdas e qualidade."><button wire:click="create" class="btn-primary">Registrar colheita</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-orange"><x-icon name="truck" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Registrar colheita</h3><p class="text-xs text-stone-400">Volume colhido, qualidade e perdas</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Plantação *</label><select wire:model="form.planting_id" class="input"><option value="">—</option>@foreach($plantings as $p)<option value="{{ $p->id }}">#{{ $p->id }} {{ $p->crop?->name }} · {{ $p->field?->name }}</option>@endforeach</select></div>
        <div><label class="label">Data *</label><input type="date" wire:model="form.harvested_at" class="input"></div>
        <div><label class="label">Quantidade *</label><input type="number" step="0.001" wire:model="form.quantity" class="input"></div>
        <div><label class="label">Unidade</label><select wire:model="form.unit" class="input"><option value="sc">Sacas</option><option value="kg">Kg</option><option value="ton">Ton</option></select></div>
        <div><label class="label">Perdas</label><input type="number" step="0.001" wire:model="form.losses" class="input"></div>
        <div><label class="label">Qualidade</label><select wire:model="form.quality" class="input"><option value="">—</option><option value="premium">Premium</option><option value="padrao">Padrão</option><option value="inferior">Inferior</option></select></div>
        <div><label class="label">Responsável</label><select wire:model="form.responsible_id" class="input"><option value="">—</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
        <div><label class="label">Equipamento</label><input wire:model="form.equipment_used" class="input" placeholder="Colheitadeira JX-07"></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir colheita?</h3><p class="text-sm text-stone-500 mt-1">A plantação vinculada terá a produção reduzida.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>