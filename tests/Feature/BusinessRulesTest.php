<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\Field;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\Planting;
use App\Models\Production;
use App\Models\Property;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ProductionService;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_compra_recebida_atualiza_estoque(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'gestor']));

        $supplier = Supplier::create(['organization_id' => $org->id, 'company_name' => 'Fornecedor Teste']);
        $item = InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 10]);
        $purchase = Purchase::create(['organization_id' => $org->id, 'supplier_id' => $supplier->id, 'number' => 'PED-T1', 'status' => 'aprovada', 'total' => 100]);
        $purchase->items()->create(['inventory_item_id' => $item->id, 'quantity' => 25, 'unit' => 'un', 'unit_price' => 10, 'subtotal' => 250]);

        app(PurchaseService::class)->receive($purchase);

        $this->assertEquals('recebida', $purchase->fresh()->status);
        $this->assertEquals(35, (float) $item->fresh()->current_stock);
    }

    public function test_colheita_atualiza_producao(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'agronomo']));

        $property = Property::factory()->create(['organization_id' => $org->id]);
        $field = Field::create(['organization_id' => $org->id, 'property_id' => $property->id, 'name' => 'T1', 'area' => 100]);
        $crop = Crop::create(['organization_id' => $org->id, 'name' => 'Soja']);
        $planting = Planting::create(['organization_id' => $org->id, 'property_id' => $property->id, 'field_id' => $field->id, 'crop_id' => $crop->id, 'planted_area' => 100, 'status' => 'crescimento']);

        app(ProductionService::class)->recordHarvest([
            'organization_id' => $org->id, 'planting_id' => $planting->id, 'field_id' => $field->id,
            'crop_id' => $crop->id, 'harvested_at' => now()->format('Y-m-d'), 'quantity' => 500, 'unit' => 'sc',
        ]);

        $this->assertEquals(500, (float) $planting->fresh()->realized_yield);
        $this->assertEquals(500, (float) Production::where('planting_id', $planting->id)->sum('quantity'));
    }

    public function test_tenant_isolation(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $orgA->id, 'role' => 'gestor']));
        Property::factory()->create(['organization_id' => $orgA->id, 'name' => 'Fazenda A']);
        Property::factory()->create(['organization_id' => $orgB->id, 'name' => 'Fazenda B']);

        $names = Property::all()->pluck('name');

        $this->assertContains('Fazenda A', $names);
        $this->assertNotContains('Fazenda B', $names);
    }

    public function test_autorizacao_por_perfil(): void
    {
        $org = Organization::factory()->create();
        $estoquista = User::factory()->create(['organization_id' => $org->id, 'role' => 'estoquista']);

        $this->assertFalse($estoquista->canAccess('sales'));
        $this->assertTrue($estoquista->canAccess('inventory'));
    }
}
