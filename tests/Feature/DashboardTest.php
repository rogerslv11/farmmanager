<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\Field;
use App\Models\Organization;
use App\Models\Planting;
use App\Models\Property;
use App\Models\Season;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_season_returns_progress_and_totals(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));
        $property = Property::factory()->create(['organization_id' => $org->id]);
        $crop = Crop::create(['organization_id' => $org->id, 'name' => 'Soja']);
        Season::create(['organization_id' => $org->id, 'property_id' => $property->id, 'crop_id' => $crop->id, 'name' => 'Safra X', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonth(), 'total_area' => 100, 'status' => 'em_andamento']);

        $featured = app(DashboardService::class)->featuredSeason($org->id);

        $this->assertNotNull($featured);
        $this->assertEquals('Safra X', $featured['name']);
        $this->assertGreaterThan(0, $featured['progress']);
    }

    public function test_deltas_have_expected_structure(): void
    {
        $org = Organization::factory()->create();
        $deltas = app(DashboardService::class)->deltas($org->id);

        $this->assertEquals(['revenue', 'expenses', 'production'], array_keys($deltas));
        $this->assertArrayHasKey('pct', $deltas['revenue']);
    }

    public function test_agenda_lists_only_pending_items(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));
        \App\Models\AgriculturalActivity::create(['organization_id' => $org->id, 'title' => 'Pendente', 'scheduled_at' => now()->addDay(), 'status' => 'pendente']);
        \App\Models\AgriculturalActivity::create(['organization_id' => $org->id, 'title' => 'Feita', 'scheduled_at' => now()->addDay(), 'status' => 'concluida']);

        $agenda = app(DashboardService::class)->agenda($org->id);

        $this->assertTrue($agenda->pluck('title')->contains('Pendente'));
        $this->assertFalse($agenda->pluck('title')->contains('Feita'));
    }

    public function test_productivity_is_production_over_area(): void
    {
        $org = Organization::factory()->create();
        $property = Property::factory()->create(['organization_id' => $org->id]);
        $field = Field::create(['organization_id' => $org->id, 'property_id' => $property->id, 'name' => 'T1', 'area' => 100]);
        $crop = Crop::create(['organization_id' => $org->id, 'name' => 'Soja']);
        Planting::create(['organization_id' => $org->id, 'property_id' => $property->id, 'field_id' => $field->id, 'crop_id' => $crop->id, 'planted_area' => 100, 'realized_yield' => 6000, 'status' => 'colhida']);

        $ranking = app(DashboardService::class)->productivityByCrop($org->id);

        $this->assertEquals(60.0, $ranking[0]['value']);
    }
}
