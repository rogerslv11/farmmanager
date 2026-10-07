<?php

namespace App\Livewire\Properties;

use App\Livewire\CrudIndex;
use App\Models\Property;
use App\Services\AuditService;
use Livewire\WithPagination;

class PropertyIndex extends CrudIndex
{
    use WithPagination;

    public array $form = [
        'name' => '', 'description' => '', 'owner_name' => '', 'document' => '',
        'address' => '', 'city' => '', 'state' => '', 'total_area' => '',
        'productive_area' => '', 'latitude' => '', 'longitude' => '',
        'property_type' => 'fazenda', 'status' => 'ativa',
    ];

    protected function resetForm(): void
    {
        $this->form = ['name' => '', 'description' => '', 'owner_name' => '', 'document' => '', 'address' => '', 'city' => '', 'state' => '', 'total_area' => '', 'productive_area' => '', 'latitude' => '', 'longitude' => '', 'property_type' => 'fazenda', 'status' => 'ativa'];
    }

    protected function fillForm(int $id): void
    {
        $p = Property::findOrFail($id);
        $this->form = $p->only(array_keys($this->form));
    }

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.city' => 'nullable|string|max:120',
            'form.state' => 'nullable|string|max:2',
            'form.total_area' => 'nullable|numeric|min:0',
            'form.productive_area' => 'nullable|numeric|min:0',
            'form.latitude' => 'nullable|numeric|between:-90,90',
            'form.longitude' => 'nullable|numeric|between:-180,180',
            'form.status' => 'required|in:ativa,inativa',
        ];
    }

    public function save(): void
    {
        $this->validate($this->rules());
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;

        if ($this->editingId) {
            $p = Property::findOrFail($this->editingId);
            $old = $p->toArray();
            $p->update($data);
            AuditService::log('property.update', $p, "Propriedade \"{$p->name}\" atualizada.", $old, $p->fresh()->toArray());
            $this->notify('Propriedade atualizada.');
        } else {
            $p = Property::create($data);
            AuditService::log('property.create', $p, "Propriedade \"{$p->name}\" criada.");
            $this->notify('Propriedade criada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        $p = Property::findOrFail($this->deletingId);
        $p->delete();
        AuditService::log('property.delete', $p, "Propriedade \"{$p->name}\" excluída.");
        $this->showDelete = false;
        $this->notify('Propriedade excluída.');
    }

    public function render()
    {
        $items = Property::withCount(['fields', 'plantings'])
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $stats = [
            'count' => Property::count(),
            'area' => (float) Property::sum('total_area'),
            'productive' => (float) Property::sum('productive_area'),
            'fields' => \App\Models\Field::count(),
        ];
        $stats['productive_pct'] = $stats['area'] > 0 ? round($stats['productive'] / $stats['area'] * 100, 1) : 0;

        return view('livewire.properties.index', compact('items', 'stats'));
    }
}
