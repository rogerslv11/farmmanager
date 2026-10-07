<div class="anim-fade-up">
  @php $cgS = app(\App\Services\ChartService::class)->plantingsByStatus(); $totalP = array_sum($cgS['values']); @endphp

  <!-- Status pipeline -->
  <div class="card !p-4 mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
    <div class="relative">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="sprout" class="w-4 h-4" /></span>
          <div>
            <h3 class="font-extrabold tracking-tight">Pipeline de plantações</h3>
            <p class="text-xs text-stone-400">Onde cada plantação está no ciclo de vida</p>
          </div>
        </div>
        <span class="badge badge-agro"><span class="dot"></span>{{ $totalP }} no total</span>
      </div>
      <div class="pipeline">
        @foreach($cgS['labels'] as $i => $label)
          @php $val = $cgS['values'][$i] ?? 0; $pct = $totalP > 0 ? round($val / $totalP * 100) : 0; @endphp
          <div class="pipeline-step {{ $val > 0 ? 'pipeline-step-active' : '' }}">
            <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400">{{ $label }}</p>
            <p class="pipeline-num mt-0.5 {{ $val > 0 ? 'text-agro-700' : 'text-stone-400' }}">{{ $val }}</p>
            <div class="h-1 rounded-full bg-stone-200 overflow-hidden mt-1 dark:bg-white/10"><div class="h-full rounded-full bg-agro-500" style="width: {{ $pct }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar por cultura…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-52"><option value="">Todos status</option><option value="planejada">Planejada</option><option value="preparacao">Preparação</option><option value="plantada">Plantada</option><option value="crescimento">Em crescimento</option><option value="pronta_colheita">Pronta p/ colheita</option><option value="colhida">Colhida</option><option value="encerrada">Encerrada</option></select>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova plantação</button>
  </div>
  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
      <thead><tr><th class="table-th">Cultura / Talhão</th><th class="table-th">Propriedade</th><th class="table-th">Área</th><th class="table-th">Plantio</th><th class="table-th">Progresso</th><th class="table-th">Status</th><th class="table-th text-right">Produtiv.</th><th class="table-th"></th></tr></thead>
      <tbody>
        @forelse($items as $p)
        @php
          $statusProgress = ['planejada'=>10,'preparacao'=>25,'plantada'=>45,'crescimento'=>65,'pronta_colheita'=>85,'colhida'=>95][$p->status] ?? 50;
        @endphp
        <tr>
          <td class="table-td"><div class="flex items-center gap-2.5"><span class="kpi-tint tint-agro !w-8 !h-8 !rounded-lg"><x-icon name="sprout" class="w-4 h-4" /></span><div><b>{{ $p->crop?->name }}</b><br><span class="text-xs text-stone-400">{{ $p->field?->name }} · {{ $p->season?->name }}</span></div></div></td>
          <td class="table-td">{{ $p->property?->name }}</td>
          <td class="table-td font-extrabold tabular-nums">{{ $p->planted_area }} ha</td>
          <td class="table-td tabular-nums">{{ $p->planted_at?->format('d/m/Y') }}</td>
          <td class="table-td"><div class="flex items-center gap-2 min-w-[110px]"><div class="flex-1 h-1.5 rounded-full bg-stone-100 overflow-hidden dark:bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-agro-600 to-agro-400" style="width: {{ $statusProgress }}%"></div></div><span class="text-[10px] font-bold tabular-nums text-stone-500">{{ $statusProgress }}%</span></div></td>
          <td class="table-td"><x-badge :color="$p->status === 'colhida' ? 'green' : ($p->status === 'encerrada' ? 'stone' : 'amber')">{{ $p->status }}</x-badge></td>
          <td class="table-td tabular-nums font-bold">{{ $p->productivity }}/ha</td>
          <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $p->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $p->id }})" class="link-danger ml-3">Excluir</button></td></tr>
        @empty <tr><td colspan="8" class="!border-0"><x-empty icon="sprout" title="Nenhuma plantação" hint="Comece planejando a primeira plantação em um talhão."><button wire:click="create" class="btn-primary">Planejar plantação</button></x-empty></td></tr> @endforelse
      </tbody>
    </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-agro"><x-icon name="sprout" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} plantação</h3><p class="text-xs text-stone-400">Vincule cultura, talhão, safra e operação</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Propriedade *</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Talhão *</label><select wire:model="form.field_id" class="input"><option value="">—</option>@foreach($fields as $f)<option value="{{ $f->id }}">{{ $f->property?->name }} · {{ $f->name }}</option>@endforeach</select></div>
        <div><label class="label">Cultura *</label><select wire:model="form.crop_id" class="input"><option value="">—</option>@foreach($crops as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="label">Safra</label><select wire:model="form.season_id" class="input"><option value="">—</option>@foreach($seasons as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        <div><label class="label">Data do plantio</label><input type="date" wire:model="form.planted_at" class="input"></div>
        <div><label class="label">Previsão de colheita</label><input type="date" wire:model="form.harvest_forecast_at" class="input"></div>
        <div><label class="label">Área plantada (ha)</label><input type="number" step="0.01" wire:model="form.planted_area" class="input"></div>
        <div><label class="label">Qtd. sementes</label><input type="number" step="0.01" wire:model="form.seed_quantity" class="input"></div>
        <div><label class="label">Prod. estimada</label><input type="number" step="0.01" wire:model="form.estimated_yield" class="input"></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="planejada">Planejada</option><option value="preparacao">Preparação</option><option value="plantada">Plantada</option><option value="crescimento">Em crescimento</option><option value="pronta_colheita">Pronta p/ colheita</option><option value="colhida">Colhida</option><option value="encerrada">Encerrada</option></select></div>
        <div class="sm:col-span-2"><label class="label">Observações</label><textarea wire:model="form.notes" class="input" rows="2"></textarea></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar plantação</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir plantação?</h3><p class="text-sm text-stone-500 mt-1">Atividades e produções vinculadas serão removidas.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>