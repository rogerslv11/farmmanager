<?php

namespace Database\Seeders;

use App\Models\AgriculturalActivity;
use App\Models\Crop;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\Field;
use App\Models\Harvest;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\MaintenanceRecord;
use App\Models\Organization;
use App\Models\Planting;
use App\Models\Production;
use App\Models\Property;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Season;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::create([
            'name' => 'Fazenda Santa Esperança', 'slug' => 'santa-esperanca',
            'document' => '12.345.678/0001-90', 'email' => 'contato@santaesperanca.com',
            'phone' => '(64) 99999-0000', 'plan' => 'profissional',
        ]);

        $users = [
            ['Admin Demo', 'admin@fazenda.com', 'admin'],
            ['Gestor Operacional', 'gestor@fazenda.com', 'gestor'],
            ['Ana Agrônoma', 'agro@fazenda.com', 'agronomo'],
            ['Carlos Estoque', 'estoque@fazenda.com', 'estoquista'],
            ['João Campo', 'funcionario@fazenda.com', 'funcionario'],
        ];
        foreach ($users as [$name, $email, $role]) {
            User::create(['name' => $name, 'email' => $email, 'password' => Hash::make('password'), 'organization_id' => $org->id, 'role' => $role, 'active' => true, 'email_verified_at' => now()]);
        }

        $cropsData = [
            ['Soja', 'SOJ', 120, 'sc', 62], ['Milho', 'MIL', 130, 'sc', 110],
            ['Café', 'CAF', 365, 'sc', 32], ['Cana-de-açúcar', 'CAN', 365, 'ton', 85],
            ['Algodão', 'ALG', 170, 'kg', 4200], ['Trigo', 'TRI', 120, 'sc', 55],
            ['Feijão', 'FEI', 90, 'sc', 28], ['Arroz', 'ARZ', 130, 'sc', 75],
        ];
        $crops = [];
        foreach ($cropsData as [$name, $code, $cycle, $unit, $yield]) {
            $crops[$name] = Crop::create(['organization_id' => $org->id, 'name' => $name, 'code' => $code, 'average_cycle_days' => $cycle, 'production_unit' => $unit, 'expected_yield' => $yield, 'status' => 'ativa']);
        }

        $props = [];
        foreach ([
            ['Fazenda Santa Esperança — Sede', 'Rio Verde', 'GO', 2400, 1950, -17.79, -50.92],
            ['Fazenda Boa Vista', 'Jataí', 'GO', 1300, 1100, -17.88, -51.71],
            ['Sítio Recanto Verde', 'Montividiu', 'GO', 320, 260, -17.44, -51.17],
        ] as [$name, $city, $state, $total, $prod, $lat, $lng]) {
            $props[] = Property::create(['organization_id' => $org->id, 'name' => $name, 'description' => 'Propriedade produtiva com foco em grãos.', 'owner_name' => 'Família Esperança', 'city' => $city, 'state' => $state, 'total_area' => $total, 'productive_area' => $prod, 'latitude' => $lat, 'longitude' => $lng, 'property_type' => 'fazenda', 'status' => 'ativa']);
        }

        $soils = ['Latossolo', 'Terra roxa', 'Argiloso', 'Misto'];
        $fields = [];
        foreach ($props as $pi => $prop) {
            for ($i = 1; $i <= 6; $i++) {
                $fields[] = Field::create(['organization_id' => $org->id, 'property_id' => $prop->id, 'name' => "Talhão {$i}", 'code' => 'T'.($pi + 1).str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'area' => rand(40, 180), 'soil_type' => $soils[array_rand($soils)], 'irrigation' => $i % 3 === 0 ? 'Pivô central' : 'Sequeiro', 'status' => 'ativo']);
            }
        }

        $seasons = [];
        $season = Season::create(['organization_id' => $org->id, 'property_id' => $props[0]->id, 'crop_id' => $crops['Soja']->id, 'name' => 'Safra 2025/2026 — Soja', 'year' => '2025/26', 'starts_at' => now()->subMonths(5), 'ends_at' => now()->addMonths(2), 'total_area' => 850, 'estimated_production' => 2600, 'realized_production' => 0, 'estimated_cost' => 520000, 'estimated_revenue' => 780000, 'status' => 'em_andamento']);
        $seasons[] = $season;
        $seasons[] = Season::create(['organization_id' => $org->id, 'property_id' => $props[1]->id, 'crop_id' => $crops['Milho']->id, 'name' => 'Safrinha 2026 — Milho', 'year' => '2026', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(4), 'total_area' => 420, 'estimated_production' => 1500, 'estimated_cost' => 260000, 'estimated_revenue' => 390000, 'status' => 'em_andamento']);
        $seasons[] = Season::create(['organization_id' => $org->id, 'property_id' => $props[0]->id, 'crop_id' => $crops['Soja']->id, 'name' => 'Safra 2024/2025 — Soja', 'year' => '2024/25', 'starts_at' => now()->subMonths(17), 'ends_at' => now()->subMonths(9), 'total_area' => 800, 'estimated_production' => 2400, 'realized_production' => 2450, 'estimated_cost' => 480000, 'realized_cost' => 465000, 'estimated_revenue' => 720000, 'status' => 'finalizada']);

        $employees = [];
        foreach ([['José Operador', 'Operador de máquinas'], ['Maria Técnica', 'Técnica agrícola'], ['Pedro Auxiliar', 'Auxiliar de campo'], ['Lucia Administradora', 'Administrativo'], ['Rafael Tratorista', 'Tratorista'], ['Tiago Pulverizador', 'Operador de pulverizador']] as [$name, $role]) {
            $employees[] = Employee::create(['organization_id' => $org->id, 'property_id' => $props[array_rand($props)]->id, 'name' => $name, 'role_title' => $role, 'phone' => '(64) 99999-'.rand(1000, 9999), 'email' => Str::slug(explode(' ', $name)[0]).'@fazenda.com', 'hired_at' => now()->subYears(rand(1, 5)), 'status' => 'ativo']);
        }

        $statuses = ['plantada', 'crescimento', 'crescimento', 'pronta_colheita', 'colhida'];
        $plantings = [];
        foreach (array_slice($fields, 0, 12) as $i => $field) {
            $crop = $i % 2 === 0 ? $crops['Soja'] : $crops['Milho'];
            $plantings[] = Planting::create(['organization_id' => $org->id, 'property_id' => $field->property_id, 'field_id' => $field->id, 'crop_id' => $crop->id, 'season_id' => $seasons[$i % 2]->id, 'planted_at' => now()->subMonths(rand(1, 4)), 'harvest_forecast_at' => now()->addDays(rand(5, 90)), 'planted_area' => $field->area, 'seed_quantity' => $field->area * 0.06, 'estimated_yield' => $field->area * 60, 'status' => $statuses[$i % count($statuses)]]);
        }

        $activityTypes = ['preparo_solo' => 'Preparo do solo', 'plantio' => 'Plantio', 'fertilizante' => 'Adubação de cobertura', 'defensivo' => 'Pulverização', 'monitoramento' => 'Monitoramento da lavoura', 'irrigacao' => 'Irrigação', 'colheita' => 'Colheita'];
        $actStatuses = ['concluida', 'concluida', 'em_andamento', 'agendada', 'pendente'];
        foreach ($plantings as $i => $pl) {
            foreach (array_rand($activityTypes, 3) as $type) {
                $a = AgriculturalActivity::create(['organization_id' => $org->id, 'property_id' => $pl->property_id, 'field_id' => $pl->field_id, 'planting_id' => $pl->id, 'season_id' => $pl->season_id, 'responsible_id' => $employees[array_rand($employees)]->id, 'title' => $activityTypes[$type].' — '.$pl->field->name, 'activity_type' => $type, 'scheduled_at' => now()->subDays(rand(0, 40))->addDays($i % 2 ? -3 : 10), 'duration_hours' => rand(2, 10), 'cost' => rand(500, 8000), 'status' => $actStatuses[array_rand($actStatuses)]]);
                $a->employees()->attach(array_rand(array_flip(array_column(array_map(fn ($e) => ['id' => $e->id], $employees), 'id')), min(2, count($employees))));
            }
        }
        // atividade propositalmente atrasada
        AgriculturalActivity::create(['organization_id' => $org->id, 'property_id' => $props[0]->id, 'title' => 'Pulverização urgente — Talhão 2', 'activity_type' => 'defensivo', 'scheduled_at' => now()->subDays(6), 'status' => 'pendente', 'cost' => 3200]);

        $categories = [];
        foreach ([['Sementes', 'insumo'], ['Fertilizantes', 'insumo'], ['Defensivos', 'insumo'], ['Combustíveis', 'insumo'], ['Ferramentas', 'material'], ['EPIs', 'material'], ['Peças', 'material']] as [$name, $type]) {
            $categories[$name] = InventoryCategory::create(['organization_id' => $org->id, 'name' => $name, 'type' => $type]);
        }

        $suppliers = [];
        foreach ([['AgroSul Insumos LTDA', 'AgroSul', 'insumos'], ['FertSolo Distribuidora', 'FertSolo', 'insumos'], ['TratorPeças Centro-Oeste', 'TratorPeças', 'maquinas'], ['CoopAgri Serviços', 'CoopAgri', 'servicos']] as [$rs, $fant, $cat]) {
            $suppliers[] = Supplier::create(['organization_id' => $org->id, 'company_name' => $rs, 'trade_name' => $fant, 'document' => rand(10, 99).'.'.rand(100, 999).'.'.rand(100, 999).'/0001-'.rand(10, 99), 'phone' => '(64) 3333-'.rand(1000, 9999), 'email' => Str::slug($fant).'@exemplo.com', 'category' => $cat, 'status' => 'ativo']);
        }

        $productsData = [
            ['Semente Soja RR 8473', 'SEM-SOJ-01', 'Sementes', 'sc', 320, 100, 485.00],
            ['Semente Milho Híbrido 505', 'SEM-MIL-02', 'Sementes', 'sc', 180, 60, 620.00],
            ['Fertilizante NPK 04-14-08', 'FER-NPK-01', 'Fertilizantes', 'ton', 42, 15, 2850.00],
            ['Ureia 45%', 'FER-URE-02', 'Fertilizantes', 'ton', 8, 12, 3100.00],
            ['Herbicida Glifosato 5L', 'DEF-GLI-01', 'Defensivos', 'un', 64, 20, 189.90],
            ['Inseticida Biológico 1L', 'DEF-INS-02', 'Defensivos', 'un', 25, 30, 145.00],
            ['Diesel S10', 'COM-DIE-01', 'Combustíveis', 'L', 5200, 2000, 6.29],
            ['Óleo Lubrificante 20L', 'PEC-OLE-01', 'Peças', 'un', 14, 6, 520.00],
        ];
        $items = [];
        foreach ($productsData as [$name, $sku, $cat, $unit, $stock, $min, $cost]) {
            $items[] = InventoryItem::create(['organization_id' => $org->id, 'inventory_category_id' => $categories[$cat]->id, 'supplier_id' => $suppliers[array_rand($suppliers)]->id, 'name' => $name, 'sku' => $sku, 'unit' => $unit, 'current_stock' => $stock, 'minimum_stock' => $min, 'average_cost' => $cost, 'location' => 'Galpão A', 'status' => 'ativo']);
        }
        foreach ($items as $item) {
            InventoryMovement::create(['organization_id' => $org->id, 'inventory_item_id' => $item->id, 'user_id' => User::where('organization_id', $org->id)->first()->id, 'type' => 'entrada', 'quantity' => $item->current_stock, 'unit' => $item->unit, 'unit_cost' => $item->average_cost, 'moved_at' => now()->subDays(rand(5, 60)), 'reason' => 'Estoque inicial']);
        }

        $purchase = Purchase::create(['organization_id' => $org->id, 'supplier_id' => $suppliers[0]->id, 'property_id' => $props[0]->id, 'number' => 'PED-20260001', 'ordered_at' => now()->subDays(12), 'discount' => 500, 'taxes' => 320, 'total' => 0, 'status' => 'aprovada']);
        $subtotal = 0;
        foreach (array_slice($items, 0, 3) as $item) {
            $qty = rand(5, 20);
            $st = $qty * $item->average_cost;
            $purchase->items()->create(['inventory_item_id' => $item->id, 'quantity' => $qty, 'unit' => $item->unit, 'unit_price' => $item->average_cost, 'discount' => 0, 'subtotal' => $st]);
            $subtotal += $st;
        }
        $purchase->update(['total' => $subtotal - 500 + 320]);

        $equipData = [['Trator John Deere 5075E', 'trator', 'disponivel'], ['Colheitadeira New Holland TC 5090', 'colheitadeira', 'em_uso'], ['Pulverizador Jacto Uniport 3030', 'pulverizador', 'disponivel'], ['Caminhão VW 24280', 'caminhao', 'em_manutencao'], ['Grade Aradora 16 discos', 'implemento', 'disponivel']];
        $equipment = [];
        foreach ($equipData as [$name, $cat, $status]) {
            $equipment[] = Equipment::create(['organization_id' => $org->id, 'property_id' => $props[0]->id, 'name' => $name, 'asset_code' => 'PAT-'.rand(1000, 9999), 'category' => $cat, 'brand' => 'Diversas', 'year' => (string) rand(2015, 2024), 'hour_meter' => rand(800, 6000), 'status' => $status]);
        }
        MaintenanceRecord::create(['organization_id' => $org->id, 'equipment_id' => $equipment[3]->id, 'maintenance_type' => 'corretiva', 'performed_at' => now()->subDays(40), 'next_at' => now()->subDays(2), 'cost' => 8400, 'description' => 'Revisão do sistema hidráulico', 'status' => 'concluida']);
        MaintenanceRecord::create(['organization_id' => $org->id, 'equipment_id' => $equipment[0]->id, 'maintenance_type' => 'preventiva', 'performed_at' => now()->subDays(10), 'next_at' => now()->addDays(20), 'cost' => 2300, 'description' => 'Troca de óleo e filtros', 'status' => 'concluida']);

        $customers = [];
        foreach ([['Cooperativa AgroVale', 'pj'], ['Cerealista Boa Safra', 'pj'], ['João Comprador', 'pf']] as [$name, $type]) {
            $customers[] = Customer::create(['organization_id' => $org->id, 'name' => $name, 'document' => rand(100, 999).'.'.rand(100, 999).'.'.rand(100, 999).'-'.rand(10, 99), 'phone' => '(64) 3222-'.rand(1000, 9999), 'customer_type' => $type]);
        }

        foreach ($plantings as $i => $pl) {
            if ($i % 2 === 0) {
                $qty = $pl->planted_area * rand(45, 65);
                Production::create(['organization_id' => $org->id, 'crop_id' => $pl->crop_id, 'season_id' => $pl->season_id, 'field_id' => $pl->field_id, 'planting_id' => $pl->id, 'produced_at' => now()->subDays(rand(2, 50)), 'quantity' => $qty, 'unit' => 'sc', 'quality' => 'padrao', 'batch' => 'LOTE-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT), 'operator' => $employees[array_rand($employees)]->name]);
                $pl->update(['realized_yield' => $qty]);
            }
        }
        $season->update(['realized_production' => Production::where('season_id', $season->id)->sum('quantity')]);

        Harvest::create(['organization_id' => $org->id, 'planting_id' => $plantings[0]->id, 'field_id' => $plantings[0]->field_id, 'crop_id' => $plantings[0]->crop_id, 'responsible_id' => $employees[0]->id, 'harvested_at' => now()->subDays(5), 'quantity' => 3200, 'unit' => 'sc', 'losses' => 45, 'quality' => 'premium', 'equipment_used' => $equipment[1]->name]);

        foreach ([['Sementes de soja', 'sementes', 120000], ['Adubação NPK', 'fertilizantes', 180000], ['Defensivos', 'defensivos', 64000], ['Diesel colheita', 'combustivel', 38000], ['Mão de obra', 'mao_de_obra', 95000], ['Manutenção máquinas', 'manutencao', 27000]] as [$desc, $cat, $val]) {
            Expense::create(['organization_id' => $org->id, 'property_id' => $props[0]->id, 'season_id' => $season->id, 'description' => $desc, 'category' => $cat, 'amount' => $val, 'incurred_at' => now()->subDays(rand(3, 90)), 'status' => 'pago']);
        }
        $season->update(['realized_cost' => Expense::where('season_id', $season->id)->sum('amount')]);

        foreach ([[0, 900, 152.50], [1, 1400, 98.00], [0, 600, 155.00]] as [$ci, $qty, $price]) {
            Sale::create(['organization_id' => $org->id, 'customer_id' => $customers[$ci % count($customers)]->id, 'crop_id' => $crops['Soja']->id, 'batch' => 'LOTE-00'.($ci + 1), 'quantity' => $qty, 'unit' => 'sc', 'unit_price' => $price, 'discount' => 0, 'total' => $qty * $price, 'sold_at' => now()->subDays(rand(2, 40)), 'status' => 'entregue']);
        }
    }
}
