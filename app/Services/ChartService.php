<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\Expense;
use App\Models\Field;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\MaintenanceRecord;
use App\Models\Planting;
use App\Models\Production;
use App\Models\Property;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;

/** Agregações prontas para gráficos (labels + values), sempre no escopo da organização logada. */
class ChartService
{
    protected function months(int $n = 11): array
    {
        return collect(range($n, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'))->toArray();
    }

    protected function monthLabel(string $ym): string
    {
        return ucfirst(Carbon::createFromFormat('Y-m', $ym)->translatedFormat('M/y'));
    }

    public function stockValueByCategory(): array
    {
        $g = InventoryItem::with('category')->get()
            ->groupBy(fn ($i) => $i->category?->name ?? 'Sem categoria')
            ->map(fn ($items) => round($items->sum(fn ($i) => $i->stock_value), 2))
            ->sortDesc();

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function stockHealthByCategory(): array
    {
        $g = InventoryItem::with('category')->get()
            ->groupBy(fn ($i) => $i->category?->name ?? 'Sem categoria')
            ->map(fn ($items) => [
                'total' => $items->count(),
                'low' => $items->filter(fn ($i) => $i->is_low_stock)->count(),
                'value' => round($items->sum(fn ($i) => $i->stock_value), 2),
            ]);

        return [
            'labels' => $g->keys()->toArray(),
            'total' => $g->pluck('total')->toArray(),
            'low' => $g->pluck('low')->toArray(),
            'value' => $g->pluck('value')->toArray(),
        ];
    }

    public function movementsInOut30d(): array
    {
        $moves = InventoryMovement::where('moved_at', '>=', now()->subDays(30))->get()
            ->groupBy('type')
            ->map(fn ($g) => round($g->sum('quantity'), 2));

        $labels = ['entrada' => 'Entradas', 'saida' => 'Saídas', 'ajuste' => 'Ajustes', 'transferencia' => 'Transferências'];

        return [
            'labels' => collect($labels)->filter(fn ($l, $k) => ($moves[$k] ?? 0) > 0)->values()->toArray(),
            'values' => collect($labels)->keys()->filter(fn ($k) => ($moves[$k] ?? 0) > 0)->map(fn ($k) => $moves[$k])->values()->toArray(),
        ];
    }

    public function movementsDaily30d(): array
    {
        $days = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));
        $moves = InventoryMovement::where('moved_at', '>=', now()->subDays(30))->get();

        $entries = $moves->where('type', 'entrada')->groupBy(fn ($m) => $m->moved_at?->format('Y-m-d'))->map(fn ($g) => round($g->sum('quantity'), 2));
        $exits = $moves->where('type', 'saida')->groupBy(fn ($m) => $m->moved_at?->format('Y-m-d'))->map(fn ($g) => round($g->sum('quantity'), 2));

        return [
            'labels' => $days->map(fn ($d) => Carbon::parse($d)->format('d/m'))->toArray(),
            'entradas' => $days->map(fn ($d) => $entries[$d] ?? 0)->toArray(),
            'saidas' => $days->map(fn ($d) => $exits[$d] ?? 0)->toArray(),
        ];
    }

    public function salesByMonth(int $n = 11): array
    {
        $months = $this->months($n);
        $rows = Sale::where('sold_at', '>=', now()->subMonths($n)->startOfMonth())->get()
            ->groupBy(fn ($s) => $s->sold_at?->format('Y-m'));

        return [
            'labels' => collect($months)->map(fn ($m) => $this->monthLabel($m))->toArray(),
            'values' => collect($months)->map(fn ($m) => round($rows->get($m)?->sum('total') ?? 0, 2))->toArray(),
        ];
    }

    public function salesByCrop(): array
    {
        $g = Sale::with('crop:id,name')->get()
            ->groupBy(fn ($s) => $s->crop?->name ?? '—')
            ->map(fn ($items) => round($items->sum('total'), 2))->sortDesc()->take(8);

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function topCustomers(int $limit = 5): array
    {
        $g = Sale::with('customer:id,name')->get()
            ->groupBy(fn ($s) => $s->customer?->name ?? '—')
            ->map(fn ($items) => round($items->sum('total'), 2))->sortDesc()->take($limit);

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function expensesByCategory(): array
    {
        $g = Expense::all()->groupBy('category')
            ->map(fn ($items) => round($items->sum('amount'), 2))->sortDesc();

        return [
            'labels' => $g->keys()->map(fn ($c) => ucfirst(str_replace('_', ' ', $c)))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function expensesMonthly(int $n = 11): array
    {
        $months = $this->months($n);
        $rows = Expense::where('incurred_at', '>=', now()->subMonths($n)->startOfMonth())->get()
            ->groupBy(fn ($e) => $e->incurred_at?->format('Y-m'));

        return [
            'labels' => collect($months)->map(fn ($m) => $this->monthLabel($m))->toArray(),
            'values' => collect($months)->map(fn ($m) => round($rows->get($m)?->sum('amount') ?? 0, 2))->toArray(),
        ];
    }

    public function productionMonthly(int $n = 11): array
    {
        $months = $this->months($n);
        $rows = Production::where('produced_at', '>=', now()->subMonths($n)->startOfMonth())->get()
            ->groupBy(fn ($p) => $p->produced_at?->format('Y-m'));

        return [
            'labels' => collect($months)->map(fn ($m) => $this->monthLabel($m))->toArray(),
            'values' => collect($months)->map(fn ($m) => round($rows->get($m)?->sum('quantity') ?? 0, 2))->toArray(),
        ];
    }

    public function productionByCrop(): array
    {
        $g = Production::with('crop:id,name')->get()
            ->groupBy(fn ($p) => $p->crop?->name ?? '—')
            ->map(fn ($items) => round($items->sum('quantity'), 2))->sortDesc()->take(8);

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function productionByQuality(): array
    {
        $g = Production::all()->groupBy(fn ($p) => $p->quality ?? 'não classificado')
            ->map(fn ($items) => round($items->sum('quantity'), 2))->sortDesc();

        return ['labels' => $g->keys()->map(fn ($q) => ucfirst($q))->toArray(), 'values' => $g->values()->toArray()];
    }

    public function maintenanceCostByEquipment(int $limit = 6): array
    {
        $g = MaintenanceRecord::with('equipment:id,name')->get()
            ->groupBy(fn ($m) => $m->equipment?->name ?? '—')
            ->map(fn ($items) => round($items->sum('cost'), 2))->sortDesc()->take($limit);

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function maintenanceByType(): array
    {
        $g = MaintenanceRecord::all()->groupBy('maintenance_type')
            ->map(fn ($items) => $items->count())->sortDesc();

        $map = ['preventiva' => 'Preventiva', 'corretiva' => 'Corretiva'];
        return [
            'labels' => $g->keys()->map(fn ($k) => $map[$k] ?? ucfirst($k))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function propertiesByArea(): array
    {
        $props = Property::all()->sortByDesc('total_area')->take(8);
        return [
            'labels' => $props->pluck('name')->toArray(),
            'values' => $props->pluck('total_area')->map(fn ($v) => (float) $v)->toArray(),
        ];
    }

    public function propertiesByType(): array
    {
        $g = Property::all()->groupBy('property_type')
            ->map(fn ($items) => $items->count());

        return [
            'labels' => $g->keys()->map(fn ($t) => ucfirst($t))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function fieldsByStatus(): array
    {
        $g = Field::all()->groupBy('status')
            ->map(fn ($items) => $items->count());

        $map = ['ativo' => 'Ativo', 'descanso' => 'Descanso', 'inativo' => 'Inativo'];
        return [
            'labels' => $g->keys()->map(fn ($k) => $map[$k] ?? ucfirst($k))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function fieldsByIrrigation(): array
    {
        $g = Field::all()->groupBy(fn ($f) => $f->irrigation ?? 'não definido')
            ->map(fn ($items) => $items->count());

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function plantingsByStatus(): array
    {
        $g = Planting::all()->groupBy('status')
            ->map(fn ($items) => $items->count());

        $map = [
            'planejada' => 'Planejada',
            'preparacao' => 'Preparação',
            'plantada' => 'Plantada',
            'crescimento' => 'Em crescimento',
            'pronta_colheita' => 'Pronta p/ colheita',
            'colhida' => 'Colhida',
            'encerrada' => 'Encerrada',
        ];
        return [
            'labels' => $g->keys()->map(fn ($k) => $map[$k] ?? ucfirst($k))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function cropsByCycle(): array
    {
        $crops = Crop::withCount('plantings')->get()->where('average_cycle_days', '>', 0)->sortByDesc('average_cycle_days')->take(8);
        return [
            'labels' => $crops->pluck('name')->toArray(),
            'cycles' => $crops->pluck('average_cycle_days')->toArray(),
            'plantings' => $crops->pluck('plantings_count')->toArray(),
        ];
    }

    public function equipmentByStatus(): array
    {
        $g = \App\Models\Equipment::all()->groupBy('status')
            ->map(fn ($items) => $items->count());

        $map = ['disponivel' => 'Disponível', 'em_uso' => 'Em uso', 'em_manutencao' => 'Em manutenção', 'inativo' => 'Inativo'];
        return [
            'labels' => $g->keys()->map(fn ($k) => $map[$k] ?? ucfirst($k))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function purchasesByStatus(): array
    {
        $g = Purchase::all()->groupBy('status')
            ->map(fn ($items) => $items->count());

        $map = ['rascunho' => 'Rascunho', 'solicitada' => 'Solicitada', 'aprovada' => 'Aprovada', 'recebida' => 'Recebida', 'cancelada' => 'Cancelada'];
        return [
            'labels' => $g->keys()->map(fn ($k) => $map[$k] ?? ucfirst($k))->toArray(),
            'values' => $g->values()->toArray(),
        ];
    }

    public function purchasesMonthly(int $n = 5): array
    {
        $months = collect(range($n, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $rows = Purchase::where('ordered_at', '>=', now()->subMonths($n)->startOfMonth())->get()
            ->groupBy(fn ($p) => $p->ordered_at?->format('Y-m'));

        return [
            'labels' => $months->map(fn ($m) => $this->monthLabel($m))->toArray(),
            'values' => $months->map(fn ($m) => round($rows->get($m)?->sum('total') ?? 0, 2))->toArray(),
        ];
    }

    public function employeesByRole(): array
    {
        $g = \App\Models\Employee::all()->groupBy(fn ($e) => $e->role_title ?? 'Não definido')
            ->map(fn ($items) => $items->count())->sortDesc()->take(6);

        return ['labels' => $g->keys()->toArray(), 'values' => $g->values()->toArray()];
    }

    public function auditActivityHeatmap(): array
    {
        // Retorna matriz [dia_semana (0-6)][hora (0-23)] = contagem
        $logs = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(60))
            ->get(['created_at']);

        $matrix = array_fill(0, 7, array_fill(0, 24, 0));
        foreach ($logs as $l) {
            $matrix[($l->created_at->dayOfWeek + 6) % 7][$l->created_at->hour]++;
        }

        // Achata em formato de lista com dia, hora, count
        $flat = [];
        foreach ($matrix as $d => $hours) {
            foreach ($hours as $h => $c) {
                $flat[] = ['d' => $d, 'h' => $h, 'c' => $c];
            }
        }
        return $flat;
    }
}