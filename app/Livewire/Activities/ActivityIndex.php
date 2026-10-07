<?php

namespace App\Livewire\Activities;

use App\Livewire\CrudIndex;
use App\Models\AgriculturalActivity;
use App\Models\Employee;
use App\Models\Field;
use App\Models\Planting;
use App\Models\Property;
use App\Services\AuditService;
use Livewire\WithPagination;

class ActivityIndex extends CrudIndex
{
    use WithPagination;

    public string $view = 'list';
    public array $form = ['title' => '', 'activity_type' => 'plantio', 'property_id' => '', 'field_id' => '', 'planting_id' => '', 'responsible_id' => '', 'scheduled_at' => '', 'duration_hours' => '', 'cost' => '', 'status' => 'pendente', 'description' => ''];
    public array $selectedEmployees = [];

    protected function resetForm(): void
    {
        $this->form = ['title' => '', 'activity_type' => 'plantio', 'property_id' => '', 'field_id' => '', 'planting_id' => '', 'responsible_id' => '', 'scheduled_at' => '', 'duration_hours' => '', 'cost' => '', 'status' => 'pendente', 'description' => ''];
        $this->selectedEmployees = [];
    }

    protected function fillForm(int $id): void
    {
        $a = AgriculturalActivity::with('employees')->findOrFail($id);
        $this->form = ['title' => $a->title, 'activity_type' => $a->activity_type, 'property_id' => $a->property_id, 'field_id' => $a->field_id, 'planting_id' => $a->planting_id, 'responsible_id' => $a->responsible_id, 'scheduled_at' => $a->scheduled_at?->format('Y-m-d'), 'duration_hours' => $a->duration_hours, 'cost' => $a->cost, 'status' => $a->status, 'description' => $a->description];
        $this->selectedEmployees = $a->employees->pluck('id')->toArray();
    }

    public function save(): void
    {
        $this->validate(['form.title' => 'required|max:255', 'form.scheduled_at' => 'nullable|date']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        if ($this->editingId) {
            $a = AgriculturalActivity::findOrFail($this->editingId);
            $a->update($data);
            $a->employees()->sync($this->selectedEmployees);
            AuditService::log('activity.update', $a, "Atividade \"{$a->title}\" atualizada.");
            $this->notify('Atividade atualizada.');
        } else {
            $a = AgriculturalActivity::create($data);
            $a->employees()->sync($this->selectedEmployees);
            AuditService::log('activity.create', $a, "Atividade \"{$a->title}\" criada.");
            $this->notify('Atividade criada.');
        }
        $this->showModal = false;
    }

    public function markDone(int $id): void
    {
        $a = AgriculturalActivity::findOrFail($id);
        $a->update(['status' => 'concluida']);
        $this->notify('Atividade concluída.');
    }

    public function delete(): void
    {
        AgriculturalActivity::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Atividade excluída.');
    }

    public function render()
    {
        $items = AgriculturalActivity::with(['property', 'field', 'responsible'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('scheduled_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.activities.index', [
            'items' => $items,
            'properties' => Property::orderBy('name')->get(),
            'fields' => Field::orderBy('name')->get(),
            'plantings' => Planting::with('crop')->orderByDesc('id')->limit(200)->get(),
            'employees' => Employee::active()->orderBy('name')->get(),
        ]);
    }
}
