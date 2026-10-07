<?php

namespace App\Services;

use App\Enums\MovementType;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function move(InventoryItem $item, array $data): InventoryMovement
    {
        return DB::transaction(function () use ($item, $data) {
            $type = $data['type'];
            $qty = (float) $data['quantity'];

            if ($qty <= 0) {
                throw ValidationException::withMessages(['quantity' => 'Quantidade deve ser maior que zero.']);
            }

            $old = $item->current_stock;

            match ($type) {
                MovementType::Entrada->value => $item->increment('current_stock', $qty),
                MovementType::Saida->value => $this->withdraw($item, $qty),
                MovementType::Ajuste->value => $item->update(['current_stock' => $qty]),
                MovementType::Transferencia->value => $this->withdraw($item, $qty),
                default => throw ValidationException::withMessages(['type' => 'Tipo inválido.']),
            };

            $item->refresh();

            if (! empty($data['unit_cost']) && in_array($type, [MovementType::Entrada->value, MovementType::Ajuste->value])) {
                $this->updateAverageCost($item, $qty, (float) $data['unit_cost'], $type);
            }

            $movement = InventoryMovement::create([
                'organization_id' => $item->organization_id,
                'inventory_item_id' => $item->id,
                'property_id' => ! empty($data['property_id']) ? $data['property_id'] : null,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => $qty,
                'unit' => ! empty($data['unit']) ? $data['unit'] : $item->unit,
                'unit_cost' => isset($data['unit_cost']) && $data['unit_cost'] !== '' && $data['unit_cost'] !== null ? (float) $data['unit_cost'] : null,
                'moved_at' => ! empty($data['moved_at']) ? $data['moved_at'] : now()->toDateString(),
                'reason' => ! empty($data['reason']) ? $data['reason'] : null,
                'notes' => ! empty($data['notes']) ? $data['notes'] : null,
            ]);

            AuditService::log(
                'inventory.move',
                $item,
                sprintf('%s movimentou %s de "%s" (%s → %s %s)', auth()->user()?->name ?? 'Sistema', $qty, $item->name, $old, $item->current_stock, $item->unit),
                ['current_stock' => $old],
                ['current_stock' => $item->current_stock]
            );

            if ($item->fresh()->is_low_stock) {
                auth()->user()?->notify(new \App\Notifications\LowStockAlert($item->fresh()));
            }

            return $movement;
        });
    }

    protected function withdraw(InventoryItem $item, float $qty): void
    {
        if ($qty > (float) $item->current_stock) {
            throw ValidationException::withMessages(['quantity' => "Saída maior que o estoque disponível ({$item->current_stock} {$item->unit})."]);
        }
        $item->decrement('current_stock', $qty);
    }

    protected function updateAverageCost(InventoryItem $item, float $qty, float $unitCost, string $type): void
    {
        if ($type === MovementType::Ajuste->value) {
            $item->update(['average_cost' => $unitCost]);
            return;
        }
        $totalQty = (float) $item->current_stock;
        $prevQty = $totalQty - $qty;
        $prevCost = (float) $item->average_cost;
        $avg = $totalQty > 0 ? (($prevQty * $prevCost) + ($qty * $unitCost)) / $totalQty : $unitCost;
        $item->update(['average_cost' => round($avg, 2)]);
    }
}
