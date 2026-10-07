<?php

namespace App\Livewire\Purchases;

use App\Models\InventoryItem;
use App\Models\Property;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\PurchaseService;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showModal = false;
    public bool $showDetail = false;
    public ?int $detailId = null;
    public array $form = ['supplier_id' => '', 'property_id' => '', 'ordered_at' => '', 'expected_at' => '', 'discount' => '0', 'taxes' => '0', 'notes' => ''];
    public array $lines = [];

    public function create(): void
    {
        $this->form = ['supplier_id' => '', 'property_id' => '', 'ordered_at' => now()->format('Y-m-d'), 'expected_at' => '', 'discount' => '0', 'taxes' => '0', 'notes' => ''];
        $this->lines = [];
        $this->showModal = true;
    }

    public function addLine(): void { $this->lines[] = ['inventory_item_id' => '', 'quantity' => '1', 'unit' => 'un', 'unit_price' => '', 'discount' => '0']; }
    public function removeLine(int $i): void { unset($this->lines[$i]); $this->lines = array_values($this->lines); }

    public function save(): void
    {
        $this->validate(['form.supplier_id' => 'required|exists:suppliers,id', 'lines' => 'required|array|min:1', 'lines.*.inventory_item_id' => 'required|exists:inventory_items,id', 'lines.*.quantity' => 'required|numeric|min:0.001', 'lines.*.unit_price' => 'required|numeric|min:0']);
        $purchase = Purchase::create([
            'organization_id' => auth()->user()->organization_id,
            'supplier_id' => $this->form['supplier_id'],
            'property_id' => $this->form['property_id'] ?: null,
            'number' => 'PED-'.now()->format('YmdHis'),
            'ordered_at' => $this->form['ordered_at'] ?: now(),
            'expected_at' => $this->form['expected_at'] ?: null,
            'discount' => $this->form['discount'] ?? 0,
            'taxes' => $this->form['taxes'] ?? 0,
            'status' => 'solicitada',
            'notes' => $this->form['notes'] ?? null,
        ]);
        $service = app(PurchaseService::class);
        foreach ($this->lines as $line) {
            $service->addItem($purchase, $line);
        }
        AuditService::log('purchase.create', $purchase, "Pedido {$purchase->number} criado.");
        $this->showModal = false;
        $this->dispatch('notify', message: 'Pedido criado.', type: 'success');
    }

    public function detail(int $id): void { $this->detailId = $id; $this->showDetail = true; }

    public function approve(int $id): void
    {
        $p = Purchase::findOrFail($id);
        $p->update(['status' => 'aprovada']);
        $this->dispatch('notify', message: 'Pedido aprovado.', type: 'success');
    }

    public function receive(int $id, PurchaseService $service): void
    {
        $service->receive(Purchase::findOrFail($id));
        $this->dispatch('notify', message: 'Compra recebida — estoque atualizado.', type: 'success');
    }

    public function cancel(int $id): void
    {
        Purchase::findOrFail($id)->update(['status' => 'cancelada']);
        $this->dispatch('notify', message: 'Pedido cancelado.', type: 'info');
    }

    public function render()
    {
        $items = Purchase::with(['supplier', 'items'])
            ->when($this->search, fn ($q) => $q->where('number', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('id')->paginate(12);

        $detail = $this->detailId ? Purchase::with(['supplier', 'items.item'])->find($this->detailId) : null;

        return view('livewire.purchases.index', [
            'items' => $items, 'detail' => $detail,
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'properties' => Property::orderBy('name')->get(),
            'products' => InventoryItem::orderBy('name')->get(),
        ]);
    }
}
