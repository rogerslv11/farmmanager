<?php

namespace App\Livewire\Seasons;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use App\Models\Property;
use App\Models\Season;
use App\Services\AuditService;
use Livewire\WithPagination;

class SeasonIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['name' => '', 'year' => '', 'crop_id' => '', 'property_id' => '', 'starts_at' => '', 'ends_at' => '', 'total_area' => '', 'estimated_production' => '', 'estimated_cost' => '', 'estimated_revenue' => '', 'status' => 'planejada', 'notes' => ''];

    protected function resetForm(): void
    {
        $this->form = ['name' => '', 'year' => date('Y'), 'crop_id' => '', 'property_id' => '', 'starts_at' => '', 'ends_at' => '', 'total_area' => '', 'estimated_production' => '', 'estimated_cost' => '', 'estimated_revenue' => '', 'status' => 'planejada', 'notes' => ''];
    }

    protected function fillForm(int $id): void
    {
        $s = Season::findOrFail($id);
        $this->form = ['name' => $s->name, 'year' => $s->year, 'crop_id' => $s->crop_id, 'property_id' => $s->property_id, 'starts_at' => $s->starts_at?->format('Y-m-d'), 'ends_at' => $s->ends_at?->format('Y-m-d'), 'total_area' => $s->total_area, 'estimated_production' => $s->estimated_production, 'estimated_cost' => $s->estimated_cost, 'estimated_revenue' => $s->estimated_revenue, 'status' => $s->status, 'notes' => $s->notes];
    }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255', 'form.status' => 'required']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        if ($this->editingId) {
            $s = Season::findOrFail($this->editingId);
            $s->update($data);
            AuditService::log('season.update', $s, "Safra \"{$s->name}\" atualizada.");
            $this->notify('Safra atualizada.');
        } else {
            $s = Season::create($data);
            AuditService::log('season.create', $s, "Safra \"{$s->name}\" criada.");
            $this->notify('Safra criada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Season::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Safra excluída.');
    }

    public function render()
    {
        $items = Season::with(['crop', 'property'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
        $crops = Crop::orderBy('name')->get();
        $properties = Property::orderBy('name')->get();

        return view('livewire.seasons.index', compact('items', 'crops', 'properties'));
    }
}
