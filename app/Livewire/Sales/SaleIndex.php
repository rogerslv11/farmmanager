<?php

namespace App\Livewire\Sales;

use App\Livewire\CrudIndex;
use App\Models\Crop;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\AuditService;
use Livewire\WithPagination;

class SaleIndex extends CrudIndex
{
    use WithPagination;

    public array $form = ['customer_id' => '', 'crop_id' => '', 'batch' => '', 'quantity' => '', 'unit' => 'sc', 'unit_price' => '', 'discount' => '0', 'sold_at' => '', 'status' => 'pendente', 'notes' => ''];

    protected function resetForm(): void { $this->form = ['customer_id' => '', 'crop_id' => '', 'batch' => '', 'quantity' => '', 'unit' => 'sc', 'unit_price' => '', 'discount' => '0', 'sold_at' => now()->format('Y-m-d'), 'status' => 'pendente', 'notes' => '']; }
    protected function fillForm(int $id): void
    {
        $s = Sale::findOrFail($id);
        $this->form = ['customer_id' => $s->customer_id, 'crop_id' => $s->crop_id, 'batch' => $s->batch, 'quantity' => $s->quantity, 'unit' => $s->unit, 'unit_price' => $s->unit_price, 'discount' => $s->discount, 'sold_at' => $s->sold_at?->format('Y-m-d'), 'status' => $s->status, 'notes' => $s->notes];
    }

    public function save(): void
    {
        $this->validate(['form.customer_id' => 'required|exists:customers,id', 'form.quantity' => 'required|numeric|min:0.001', 'form.unit_price' => 'required|numeric|min:0', 'form.sold_at' => 'required|date']);
        $total = ((float) $this->form['quantity'] * (float) $this->form['unit_price']) - (float) ($this->form['discount'] ?? 0);
        $data = array_filter($this->form, fn ($v) => $v !== '');
        $data['organization_id'] = auth()->user()->organization_id;
        $data['total'] = max(0, $total);
        if ($this->editingId) {
            $s = Sale::findOrFail($this->editingId);
            $s->update($data);
            $this->notify('Venda atualizada.');
        } else {
            $s = Sale::create($data);
            AuditService::log('sale.create', $s, "Venda de R$ {$s->total} registrada.");
            $this->notify('Venda registrada.');
        }
        $this->showModal = false;
    }

    public function delete(): void
    {
        Sale::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Venda excluída.');
    }

    public function render()
    {
        $items = Sale::with(['customer', 'crop'])
            ->when($this->search, fn ($q) => $q->where('batch', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('sold_at', 'desc')->paginate($this->perPage);

        return view('livewire.sales.index', ['items' => $items, 'customers' => Customer::orderBy('name')->get(), 'crops' => Crop::orderBy('name')->get()]);
    }
}
