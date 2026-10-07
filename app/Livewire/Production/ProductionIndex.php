<?php

namespace App\Livewire\Production;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use App\Models\Field;
use App\Models\Planting;
use App\Models\Production;
use App\Models\Season;
use App\Services\ProductionService;
use Livewire\WithPagination;

class ProductionIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['crop_id' => '', 'season_id' => '', 'field_id' => '', 'planting_id' => '', 'produced_at' => '', 'quantity' => '', 'unit' => 'sc', 'quality' => '', 'batch' => '', 'operator' => '', 'notes' => ''];

    protected function resetForm(): void
    {
        $this->form = ['crop_id' => '', 'season_id' => '', 'field_id' => '', 'planting_id' => '', 'produced_at' => now()->format('Y-m-d'), 'quantity' => '', 'unit' => 'sc', 'quality' => '', 'batch' => '', 'operator' => '', 'notes' => ''];
    }

    protected function fillForm(int $id): void
    {
        $p = Production::findOrFail($id);
        $this->form = ['crop_id' => $p->crop_id, 'season_id' => $p->season_id, 'field_id' => $p->field_id, 'planting_id' => $p->planting_id, 'produced_at' => $p->produced_at?->format('Y-m-d'), 'quantity' => $p->quantity, 'unit' => $p->unit, 'quality' => $p->quality, 'batch' => $p->batch, 'operator' => $p->operator, 'notes' => $p->notes];
    }

    public function save(ProductionService $service): void
    {
        $this->validate(['form.crop_id' => 'required|exists:crops,id', 'form.quantity' => 'required|numeric|min:0.001', 'form.produced_at' => 'required|date']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        if ($this->editingId) {
            Production::findOrFail($this->editingId)->update($data);
            $this->notify('Produção atualizada.');
        } else {
            $service->recordProduction($data);
            $this->notify('Produção registrada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Production::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Registro excluído.');
    }

    public function render()
    {
        $items = Production::with(['crop', 'season', 'field'])
            ->when($this->search, fn ($q) => $q->where('batch', 'like', "%{$this->search}%"))
            ->orderBy('produced_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.production.index', [
            'items' => $items,
            'crops' => Crop::orderBy('name')->get(),
            'seasons' => Season::orderByDesc('id')->get(),
            'fields' => Field::orderBy('name')->get(),
            'plantings' => Planting::with('crop')->orderByDesc('id')->limit(200)->get(),
        ]);
    }
}
