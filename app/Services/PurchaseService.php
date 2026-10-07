<?php

namespace App\Services;

use App\Enums\PurchaseStatus;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(protected InventoryService $inventory) {}

    public function receive(Purchase $purchase): void
    {
        if ($purchase->status === PurchaseStatus::Recebida->value) {
            return;
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $line) {
                $this->inventory->move($line->item, [
                    'type' => 'entrada',
                    'quantity' => (float) $line->quantity,
                    'unit' => $line->unit,
                    'unit_cost' => (float) $line->unit_price,
                    'moved_at' => now()->toDateString(),
                    'reason' => "Compra {$purchase->number} recebida",
                    'property_id' => $purchase->property_id,
                ]);
            }

            $purchase->update(['status' => PurchaseStatus::Recebida->value, 'received_at' => now()->toDateString()]);

            AuditService::log('purchase.receive', $purchase, "Compra {$purchase->number} recebida — estoque atualizado.");
        });
    }

    public function addItem(Purchase $purchase, array $data): void
    {
        DB::transaction(function () use ($purchase, $data) {
            $subtotal = ((float) $data['quantity'] * (float) $data['unit_price']) - (float) ($data['discount'] ?? 0);
            $purchase->items()->create([
                'inventory_item_id' => $data['inventory_item_id'],
                'quantity' => $data['quantity'],
                'unit' => $data['unit'] ?? 'un',
                'unit_price' => $data['unit_price'],
                'discount' => $data['discount'] ?? 0,
                'subtotal' => max(0, $subtotal),
            ]);
            $purchase->refresh()->recalculateTotal();
        });
    }
}
