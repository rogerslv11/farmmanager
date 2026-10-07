<?php

namespace App\Livewire\Expenses;

use App\Livewire\CrudIndex;
use App\Models\Expense;
use App\Models\Property;
use App\Models\Season;
use Livewire\WithPagination;

class ExpenseIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['description' => '', 'category' => 'outros', 'amount' => '', 'incurred_at' => '', 'property_id' => '', 'season_id' => '', 'status' => 'pago', 'notes' => ''];

    protected function resetForm(): void { $this->form = ['description' => '', 'category' => 'outros', 'amount' => '', 'incurred_at' => now()->format('Y-m-d'), 'property_id' => '', 'season_id' => '', 'status' => 'pago', 'notes' => '']; }
    protected function fillForm(int $id): void
    {
        $e = Expense::findOrFail($id);
        $this->form = ['description' => $e->description, 'category' => $e->category, 'amount' => $e->amount, 'incurred_at' => $e->incurred_at?->format('Y-m-d'), 'property_id' => $e->property_id, 'season_id' => $e->season_id, 'status' => $e->status, 'notes' => $e->notes];
    }

    public function save(): void
    {
        $this->validate(['form.description' => 'required|max:255', 'form.amount' => 'required|numeric|min:0.01', 'form.incurred_at' => 'required|date']);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? Expense::findOrFail($this->editingId)->update($data) : Expense::create($data);
        $this->showModal = false;
        $this->notify('Custo registrado.');
    }

    public function delete(): void
    {
        Expense::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Custo excluído.');
    }

    public function render()
    {
        $items = Expense::with(['property', 'season'])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('category', $this->statusFilter))
            ->orderBy('incurred_at', 'desc')->paginate($this->perPage);
        $total = Expense::when($this->statusFilter, fn ($q) => $q->where('category', $this->statusFilter))->sum('amount');

        return view('livewire.expenses.index', ['items' => $items, 'total' => $total, 'properties' => Property::orderBy('name')->get(), 'seasons' => Season::orderByDesc('id')->get()]);
    }
}
