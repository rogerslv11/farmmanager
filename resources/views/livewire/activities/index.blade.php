<div class="anim-fade-up">
  @php $statusCounts = ['pendente'=>0,'agendada'=>0,'em_andamento'=>0,'concluida'=>0,'cancelada'=>0]; @endphp
  @php
    // Não temos collection no momento; montaremos a partir de uma nova coleção para o gráfico
    $allForChart = \App\Models\AgriculturalActivity::where('organization_id', auth()->user()->organization_id)->get();
    $sc = $allForChart->groupBy('status')->map->count();
  @endphp
  <!-- Status pipeline mini -->
  <div class="card !p-4 mb-4 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-amber-100/30 pointer-events-none"></div>
    <div class="relative flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <span class="kpi-tint tint-amber !w-9 !h-9 !rounded-lg"><x-icon name="clipboard" class="w-5 h-5" /></span>
        <div>
          <h3 class="font-extrabold tracking-tight">Status das atividades</h3>
          <p class="text-xs text-stone-400">Distribuição em tempo real</p>
        </div>
      </div>
      <div class="pipeline">
        @foreach([
          'pendente' => ['Pendente', 'red'],
          'agendada' => ['Agendada', 'sky'],
          'em_andamento' => ['Em andamento', 'amber'],
          'concluida' => ['Concluída', 'green'],
          'cancelada' => ['Cancelada', 'stone'],
        ] as $k => [$lbl, $color])
          @php $val = $sc[$k] ?? 0; @endphp
          <div class="pipeline-step {{ $val > 0 ? 'pipeline-step-active' : '' }}">
            <p class="text-[10px] font-bold uppercase tracking-wider text-stone-400 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>{{ $lbl }}</p>
            <p class="pipeline-num mt-0.5 {{ $val > 0 ? 'text-agro-700' : 'text-stone-400' }}">{{ $val }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="filter-bar">
    <div class="search-wrap"><x-icon name="search" class="w-4 h-4" /><input wire:model.live.debounce.300ms="search" placeholder="Buscar atividade…" class="search-input !w-52"></div>
    <select wire:model.live="statusFilter" class="input !w-44"><option value="">Todos status</option><option value="pendente">Pendente</option><option value="agendada">Agendada</option><option value="em_andamento">Em andamento</option><option value="concluida">Concluída</option><option value="cancelada">Cancelada</option></select>
    <div class="flex gap-1 bg-stone-100 rounded-xl p-1 dark:bg-white/5">
      <button wire:click="$set('view','list')" class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ $view === 'list' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400 hover:text-stone-600' }}"><x-icon name="list" class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" />Lista</button>
      <button wire:click="$set('view','calendar')" class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ $view === 'calendar' ? 'bg-white shadow text-stone-800 dark:bg-stone-700 dark:text-white' : 'text-stone-400 hover:text-stone-600' }}"><x-icon name="calendar" class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" />Calendário</button>
    </div>
    <button wire:click="create" class="btn-primary ml-auto"><x-icon name="plus" class="w-4 h-4" /> Nova atividade</button>
  </div>

  @if($view === 'calendar')
    <div class="card relative overflow-hidden">
      <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-agro-100/30 pointer-events-none"></div>
      <div class="relative">
        <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">@foreach(['SEG','TER','QUA','QUI','SEX','SAB','DOM'] as $d)<div class="py-1">{{ $d }}</div>@endforeach</div>
        <div class="grid grid-cols-7 gap-1.5">
          @foreach($items as $a)
            @php $statusColor = ['pendente'=>'border-red-200 bg-red-50','agendada'=>'border-sky-200 bg-sky-50','em_andamento'=>'border-amber-200 bg-amber-50','concluida'=>'border-agro-200 bg-agro-50','cancelada'=>'border-stone-200 bg-stone-50'][$a->status] ?? 'border-stone-200 bg-stone-50'; @endphp
            <div class="rounded-xl border p-2.5 min-h-[100px] text-xs {{ $statusColor }} hover:shadow-md transition dark:bg-white/5 dark:border-white/10">
              <div class="flex items-center justify-between mb-1">
                <p class="font-black text-sm tabular-nums">{{ $a->scheduled_at?->format('d/m') }}</p>
                <span class="w-1.5 h-1.5 rounded-full {{ $a->is_overdue ? 'bg-red-500' : 'bg-agro-500' }}"></span>
              </div>
              <p class="font-bold truncate text-stone-700 dark:text-stone-200">{{ $a->title }}</p>
              <p class="text-stone-400 truncate text-[10px]">{{ $a->property?->name }}</p>
              <div class="flex items-center justify-between mt-1.5">
                <span class="badge badge-stone !text-[9px] !px-1.5 !py-0">{{ $a->activity_type }}</span>
                @if($a->is_overdue)<span class="badge badge-red !text-[9px] !px-1.5 !py-0">!</span>@endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @else
  <div class="card !p-0 overflow-hidden"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr><th class="table-th">Atividade</th><th class="table-th">Data</th><th class="table-th">Responsável</th><th class="table-th text-right">Custo</th><th class="table-th">Status</th><th class="table-th"></th></tr></thead>
    <tbody>@forelse($items as $a)<tr class="hover:bg-stone-50 {{ $a->is_overdue ? 'bg-red-50/50 dark:bg-red-500/5' : '' }}">
      <td class="table-td"><div class="flex items-center gap-2.5"><span class="kpi-tint tint-amber !w-8 !h-8 !rounded-lg shrink-0"><x-icon name="clipboard" class="w-4 h-4" /></span><div><b>{{ $a->title }}</b><br><span class="text-xs text-stone-400">{{ $a->activity_type }} · {{ $a->property?->name }} {{ $a->field ? '· '.$a->field->name : '' }}</span></div></div></td>
      <td class="table-td">{{ $a->scheduled_at?->format('d/m/Y') }} @if($a->is_overdue)<x-badge color="red">atrasada</x-badge>@endif</td>
      <td class="table-td"><span class="inline-flex items-center gap-1.5"><span class="avatar w-6 h-6 text-[10px]">{{ strtoupper(substr($a->responsible?->name ?? '—', 0, 1)) }}</span>{{ $a->responsible?->name ?? '—' }}</span></td>
      <td class="table-td tabular-nums font-bold">R$ {{ number_format($a->cost, 2, ',', '.') }}</td>
      <td class="table-td"><x-badge :color="$a->status === 'concluida' ? 'green' : ($a->status === 'cancelada' ? 'stone' : 'amber')">{{ $a->status }}</x-badge></td>
      <td class="table-td whitespace-nowrap text-right">@if($a->status !== 'concluida')<button wire:click="markDone({{ $a->id }})" class="btn-secondary !py-1.5 !px-3 !text-xs !border-agro-200 !text-agro-700 hover:!bg-agro-50"><x-icon name="check" class="w-3.5 h-3.5" /> Concluir</button>@endif <button wire:click="edit({{ $a->id }})" class="btn-ghost">Editar</button></td></tr>
    @empty <tr><td colspan="6" class="!border-0"><x-empty icon="clipboard" title="Nenhuma atividade" hint="Crie atividades para organizar plantio, manejo, colheita e manutenção."><button wire:click="create" class="btn-primary">Criar atividade</button></x-empty></td></tr> @endforelse</tbody>
  </table></div></div>
  <div class="mt-4">{{ $items->links() }}</div>
  @endif

  @if($showModal)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showModal', false)"></div>
    <div class="modal-panel max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center gap-3 mb-5"><span class="kpi-tint tint-amber"><x-icon name="clipboard" class="w-5 h-5" /></span><div><h3 class="font-extrabold text-lg tracking-tight">{{ $editingId ? 'Editar' : 'Nova' }} atividade</h3><p class="text-xs text-stone-400">Plantio, manejo, colheita ou manutenção</p></div></div>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2"><label class="label">Título *</label><input wire:model="form.title" class="input" placeholder="Aplicação de fertilizante"></div>
        <div><label class="label">Tipo</label><select wire:model="form.activity_type" class="input"><option value="plantio">Plantio</option><option value="preparo_solo">Preparo do solo</option><option value="irrigacao">Irrigação</option><option value="fertilizante">Fertilizante</option><option value="defensivo">Defensivos</option><option value="poda">Poda</option><option value="capina">Capina</option><option value="monitoramento">Monitoramento</option><option value="colheita">Colheita</option><option value="manutencao">Manutenção</option></select></div>
        <div><label class="label">Status</label><select wire:model="form.status" class="input"><option value="pendente">Pendente</option><option value="agendada">Agendada</option><option value="em_andamento">Em andamento</option><option value="concluida">Concluída</option><option value="cancelada">Cancelada</option></select></div>
        <div><label class="label">Propriedade</label><select wire:model="form.property_id" class="input"><option value="">—</option>@foreach($properties as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div><label class="label">Talhão</label><select wire:model="form.field_id" class="input"><option value="">—</option>@foreach($fields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>
        <div><label class="label">Plantação</label><select wire:model="form.planting_id" class="input"><option value="">—</option>@foreach($plantings as $p)<option value="{{ $p->id }}">#{{ $p->id }} {{ $p->crop?->name }}</option>@endforeach</select></div>
        <div><label class="label">Responsável</label><select wire:model="form.responsible_id" class="input"><option value="">—</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
        <div><label class="label">Data</label><input type="date" wire:model="form.scheduled_at" class="input"></div>
        <div><label class="label">Duração (h)</label><input type="number" step="0.5" wire:model="form.duration_hours" class="input"></div>
        <div><label class="label">Custo (R$)</label><input type="number" step="0.01" wire:model="form.cost" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Equipe</label><div class="flex flex-wrap gap-2">@foreach($employees as $e)<label class="text-xs flex items-center gap-1 border rounded-lg px-2 py-1 cursor-pointer hover:bg-stone-50 transition"><input type="checkbox" wire:model="selectedEmployees" value="{{ $e->id }}"> {{ $e->name }}</label>@endforeach</div></div>
        <div class="sm:col-span-2"><label class="label">Descrição</label><textarea wire:model="form.description" class="input" rows="2"></textarea></div>
      </div>
      <div class="flex justify-end gap-2 mt-6"><button wire:click="$set('showModal', false)" class="btn-secondary">Cancelar</button><button wire:click="save" class="btn-primary">Salvar atividade</button></div>
    </div>
  </div>
  @endif
  @if($showDelete)
  <div class="modal-backdrop">
    <div class="absolute inset-0" wire:click="$set('showDelete', false)"></div>
    <div class="modal-panel max-w-sm text-center"><span class="kpi-tint tint-red !w-12 !h-12 !rounded-2xl mx-auto"><x-icon name="alert" class="w-6 h-6" /></span><h3 class="font-extrabold text-lg mt-3">Excluir atividade?</h3><p class="text-sm text-stone-500 mt-1">Esta ação não pode ser desfeita.</p><div class="flex justify-center gap-2 mt-5"><button wire:click="$set('showDelete', false)" class="btn-secondary">Cancelar</button><button wire:click="delete" class="btn-danger">Excluir</button></div></div>
  </div>
  @endif
</div>