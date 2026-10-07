<?php

namespace App\Livewire\Reports;

use App\Models\Crop;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Production;
use App\Models\Property;
use App\Models\Sale;
use App\Models\Season;
use Livewire\Component;

class ReportsIndex extends Component
{
    public string $tab = 'producao';
    public ?string $from = null;
    public ?string $to = null;
    public ?int $propertyId = null;
    public ?int $seasonId = null;

    public function updated($name, $value): void
    {
        $this->dispatch('reports-charts', payload: $this->chartPayload());
    }

    /** Dados agregados dos gráficos, respeitando os filtros ativos. */
    public function chartPayload(): array
    {
        $orgId = auth()->user()->organization_id;
        $from = $this->from ?: now()->subYear()->format('Y-m-d');
        $to = $this->to ?: now()->format('Y-m-d');

        $productions = Production::where('organization_id', $orgId)
            ->whereBetween('produced_at', [$from, $to])
            ->when($this->seasonId, fn ($q) => $q->where('season_id', $this->seasonId))
            ->with('crop:id,name')->get();

        $byCrop = $productions->groupBy(fn ($p) => $p->crop?->name ?? '—')
            ->map(fn ($g) => round($g->sum('quantity'), 2))->sortDesc();

        $byMonth = $productions->groupBy(fn ($p) => $p->produced_at?->format('Y-m'))
            ->map(fn ($g) => round($g->sum('quantity'), 2))->sortKeys();

        $expenses = Expense::where('organization_id', $orgId)
            ->whereBetween('incurred_at', [$from, $to])
            ->when($this->propertyId, fn ($q) => $q->where('property_id', $this->propertyId))
            ->when($this->seasonId, fn ($q) => $q->where('season_id', $this->seasonId))
            ->get();

        $expByCat = $expenses->groupBy('category')
            ->map(fn ($g) => round($g->sum('amount'), 2))->sortDesc();

        $sales = Sale::where('organization_id', $orgId)
            ->whereBetween('sold_at', [$from, $to])->get();

        $salesByMonth = $sales->groupBy(fn ($s) => $s->sold_at?->format('Y-m'))
            ->map(fn ($g) => round($g->sum('total'), 2))->sortKeys();

        $expByMonth = $expenses->groupBy(fn ($e) => $e->incurred_at?->format('Y-m'))
            ->map(fn ($g) => round($g->sum('amount'), 2))->sortKeys();

        $months = $byMonth->keys()->merge($salesByMonth->keys())->merge($expByMonth->keys())->unique()->sort()->values();
        $monthLabel = fn ($m) => ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M/y'));

        $stockByCat = InventoryItem::where('organization_id', $orgId)->with('category')->get()
            ->groupBy(fn ($i) => $i->category?->name ?? 'Sem categoria')
            ->map(fn ($g) => round($g->sum(fn ($i) => $i->stock_value), 2))->sortDesc();

        return [
            'prodCrop' => ['labels' => $byCrop->keys()->toArray(), 'values' => $byCrop->values()->toArray()],
            'prodMonth' => ['labels' => $byMonth->keys()->map($monthLabel)->toArray(), 'values' => $byMonth->values()->toArray()],
            'expCat' => ['labels' => $expByCat->keys()->map(fn ($c) => ucfirst(str_replace('_', ' ', $c)))->toArray(), 'values' => $expByCat->values()->toArray()],
            'cash' => [
                'labels' => $months->map($monthLabel)->toArray(),
                'revenue' => $months->map(fn ($m) => $salesByMonth[$m] ?? 0)->toArray(),
                'expenses' => $months->map(fn ($m) => $expByMonth[$m] ?? 0)->toArray(),
            ],
            'stockCat' => ['labels' => $stockByCat->keys()->toArray(), 'values' => $stockByCat->values()->toArray()],
        ];
    }

    public function render()
    {
        $orgId = auth()->user()->organization_id;
        $from = $this->from ?: now()->subYear()->format('Y-m-d');
        $to = $this->to ?: now()->format('Y-m-d');

        $production = Production::where('organization_id', $orgId)
            ->whereBetween('produced_at', [$from, $to])
            ->when($this->seasonId, fn ($q) => $q->where('season_id', $this->seasonId))
            ->with(['crop', 'season', 'field'])->orderByDesc('produced_at')->limit(200)->get();

        $stock = InventoryItem::where('organization_id', $orgId)->with('category')->orderBy('name')->get();

        $expenses = Expense::where('organization_id', $orgId)
            ->whereBetween('incurred_at', [$from, $to])
            ->when($this->propertyId, fn ($q) => $q->where('property_id', $this->propertyId))
            ->when($this->seasonId, fn ($q) => $q->where('season_id', $this->seasonId))
            ->with(['property', 'season'])->orderByDesc('incurred_at')->get();

        $sales = Sale::where('organization_id', $orgId)
            ->whereBetween('sold_at', [$from, $to])
            ->with(['customer', 'crop'])->orderByDesc('sold_at')->get();

        return view('livewire.reports.index', [
            'production' => $production, 'stock' => $stock, 'expenses' => $expenses, 'sales' => $sales,
            'totals' => [
                'production' => $production->sum('quantity'),
                'stock_value' => $stock->sum(fn ($i) => $i->stock_value),
                'expenses' => $expenses->sum('amount'),
                'revenue' => $sales->sum('total'),
                'margin' => $sales->sum('total') - $expenses->sum('amount'),
            ],
            'properties' => Property::orderBy('name')->get(),
            'seasons' => Season::orderByDesc('id')->get(),
            'charts' => $this->chartPayload(),
        ]);
    }

    public function exportCsv(string $type)
    {
        // Exportação simples via stream — CSV
        return response()->streamDownload(function () use ($type) {
            $out = fopen('php://output', 'w');
            if ($type === 'production') {
                fputcsv($out, ['Data', 'Cultura', 'Safra', 'Qtd', 'Un', 'Lote'], ';');
                foreach ($this->productionRows() as $r) {
                    fputcsv($out, [$r->produced_at?->format('d/m/Y'), $r->crop?->name, $r->season?->name, $r->quantity, $r->unit, $r->batch], ';');
                }
            } elseif ($type === 'expenses') {
                fputcsv($out, ['Data', 'Descrição', 'Categoria', 'Valor'], ';');
                foreach ($this->expenseRows() as $r) {
                    fputcsv($out, [$r->incurred_at?->format('d/m/Y'), $r->description, $r->category, $r->amount], ';');
                }
            }
            fclose($out);
        }, "relatorio-{$type}.csv");
    }

    protected function productionRows()
    {
        return Production::where('organization_id', auth()->user()->organization_id)->with(['crop', 'season'])->orderByDesc('produced_at')->limit(500)->get();
    }

    protected function expenseRows()
    {
        return Expense::where('organization_id', auth()->user()->organization_id)->orderByDesc('incurred_at')->limit(500)->get();
    }
}
