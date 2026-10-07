<?php

namespace App\Services;

use App\Models\AgriculturalActivity;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\MaintenanceRecord;
use App\Models\Planting;
use App\Models\Production;
use App\Models\Property;
use App\Models\Sale;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function indicators(int $orgId): array
    {
        return [
            'properties' => Property::where('organization_id', $orgId)->count(),
            'total_area' => (float) Property::where('organization_id', $orgId)->sum('total_area'),
            'active_fields' => \App\Models\Field::where('organization_id', $orgId)->where('status', 'ativo')->count(),
            'active_plantings' => Planting::where('organization_id', $orgId)->active()->count(),
            'active_seasons' => Season::where('organization_id', $orgId)->active()->count(),
            'estimated_production' => (float) Season::where('organization_id', $orgId)->whereIn('status', ['em_andamento', 'colheita'])->sum('estimated_production'),
            'realized_production' => (float) Production::where('organization_id', $orgId)->sum('quantity'),
            'stock_items' => InventoryItem::where('organization_id', $orgId)->count(),
            'stock_value' => (float) InventoryItem::where('organization_id', $orgId)->get()->sum(fn ($i) => $i->stock_value),
            'season_cost' => (float) Expense::where('organization_id', $orgId)->sum('amount'),
            'estimated_revenue' => (float) Sale::where('organization_id', $orgId)->whereNotIn('status', ['cancelada'])->sum('total'),
            'active_employees' => \App\Models\Employee::where('organization_id', $orgId)->where('status', 'ativo')->count(),
        ];
    }

    public function productionByCrop(int $orgId): array
    {
        return Production::where('organization_id', $orgId)
            ->select('crop_id', DB::raw('SUM(quantity) as total'))
            ->with('crop:id,name')
            ->groupBy('crop_id')->get()
            ->map(fn ($r) => ['label' => $r->crop?->name ?? '?', 'total' => (float) $r->total])->toArray();
    }

    public function monthlyExpenses(int $orgId): array
    {
        $series = $this->monthlySeries($orgId);
        return ['labels' => $series['labels'], 'totals' => $series['expenses']];
    }

    public function monthlyRevenue(int $orgId): array
    {
        $series = $this->monthlySeries($orgId);
        return ['labels' => $series['labels'], 'totals' => $series['revenue']];
    }

    /** Séries mensais dos últimos 12 meses, agrupadas em PHP (compatível SQLite e MySQL). */
    public function monthlySeries(int $orgId): array
    {
        $months = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));

        $expenses = Expense::where('organization_id', $orgId)
            ->where('incurred_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['incurred_at', 'amount'])
            ->groupBy(fn ($e) => $e->incurred_at?->format('Y-m'));

        $sales = Sale::where('organization_id', $orgId)
            ->where('sold_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['sold_at', 'total'])
            ->groupBy(fn ($s) => $s->sold_at?->format('Y-m'));

        return [
            'labels' => $months->map(fn ($m) => ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M/y')))->toArray(),
            'keys' => $months->toArray(),
            'expenses' => $months->map(fn ($m) => round((float) ($expenses->get($m)?->sum('amount') ?? 0), 2))->toArray(),
            'revenue' => $months->map(fn ($m) => round((float) ($sales->get($m)?->sum('total') ?? 0), 2))->toArray(),
        ];
    }

    /** Variação do mês atual vs. mês anterior (receita, despesas, produção). */
    public function deltas(int $orgId): array
    {
        $series = $this->monthlySeries($orgId);
        $cur = count($series['keys']) - 1;
        $prev = $cur - 1;

        $revCur = $series['revenue'][$cur] ?? 0;
        $revPrev = $series['revenue'][$prev] ?? 0;
        $expCur = $series['expenses'][$cur] ?? 0;
        $expPrev = $series['expenses'][$prev] ?? 0;

        $prodCur = (float) Production::where('organization_id', $orgId)
            ->whereBetween('produced_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('quantity');
        $prodPrev = (float) Production::where('organization_id', $orgId)
            ->whereBetween('produced_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('quantity');

        $pct = fn ($c, $p) => $p > 0 ? round(($c - $p) / $p * 100, 1) : ($c > 0 ? 100.0 : 0.0);

        return [
            'revenue' => ['pct' => $pct($revCur, $revPrev), 'good' => $revCur >= $revPrev],
            'expenses' => ['pct' => $pct($expCur, $expPrev), 'good' => $expCur <= $expPrev],
            'production' => ['pct' => $pct($prodCur, $prodPrev), 'good' => $prodCur >= $prodPrev],
        ];
    }

    /** Safra em andamento com maior área + progresso, produção e custos. */
    public function featuredSeason(int $orgId): ?array
    {
        $season = Season::where('organization_id', $orgId)
            ->whereIn('status', ['em_andamento', 'colheita'])
            ->with(['crop', 'property'])
            ->withSum('productions as production_total', 'quantity')
            ->withSum('expenses as cost_total', 'amount')
            ->withCount(['activities as activities_total'])
            ->orderByDesc('total_area')
            ->first();

        if (! $season) {
            return null;
        }

        $done = $season->activities()->where('status', 'concluida')->count();
        $production = (float) ($season->production_total ?? 0);
        $estimated = (float) ($season->estimated_production ?? 0);

        return [
            'id' => $season->id,
            'name' => $season->name,
            'crop' => $season->crop?->name,
            'property' => $season->property?->name,
            'status' => $season->status,
            'progress' => $season->progress,
            'area' => (float) $season->total_area,
            'production' => $production,
            'estimated' => $estimated,
            'harvest_pct' => $estimated > 0 ? round(min(100, $production / $estimated * 100), 1) : 0,
            'cost' => (float) ($season->cost_total ?? 0),
            'activities_done' => $done,
            'activities_total' => (int) ($season->activities_total ?? 0),
        ];
    }

    /** Produtividade (produção/área) por cultura a partir das plantações. */
    public function productivityByCrop(int $orgId): array
    {
        return Planting::where('organization_id', $orgId)
            ->where('planted_area', '>', 0)
            ->select('crop_id', DB::raw('SUM(realized_yield) as y'), DB::raw('SUM(planted_area) as a'))
            ->with('crop:id,name')
            ->groupBy('crop_id')->get()
            ->map(fn ($r) => [
                'label' => $r->crop?->name ?? '?',
                'value' => $r->a > 0 ? round($r->y / $r->a, 1) : 0,
            ])
            ->sortByDesc('value')->take(6)->values()->toArray();
    }

    /** Agenda: atrasadas + hoje + próximos 7 dias. */
    public function agenda(int $orgId, int $limit = 7)
    {
        return AgriculturalActivity::where('organization_id', $orgId)
            ->whereIn('status', ['pendente', 'agendada', 'em_andamento'])
            ->whereDate('scheduled_at', '<=', now()->addDays(7))
            ->with(['property', 'field', 'responsible'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
    }

    /** Itens em estado mais crítico de estoque. */
    public function criticalStock(int $orgId, int $limit = 5)
    {
        return InventoryItem::where('organization_id', $orgId)
            ->lowStock()
            ->with('category')
            ->orderByRaw('current_stock - minimum_stock')
            ->limit($limit)
            ->get();
    }

    public function alerts(int $orgId): array
    {
        return [
            'low_stock' => InventoryItem::where('organization_id', $orgId)->lowStock()->count(),
            'overdue_activities' => AgriculturalActivity::where('organization_id', $orgId)->overdue()->count(),
            'overdue_maintenance' => MaintenanceRecord::where('organization_id', $orgId)->overdue()->count(),
            'near_harvest' => Planting::where('organization_id', $orgId)->where('status', 'pronta_colheita')->count(),
            'maintenance_soon' => MaintenanceRecord::where('organization_id', $orgId)->upcoming()->count(),
        ];
    }

    public function recent(int $orgId, int $limit = 8): array
    {
        return [
            'plantings' => Planting::where('organization_id', $orgId)->with(['crop', 'field'])->latest()->limit($limit)->get(),
            'movements' => \App\Models\InventoryMovement::where('organization_id', $orgId)->with('item')->latest()->limit($limit)->get(),
            'sales' => Sale::where('organization_id', $orgId)->with('customer')->latest()->limit($limit)->get(),
            'activities' => AgriculturalActivity::where('organization_id', $orgId)->latest()->limit($limit)->get(),
            'maintenances' => MaintenanceRecord::where('organization_id', $orgId)->with('equipment')->latest()->limit($limit)->get(),
        ];
    }

    public function costBySeason(int $orgId): array
    {
        return Expense::where('organization_id', $orgId)
            ->select('season_id', DB::raw('SUM(amount) as total'))
            ->with('season:id,name')
            ->groupBy('season_id')->get()
            ->map(fn ($r) => ['label' => $r->season?->name ?? 'Sem safra', 'total' => (float) $r->total])->toArray();
    }
}
