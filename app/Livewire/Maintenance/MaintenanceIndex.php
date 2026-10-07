<?php

namespace App\Livewire\Maintenance;

use App\Livewire\CrudIndex;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use Livewire\WithPagination;

class MaintenanceIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['equipment_id' => '', 'maintenance_type' => 'preventiva', 'performed_at' => '', 'next_at' => '', 'responsible_id' => '', 'cost' => '', 'parts_used' => '', 'description' => '', 'status' => 'concluida'];

    protected function resetForm(): void { $this->form = ['equipment_id' => '', 'maintenance_type' => 'preventiva', 'performed_at' => now()->format('Y-m-d'), 'next_at' => '', 'responsible_id' => '', 'cost' => '', 'parts_used' => '', 'description' => '', 'status' => 'concluida']; }
    protected function fillForm(int $id): void
    {
        $m = MaintenanceRecord::findOrFail($id);
        $this->form = ['equipment_id' => $m->equipment_id, 'maintenance_type' => $m->maintenance_type, 'performed_at' => $m->performed_at?->format('Y-m-d'), 'next_at' => $m->next_at?->format('Y-m-d'), 'responsible_id' => $m->responsible_id, 'cost' => $m->cost, 'parts_used' => $m->parts_used, 'description' => $m->description, 'status' => $m->status];
    }

    public function save(): void
    {
        $this->validate(['form.equipment_id' => 'required|exists:equipment,id', 'form.performed_at' => 'required|date']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? MaintenanceRecord::findOrFail($this->editingId)->update($data) : MaintenanceRecord::create($data);
        $this->showModal = false;
        $this->notify('Manutenção registrada.');
    }

    public function delete(): void
    {
        MaintenanceRecord::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Registro excluído.');
    }

    public function render()
    {
        $items = MaintenanceRecord::with(['equipment', 'responsible'])
            ->when($this->statusFilter === 'atrasada', fn ($q) => $q->overdue())
            ->orderBy('performed_at', 'desc')->paginate($this->perPage);

        return view('livewire.maintenance.index', ['items' => $items, 'equipment' => Equipment::orderBy('name')->get(), 'employees' => Employee::active()->orderBy('name')->get()]);
    }
}
