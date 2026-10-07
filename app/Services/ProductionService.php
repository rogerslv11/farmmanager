<?php

namespace App\Services;

use App\Models\Harvest;
use App\Models\Planting;
use App\Models\Production;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function recordProduction(array $data): Production
    {
        return DB::transaction(function () use ($data) {
            $production = Production::create($data);

            if (! empty($data['planting_id'])) {
                $planting = Planting::find($data['planting_id']);
                if ($planting) {
                    $total = Production::where('planting_id', $planting->id)->sum('quantity');
                    $planting->update(['realized_yield' => $total]);
                    $planting->season?->update([
                        'realized_production' => Production::where('season_id', $planting->season_id)->sum('quantity'),
                    ]);
                }
            }

            AuditService::log('production.record', $production, "Produção de {$production->quantity} {$production->unit} registrada.");

            return $production;
        });
    }

    public function recordHarvest(array $data): Harvest
    {
        return DB::transaction(function () use ($data) {
            $harvest = Harvest::create($data);

            $planting = Planting::find($data['planting_id']);
            if ($planting) {
                $total = Harvest::where('planting_id', $planting->id)->sum('quantity');
                $planting->update(['realized_yield' => $total, 'harvested_at' => $data['harvested_at'] ?? now()]);
                $this->recordProduction([
                    'organization_id' => $harvest->organization_id,
                    'crop_id' => $harvest->crop_id,
                    'season_id' => $planting->season_id,
                    'field_id' => $harvest->field_id,
                    'planting_id' => $planting->id,
                    'produced_at' => $data['harvested_at'] ?? now()->toDateString(),
                    'quantity' => $data['quantity'],
                    'unit' => $data['unit'] ?? 'sc',
                    'quality' => $data['quality'] ?? null,
                    'batch' => 'COL-'.$harvest->id,
                    'operator' => $data['equipment_used'] ?? null,
                ]);
            }

            AuditService::log('harvest.record', $harvest, "Colheita de {$harvest->quantity} {$harvest->unit} registrada.");

            return $harvest;
        });
    }

    public function productivity(float $production, float $area): float
    {
        return $area > 0 ? round($production / $area, 2) : 0;
    }
}
