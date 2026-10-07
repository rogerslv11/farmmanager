<?php

namespace App\Livewire\Employees;

use App\Livewire\CrudIndex;
use App\Models\Employee;
use App\Models\Property;
use Livewire\WithPagination;

class EmployeeIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['name' => '', 'document' => '', 'role_title' => '', 'phone' => '', 'email' => '', 'property_id' => '', 'hired_at' => '', 'salary' => '', 'status' => 'ativo', 'notes' => ''];

    protected function resetForm(): void { $this->form = ['name' => '', 'document' => '', 'role_title' => '', 'phone' => '', 'email' => '', 'property_id' => '', 'hired_at' => '', 'salary' => '', 'status' => 'ativo', 'notes' => '']; }
    protected function fillForm(int $id): void
    {
        $e = Employee::findOrFail($id);
        $this->form = ['name' => $e->name, 'document' => $e->document, 'role_title' => $e->role_title, 'phone' => $e->phone, 'email' => $e->email, 'property_id' => $e->property_id, 'hired_at' => $e->hired_at?->format('Y-m-d'), 'salary' => $e->salary, 'status' => $e->status, 'notes' => $e->notes];
    }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255', 'form.email' => 'nullable|email']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Employee::findOrFail($this->editingId)->update($data) : Employee::create($data);
        $this->showModal = false;
        $this->notify('Funcionário salvo.');
    }

    public function delete(): void
    {
        Employee::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Funcionário excluído.');
    }

    public function render()
    {
        $items = Employee::with('property')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('name')->paginate($this->perPage);

        return view('livewire.employees.index', ['items' => $items, 'properties' => Property::orderBy('name')->get()]);
    }
}
