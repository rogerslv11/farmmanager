<?php

namespace App\Livewire\Harvests;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use App\Models\Employee;
use App\Models\Field;
use App\Models\Harvest;
use App\Models\Planting;
use App\Services\ProductionService;
use Livewire\WithPagination;

class HarvestIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['planting_id' => '', 'field_id' => '', 'crop_id' => '', 'responsible_id' => '', 'harvested_at' => '', 'quantity' => '', 'unit' => 'sc', 'losses' => '', 'quality' => '', 'equipment_used' => '', 'notes' => ''];

    protected function resetForm(): void
    {
        $this->form = ['planting_id' => '', 'field_id' => '', 'crop_id' => '', 'responsible_id' => '', 'harvested_at' => now()->format('Y-m-d'), 'quantity' => '', 'unit' => 'sc', 'losses' => '', 'quality' => '', 'equipment_used' => '', 'notes' => ''];
    }

    protected function fillForm(int $id): void
    {
        $h = Harvest::findOrFail($id);
        $this->form = ['planting_id' => $h->planting_id, 'field_id' => $h->field_id, 'crop_id' => $h->crop_id, 'responsible_id' => $h->responsible_id, 'harvested_at' => $h->harvested_at?->format('Y-m-d'), 'quantity' => $h->quantity, 'unit' => $h->unit, 'losses' => $h->losses, 'quality' => $h->quality, 'equipment_used' => $h->equipment_used, 'notes' => $h->notes];
    }

    public function save(ProductionService $service): void
    {
        $this->validate(['form.planting_id' => 'required|exists:plantings,id', 'form.quantity' => 'required|numeric|min:0.001', 'form.harvested_at' => 'required|date']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $planting = Planting::find($data['planting_id']);
        $data['field_id'] = $data['field_id'] ?? $planting?->field_id;
        $data['crop_id'] = $data['crop_id'] ?? $planting?->crop_id;
        if ($this->editingId) {
            Harvest::findOrFail($this->editingId)->update($data);
            $this->notify('Colheita atualizada.');
        } else {
            $service->recordHarvest($data);
            $this->notify('Colheita registrada e produção atualizada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Harvest::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Colheita excluída.');
    }

    public function render()
    {
        $items = Harvest::with(['crop', 'field', 'planting', 'responsible'])
            ->orderBy('harvested_at', 'desc')->paginate($this->perPage);

        return view('livewire.harvests.index', [
            'items' => $items,
            'plantings' => Planting::with('crop')->orderByDesc('id')->limit(300)->get(),
            'employees' => Employee::active()->orderBy('name')->get(),
            'crops' => Crop::orderBy('name')->get(),
            'fields' => Field::orderBy('name')->get(),
        ]);
    }
}
