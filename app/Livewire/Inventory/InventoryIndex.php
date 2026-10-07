<?php

namespace App\Livewire\Inventory;

use App\Livewire\CrudIndex;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Property;
use App\Models\Supplier;
use App\Services\InventoryService;
use Livewire\WithPagination;

class InventoryIndex extends CrudIndex
{
    use WithPagination;

    public bool $showMove = false;
    public ?int $moveItemId = null;
    public array $move = ['type' => 'entrada', 'quantity' => '', 'unit_cost' => '', 'moved_at' => '', 'reason' => '', 'property_id' => ''];
    public ?int $historyItemId = null;

    public array $form = ['name' => '', 'sku' => '', 'inventory_category_id' => '', 'supplier_id' => '', 'unit' => 'un', 'current_stock' => '0', 'minimum_stock' => '0', 'average_cost' => '0', 'location' => '', 'status' => 'ativo'];

    protected function resetForm(): void
    {
        $this->form = ['name' => '', 'sku' => '', 'inventory_category_id' => '', 'supplier_id' => '', 'unit' => 'un', 'current_stock' => '0', 'minimum_stock' => '0', 'average_cost' => '0', 'location' => '', 'status' => 'ativo'];
    }

    protected function fillForm(int $id): void
    {
        $i = InventoryItem::findOrFail($id);
        $this->form = ['name' => $i->name, 'sku' => $i->sku, 'inventory_category_id' => $i->inventory_category_id, 'supplier_id' => $i->supplier_id, 'unit' => $i->unit, 'current_stock' => $i->current_stock, 'minimum_stock' => $i->minimum_stock, 'average_cost' => $i->average_cost, 'location' => $i->location, 'status' => $i->status];
    }

    public function save(): void
    {
        $this->validate(['form.name' => 'required|max:255', 'form.unit' => 'required|max:10']);
        $data = $this->form;
        $data['organization_id'] = auth()->user()->organization_id;
        $this->editingId ? InventoryItem::findOrFail($this->editingId)->update($data) : InventoryItem::create($data);
        $this->showModal = false;
        $this->notify('Produto salvo.');
    }

    public function openMove(int $id): void
    {
        $this->moveItemId = $id;
        $this->move = ['type' => 'entrada', 'quantity' => '', 'unit_cost' => '', 'moved_at' => now()->format('Y-m-d'), 'reason' => '', 'property_id' => ''];
        $this->showMove = true;
    }

    public function saveMove(InventoryService $service): void
    {
        $this->validate(['move.type' => 'required|in:entrada,saida,ajuste,transferencia', 'move.quantity' => 'required|numeric|min:0.001', 'move.moved_at' => 'required|date']);
        try {
            $service->move(InventoryItem::findOrFail($this->moveItemId), $this->move);
            $this->showMove = false;
            $this->notify('Movimentação registrada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('move.quantity', $e->errors()['quantity'][0] ?? 'Erro na movimentação.');
        }
    }

    public function openHistory(int $id): void { $this->historyItemId = $id; }

    public function delete(): void
    {
        InventoryItem::findOrFail($this->deletingId)->delete();
        $this->showDelete = false;
        $this->notify('Produto excluído.');
    }

    public function render()
    {
        $items = InventoryItem::with(['category', 'supplier'])
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->statusFilter === 'baixo', fn ($q) => $q->lowStock())
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $history = $this->historyItemId
            ? InventoryMovement::where('inventory_item_id', $this->historyItemId)->with('user')->latest()->limit(30)->get()
            : collect();

        return view('livewire.inventory.index', [
            'items' => $items,
            'history' => $history,
            'categories' => InventoryCategory::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'properties' => Property::orderBy('name')->get(),
        ]);
    }
}
