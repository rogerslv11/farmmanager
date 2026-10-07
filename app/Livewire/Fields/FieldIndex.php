<?php

namespace App\Livewire\Fields;

use App\Livewire\CrudIndex;
use App\Models\Field;
use App\Models\Property;
use App\Services\AuditService;
use Livewire\WithPagination;

class FieldIndex extends CrudIndex
{
    use WithPagination;

    public ?int $propertyFilter = null;
    public array $form = ['property_id' => '', 'name' => '', 'code' => '', 'area' => '', 'soil_type' => '', 'irrigation' => '', 'location' => '', 'status' => 'ativo', 'notes' => ''];

    protected function resetForm(): void
    {
        $this->form = ['property_id' => $this->propertyFilter ?? '', 'name' => '', 'code' => '', 'area' => '', 'soil_type' => '', 'irrigation' => '', 'location' => '', 'status' => 'ativo', 'notes' => ''];
    }

    protected function fillForm(int $id): void
    {
        $f = Field::findOrFail($id);
        $this->form = ['property_id' => $f->property_id, 'name' => $f->name, 'code' => $f->code, 'area' => $f->area, 'soil_type' => $f->soil_type, 'irrigation' => $f->irrigation, 'location' => $f->location, 'status' => $f->status, 'notes' => $f->notes];
    }

    public function save(): void
    {
        $this->validate(['form.property_id' => 'required|exists:properties,id', 'form.name' => 'required|max:255', 'form.area' => 'nullable|numeric|min:0']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        if ($this->editingId) {
            $f = Field::findOrFail($this->editingId);
            $f->update($data);
            AuditService::log('field.update', $f, "Talhão \"{$f->name}\" atualizado.");
            $this->notify('Talhão atualizado.');
        } else {
            $f = Field::create($data);
            AuditService::log('field.create', $f, "Talhão \"{$f->name}\" criado.");
            $this->notify('Talhão criado.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Field::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Talhão excluído.');
    }

    public function render()
    {
        $items = Field::with(['property', 'currentPlanting.crop'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->propertyFilter, fn ($q) => $q->where('property_id', $this->propertyFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
        $properties = Property::orderBy('name')->get();

        $stats = [
            'active' => Field::where('status', 'ativo')->count(),
            'area' => (float) Field::sum('area'),
            'irrigated' => Field::whereNotNull('irrigation')->where('irrigation', '!=', 'Sequeiro')->where('irrigation', '!=', '')->count(),
            'with_crop' => Field::whereHas('currentPlanting')->count(),
        ];

        return view('livewire.fields.index', compact('items', 'properties', 'stats'));
    }
}
