<?php

namespace App\Livewire\Seasons;

use App\Models\Season;
use Livewire\Component;

class SeasonDashboard extends Component
{
    public Season $season;
    public array $costsByCategory = [];
    public $activities;
    public $plantings;
    public float $productivity = 0;
    public float $totalCost = 0;
    public float $totalProduction = 0;

    public function mount(Season $season): void
    {
        $this->season = $season->load(['crop', 'property', 'plantings.crop', 'plantings.field', 'activities', 'expenses', 'productions']);
        $this->totalCost = (float) $this->season->expenses->sum('amount');
        $this->totalProduction = (float) $this->season->productions->sum('quantity');
        $this->productivity = $this->season->total_area > 0 ? round($this->totalProduction / $this->season->total_area, 2) : 0;
        $this->costsByCategory = $this->season->expenses->groupBy('category')->map(fn ($g) => (float) $g->sum('amount'))->toArray();
        $this->activities = $this->season->activities()->with('responsible')->orderBy('scheduled_at')->get();
        $this->plantings = $this->season->plantings;
    }

    public function render()
    {
        return view('livewire.seasons.dashboard');
    }
}
