<?php

namespace App\Livewire\Dashboard;

use App\Models\AgriculturalActivity;
use App\Services\DashboardService;
use Livewire\Component;

class Overview extends Component
{
    public array $indicators = [];
    public array $alerts = [];
    public array $productionByCrop = [];
    public array $cashflow = [];
    public array $costBySeason = [];
    public array $deltas = [];
    public ?array $featuredSeason = null;
    public array $productivity = [];
    public array $recent = [];

    public function mount(DashboardService $service): void
    {
        $this->loadData($service);
    }

    protected function loadData(DashboardService $service): void
    {
        $orgId = auth()->user()->organization_id;
        $this->indicators = $service->indicators($orgId);
        $this->alerts = $service->alerts($orgId);
        $this->productionByCrop = $service->productionByCrop($orgId);
        $this->cashflow = $service->monthlySeries($orgId);
        $this->costBySeason = $service->costBySeason($orgId);
        $this->deltas = $service->deltas($orgId);
        $this->featuredSeason = $service->featuredSeason($orgId);
        $this->productivity = $service->productivityByCrop($orgId);
        $this->recent = $service->recent($orgId);
        $this->agenda = $service->agenda($orgId);
        $this->criticalStock = $service->criticalStock($orgId);
    }

    /** @var \Illuminate\Database\Eloquent\Collection Atividades da agenda */
    public $agenda;

    /** @var \Illuminate\Database\Eloquent\Collection Itens críticos */
    public $criticalStock;

    public function completeActivity(int $id, DashboardService $service): void
    {
        $activity = AgriculturalActivity::findOrFail($id);
        $activity->update(['status' => 'concluida']);
        $this->loadData($service);
        $this->dispatch('notify', message: 'Atividade concluída. Bom trabalho!', type: 'success');
    }
    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}
