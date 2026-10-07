<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_entrada_aumenta_o_saldo(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'estoquista']));
        $item = InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 100]);

        app(InventoryService::class)->move($item, ['type' => 'entrada', 'quantity' => 50, 'moved_at' => now()->format('Y-m-d')]);

        $this->assertEquals(150, (float) $item->fresh()->current_stock);
    }

    public function test_saida_diminui_o_saldo(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'estoquista']));
        $item = InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 100]);

        app(InventoryService::class)->move($item, ['type' => 'saida', 'quantity' => 30, 'moved_at' => now()->format('Y-m-d')]);

        $this->assertEquals(70, (float) $item->fresh()->current_stock);
    }

    public function test_nao_permite_saida_maior_que_estoque(): void
    {
        $org = Organization::factory()->create();
        $this->actingAs(User::factory()->create(['organization_id' => $org->id, 'role' => 'estoquista']));
        $item = InventoryItem::factory()->create(['organization_id' => $org->id, 'current_stock' => 10]);

        $this->expectException(ValidationException::class);
        app(InventoryService::class)->move($item, ['type' => 'saida', 'quantity' => 999, 'moved_at' => now()->format('Y-m-d')]);
    }
}
