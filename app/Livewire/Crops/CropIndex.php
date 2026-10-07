<?php

namespace App\Livewire\Crops;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use Livewire\WithPagination;

class CropIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['name' => '', 'code' => '', 'description' => '', 'average_cycle_days' => '', 'production_unit' => 'sc', 'expected_yield' => '', 'status' => 'ativa'];

    protected function resetForm(): void
    {
        $this->form = ['name' => '', 'code' => '', 'description' => '', 'average_cycle_days' => '', 'production_unit' => 'sc', 'expected_yield' => '', 'status' => 'ativa'];
    }

    protected function fillForm(int $id): void
    {
        $c = Crop::findOrFail($id);
        $this->form = ['name' => $c->name, 'code' => $c->code, 'description' => $c->description, 'average_cycle_days' => $c->average_cycle_days, 'production_unit' => $c->production_unit, 'expected_yield' => $c->expected_yield, 'status' => $c->status];
    }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Crop::findOrFail($this->editingId)->update($data) : Crop::create($data);
        $this->showModal = false;
        $this->notify('Cultura salva.');
    }

    public function delete(): void
    {
        Crop::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Cultura excluída.');
    }

    public function render()
    {
        $items = Crop::withCount('plantings')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.crops.index', compact('items'));
    }
}
