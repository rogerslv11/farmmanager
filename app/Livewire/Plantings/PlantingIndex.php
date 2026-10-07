<?php

namespace App\Livewire\Plantings;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use App\Models\Field;
use App\Models\Planting;
use App\Models\Property;
use App\Models\Season;
use App\Services\AuditService;
use Livewire\WithPagination;

class PlantingIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['property_id' => '', 'field_id' => '', 'crop_id' => '', 'season_id' => '', 'planted_at' => '', 'harvest_forecast_at' => '', 'planted_area' => '', 'seed_quantity' => '', 'estimated_yield' => '', 'status' => 'planejada', 'notes' => ''];

    protected function resetForm(): void
    {
        $this->form = ['property_id' => '', 'field_id' => '', 'crop_id' => '', 'season_id' => '', 'planted_at' => '', 'harvest_forecast_at' => '', 'planted_area' => '', 'seed_quantity' => '', 'estimated_yield' => '', 'status' => 'planejada', 'notes' => ''];
    }

    protected function fillForm(int $id): void
    {
        $p = Planting::findOrFail($id);
        $this->form = ['property_id' => $p->property_id, 'field_id' => $p->field_id, 'crop_id' => $p->crop_id, 'season_id' => $p->season_id, 'planted_at' => $p->planted_at?->format('Y-m-d'), 'harvest_forecast_at' => $p->harvest_forecast_at?->format('Y-m-d'), 'planted_area' => $p->planted_area, 'seed_quantity' => $p->seed_quantity, 'estimated_yield' => $p->estimated_yield, 'status' => $p->status, 'notes' => $p->notes];
    }

    public function save(): void
    {
        $this->validate(['form.property_id' => 'required|exists:properties,id', 'form.field_id' => 'required|exists:fields,id', 'form.crop_id' => 'required|exists:crops,id', 'form.planted_area' => 'nullable|numeric|min:0']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        if ($this->editingId) {
            $p = Planting::findOrFail($this->editingId);
            $p->update($data);
            AuditService::log('planting.update', $p, "Plantação #{$p->id} atualizada.");
            $this->notify('Plantação atualizada.');
        } else {
            $p = Planting::create($data);
            AuditService::log('planting.create', $p, "Plantação #{$p->id} criada.");
            $this->notify('Plantação criada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Planting::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Plantação excluída.');
    }

    public function render()
    {
        $items = Planting::with(['crop', 'field', 'property', 'season'])
            ->when($this->search, fn ($q) => $q->whereHas('crop', fn ($w) => $w->where('name', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.plantings.index', [
            'items' => $items,
            'properties' => Property::orderBy('name')->get(),
            'fields' => Field::orderBy('name')->get(),
            'crops' => Crop::orderBy('name')->get(),
            'seasons' => Season::orderByDesc('id')->get(),
        ]);
    }
}
