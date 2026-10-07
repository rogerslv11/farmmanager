<?php

namespace App\Livewire\Customers;

use App\Livewire\CrudIndex;
use App\Models\Customer;
use Livewire\WithPagination;

class CustomerIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['name' => '', 'document' => '', 'email' => '', 'phone' => '', 'address' => '', 'customer_type' => 'pf', 'notes' => ''];

    protected function resetForm(): void { $this->form = ['name' => '', 'document' => '', 'email' => '', 'phone' => '', 'address' => '', 'customer_type' => 'pf', 'notes' => '']; }
    protected function fillForm(int $id): void { $this->form = Customer::findOrFail($id)->only(array_keys($this->form)); }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255']);
        $data = $this->form;
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Customer::findOrFail($this->editingId)->update($data) : Customer::create($data);
        $this->showModal = false;
        $this->notify('Cliente salvo.');
    }

    public function delete(): void
    {
        Customer::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Cliente excluído.');
    }

    public function render()
    {
        $items = Customer::withSum('sales as revenue', 'total')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate($this->perPage);

        return view('livewire.customers.index', compact('items'));
    }
}
