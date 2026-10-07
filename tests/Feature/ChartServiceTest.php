<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\User;
use App\Services\ChartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_value_by_category_returns_labels_and_values(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));
        InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 10, 'average_cost' => 100]);
        InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 5, 'average_cost' => 50]);

        $data = app(ChartService::class)->stockValueByCategory();

        $this->assertArrayHasKey('labels', $data);
        $this->assertArrayHasKey('values', $data);
        $this->assertEquals(count($data['labels']), count($data['values']));
        $this->assertEquals(1250.0, array_sum($data['values']));
    }

    public function test_monthly_series_have_twelve_points(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));

        foreach (['salesByMonth', 'expensesMonthly', 'productionMonthly'] as $method) {
            $data = app(ChartService::class)->$method();
            $this->assertCount(12, $data['labels'], $method);
            $this->assertCount(12, $data['values'], $method);
        }
    }

    public function test_reports_chart_payload_respects_filters(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));

        $component = \Livewire\Livewire::test(\App\Livewire\Reports\ReportsIndex::class);
        $payload = $component->viewData('charts');

        foreach (['prodCrop', 'prodMonth', 'expCat', 'cash', 'stockCat'] as $key) {
            $this->assertArrayHasKey($key, $payload, $key);
        }
        $this->assertArrayHasKey('revenue', $payload['cash']);
    }
}
