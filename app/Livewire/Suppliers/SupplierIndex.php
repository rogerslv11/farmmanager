<?php

namespace App\Livewire\Suppliers;

use App\Livewire\CrudIndex;
use App\Models\Supplier;
use Livewire\WithPagination;

class SupplierIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['company_name' => '', 'trade_name' => '', 'document' => '', 'phone' => '', 'email' => '', 'address' => '', 'category' => '', 'notes' => '', 'status' => 'ativo'];

    protected function resetForm(): void { $this->form = ['company_name' => '', 'trade_name' => '', 'document' => '', 'phone' => '', 'email' => '', 'address' => '', 'category' => '', 'notes' => '', 'status' => 'ativo']; }
    protected function fillForm(int $id): void { $this->form = Supplier::findOrFail($id)->only(array_keys($this->form)); }

    public function save(): void
    {
        $this->validate(['form.company_name' => 'required|max:255', 'form.email' => 'nullable|email']);
        $data = $this->form;
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Supplier::findOrFail($this->editingId)->update($data) : Supplier::create($data);
        $this->showModal = false;
        $this->notify('Fornecedor salvo.');
    }

    public function delete(): void
    {
        Supplier::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Fornecedor excluído.');
    }

    public function render()
    {
        $items = Supplier::withCount(['items', 'purchases'])
            ->when($this->search, fn ($q) => $q->where('company_name', 'like', "%{$this->search}%")->orWhere('trade_name', 'like', "%{$this->search}%"))
            ->orderBy('company_name')->paginate($this->perPage);

        return view('livewire.suppliers.index', compact('items'));
    }
}
