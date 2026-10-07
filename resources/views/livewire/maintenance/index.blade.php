<div class="anim-fade-up">
  @php $cgEq = app(\App\Services\ChartService::class)->maintenanceCostByEquipment(); $cgType = app(\App\Services\ChartService::class)->maintenanceByType(); @endphp

  <div class="grid lg:grid-cols-4 gap-3.5 mb-4">
    <x-stat icon="sliders" label="Manutenções" :value="$items->total()" hint="registros totais" tint="tint-orange" />
    <x-stat icon="alert" label="Atrasadas" :value="$items->getCollection()->filter(fn ($m) => $m->next_at && $m->next_at->isPast())->count()" hint="vencidas" tint="tint-red" />
    <x-stat icon="check" label="Preventivas" :value="$items->getCollection()->where('maintenance_type', 'preventiva')->count()" hint="programadas" tint="tint-sky" />
    <x-stat icon="wallet" label="Custo total" :value="'R$ '.number_format($items->sum('cost'), 0, ',', '.')" hint="acumulado" tint="tint-earth" />
  </div>

  <div class="filter-bar">
    <select wire:model.live="statusFilter" class="input !w-52"><option value="">Todas as manutenções</option><option value="atrasada">Atrasadas</option></select>
    <span class="text-xs text-stone-400 font-medium ml-1">preventivas e corretivas</span>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova manutenção</button>
  </div>

  <div class="grid lg:grid-cols-3 gap-4 mb-4">
    <div class="card lg:col-span-2 relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-earth-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="flex items-center gap-2 mb-1">
          <span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="tool" class="w-4 h-4" /></span>
          <div><h3 class="font-extrabold tracking-tight">Custo por máquina</h3><p class="text-xs text-stone-400">Acumulado · R$ — onde a frota mais consome</p></div>
        </div>
        <div class="relative h-56 mt-3"><canvas id="maint-eq"></canvas></div>
      </div>
    </div>
    <div class="card relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-sky-100/40 pointer-events-none"></div>
      <div class="relative">
        <div class="mb-1 flex items-center gap-2">
          <span class="kpi-tint tint-sky !w-8 !h-8 !rounded-lg"><x-icon name="pie" class="w-4 h-4" /></span>
          <h3 class="font-extrabold tracking-tight text-sm">Preventiva × Corretiva</h3>
        </div>
        <div class="relative h-44 mt-2"><canvas id="maint-type"></canvas></div>
        <p class="text-xs text-stone-500 mt-2 leading-relaxed">Manutenção preventiva custa até 3× menos que a corretiva. Programe revisões por horímetro e data para manter a frota disponível na janela da safra.</p>
        <a href="{{ route('equipment.index') }}" class="btn-secondary mt-3 !py-2 !text-xs w-full justify-center">Ver frota →</a>
      </div>
    </div>
  </div>

  @script
  <script>
    fm.make('maint-eq', { type: 'bar', data: { labels: @js($cgEq['labels']), datasets: [{ data: @js($cgEq['values']), backgroundColor: '#96744a', borderRadius: 8 }] }, options: { maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false }, tooltip: fm.moneyTip }, scales: { x: { grid: { color: fm.grid }, beginAtZero: true, ticks: { callback: v => fm.brlK(v) } }, y: { grid: { display: false } } } } });
    fm.make('maint-type', { type: 'doughnut', data: { labels: @js($cgType['labels']), datasets: [{ data: @js($cgType['values']), backgroundColor: ['#0ea5e9','#f59e0b'], borderWidth: 3, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } } }, tooltip: fm.countTip } } });
  </script>
  @endscript

  <div class="table-wrap"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Equipamento</th><th class="table-th">Tipo</th><th class="table-th">Realizada</th><th class="table-th">Próxima</th><th class="table-th">Custo</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $m)
      <tr>
        <td class="table-td"><div class="flex items-center gap-2"><span class="kpi-tint tint-earth !w-8 !h-8 !rounded-lg"><x-icon name="tool" class="w-4 h-4" /></span><div><p class="font-bold text-stone-800 dark:text-stone-100">{{ $m->equipment?->name }}</p><p class="text-xs text-stone-400">{{ $m->description }}</p></div></div></td>
        <td class="table-td"><x-badge :color="$m->maintenance_type === 'preventiva' ? 'sky' : 'orange'">{{ $m->maintenance_type }}</x-badge></td>
        <td class="table-td tabular-nums">{{ $m->performed_at?->format('d/m/Y') }}</td>
        <td class="table-td">@if($m->next_at && $m->next_at->isPast())<x-badge color="red">{{ $m->next_at->format('d/m/Y') }} · atrasada</x-badge>@else{{ $m->next_at?->format('d/m/Y') ?? '—' }}@endif</td>
        <td class="table-td font-bold tabular-nums">R$ {{ number_format($m->cost, 2, ',', '.') }}</td>
        <td class="table-td whitespace-nowrap text-right"><button wire:click="edit({{ $m->id }})" class="link-action">Editar</button><button wire:click="confirmDelete({{ $m->id }})" class="link-danger ml-3">Excluir</button></td></tr>
    @empty <tr><td colspan="6" class="!border-0"><x-empty icon="sliders" title="Nenhuma manutenção" hint="Registre manutenções para acompanhar custos da frota."><button wire:click="create" class="btn-primary">Registrar manutenção</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-lg">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-earth"><x-icon name="sliders" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">Registrar manutenção</h3><p class="text-xs text-stone-400">Preventiva ou corretiva</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div><label class="label">Equipamento *</label><select wire:model="form.equipment_id" class="input"><option value="">—</option>@foreach($equipment as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
        <div><label class="label">Tipo</label><select wire:model="form.maintenance_type" class="input"><option value="preventiva">Preventiva</option><option value="corretiva">Corretiva</option></select></div>
        <div><label class="label">Realizada em *</label><input type="date" wire:model="form.performed_at" class="input"></div>
        <div><label class="label">Próxima em</label><input type="date" wire:model="form.next_at" class="input"></div>
        <div><label class="label">Responsável</label><select wire:model="form.responsible_id" class="input"><option value="">—</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
        <div><label class="label">Custo (R$)</label><input type="number" step="0.01" wire:model="form.cost" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Peças utilizadas</label><input wire:model="form.parts_used" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Descrição</label><textarea wire:model="form.description" class="input" rows="2"></textarea></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir registro?</h3><p class="text-sm text-stone-500 mt-1">Esse custo sairá do histórico.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>