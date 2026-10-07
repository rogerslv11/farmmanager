<?php

namespace App\Livewire\Equipment;

use App\Livewire\CrudIndex;
use App\Models\Equipment;
use App\Models\Property;
use Livewire\WithPagination;

class EquipmentIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['name' => '', 'asset_code' => '', 'category' => 'trator', 'brand' => '', 'model' => '', 'year' => '', 'property_id' => '', 'hour_meter' => '', 'acquired_at' => '', 'acquisition_cost' => '', 'status' => 'disponivel', 'notes' => ''];

    protected function resetForm(): void { $this->form = ['name' => '', 'asset_code' => '', 'category' => 'trator', 'brand' => '', 'model' => '', 'year' => '', 'property_id' => '', 'hour_meter' => '', 'acquired_at' => '', 'acquisition_cost' => '', 'status' => 'disponivel', 'notes' => '']; }
    protected function fillForm(int $id): void
    {
        $e = Equipment::findOrFail($id);
        $this->form = ['name' => $e->name, 'asset_code' => $e->asset_code, 'category' => $e->category, 'brand' => $e->brand, 'model' => $e->model, 'year' => $e->year, 'property_id' => $e->property_id, 'hour_meter' => $e->hour_meter, 'acquired_at' => $e->acquired_at?->format('Y-m-d'), 'acquisition_cost' => $e->acquisition_cost, 'status' => $e->status, 'notes' => $e->notes];
    }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Equipment::findOrFail($this->editingId)->update($data) : Equipment::create($data);
        $this->showModal = false;
        $this->notify('Máquina salva.');
    }

    public function delete(): void
    {
        Equipment::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Máquina excluída.');
    }

    public function render()
    {
        $items = Equipment::with(['property', 'maintenances'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('name')->paginate($this->perPage);

        return view('livewire.equipment.index', ['items' => $items, 'properties' => Property::orderBy('name')->get()]);
    }
}
