<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AllPagesAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        $this->seed();
        return User::where('email', 'admin@fazenda.com')->firstOrFail();
    }

    public function test_all_pages_render_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $paths = [
            '/', '/properties', '/fields', '/crops', '/plantings', '/seasons',
            '/activities', '/production', '/harvests', '/inventory', '/purchases',
            '/suppliers', '/equipment', '/maintenance', '/employees', '/customers',
            '/sales', '/expenses', '/reports', '/notifications', '/audit', '/profile',
            '/properties/1', '/seasons/1',
        ];

        foreach ($paths as $path) {
            $resp = $this->get($path);
            $this->assertTrue(
                in_array($resp->status(), [200]),
                "GET {$path} retornou {$resp->status()}"
            );
        }
    }

    public function test_restricted_role_gets_403_on_guarded_modules(): void
    {
        $this->seed();
        $func = User::where('email', 'funcionario@fazenda.com')->firstOrFail();
        $this->actingAs($func);

        // funcionario só pode dashboard + activities
        $this->get('/sales')->assertStatus(403);
        $this->get('/inventory')->assertStatus(403);
        $this->get('/expenses')->assertStatus(403);
        $this->get('/activities')->assertStatus(200);
        $this->get('/')->assertStatus(200);
    }

    public function test_livewire_crud_save_validation_and_persistence(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        // Properties: vazio deve falhar validacao, valido deve criar
        Livewire::test('properties.property-index')
            ->call('create')
            ->set('form.name', '')
            ->call('save')
            ->assertHasErrors(['form.name']);
        Livewire::test('properties.property-index')
            ->call('create')
            ->set('form.name', 'Fazenda Audit')
            ->set('form.total_area', 100)
            ->call('save')
            ->assertHasNoErrors();
        $this->assertDatabaseHas('properties', ['name' => 'Fazenda Audit']);

        // Crops
        Livewire::test('crops.crop-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Suppliers
        Livewire::test('suppliers.supplier-index')
            ->call('create')->set('form.company_name', '')->call('save')
            ->assertHasErrors(['form.company_name']);

        // Customers
        Livewire::test('customers.customer-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Employees
        Livewire::test('employees.employee-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Equipment
        Livewire::test('equipment.equipment-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Expenses
        Livewire::test('expenses.expense-index')
            ->call('create')->set('form.description', '')->set('form.amount', '')->call('save')
            ->assertHasErrors(['form.description']);

        // Activities
        Livewire::test('activities.activity-index')
            ->call('create')->set('form.title', '')->call('save')
            ->assertHasErrors(['form.title']);

        // Seasons
        Livewire::test('seasons.season-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Inventory
        Livewire::test('inventory.inventory-index')
            ->call('create')->set('form.name', '')->call('save')
            ->assertHasErrors(['form.name']);

        // Sales: sem customer deve falhar, nao excecao
        Livewire::test('sales.sale-index')
            ->call('create')->set('form.quantity', '')->call('save')
            ->assertHasErrors(['form.customer_id']);

        // Purchases: sem linhas deve falhar validacao
        Livewire::test('purchases.purchase-index')
            ->call('create')->call('save')
            ->assertHasErrors(['form.supplier_id']);

        // Search / sort / paginacao nao devem lancar excecao
        Livewire::test('properties.property-index')->set('search', 'Santa')->assertOk();
        Livewire::test('seasons.season-index')->set('search', 'Soja')->call('sortBy', 'name')->assertOk();
        Livewire::test('inventory.inventory-index')->set('search', 'Soja')->set('statusFilter', 'baixo')->assertOk();
        Livewire::test('audit.audit-index')->set('search', 'x')->assertOk();
        Livewire::test('reports.reports-index')->set('tab', 'financeiro')->assertOk();
        Livewire::test('dashboard.overview')->call('completeActivity', 1)->assertOk();
    }

    public function test_inventory_move_and_purchase_flow(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $item = \App\Models\InventoryItem::firstOrFail();
        $stockBefore = (float) $item->current_stock;

        // saida maior que estoque deve gerar erro de validacao, nao 500
        Livewire::test('inventory.inventory-index')
            ->call('openMove', $item->id)
            ->set('move.type', 'saida')
            ->set('move.quantity', $stockBefore + 99999)
            ->set('move.moved_at', now()->format('Y-m-d'))
            ->call('saveMove')
            ->assertHasErrors(['move.quantity']);

        // entrada valida aumenta estoque
        Livewire::test('inventory.inventory-index')
            ->call('openMove', $item->id)
            ->set('move.type', 'entrada')
            ->set('move.quantity', 5)
            ->set('move.moved_at', now()->format('Y-m-d'))
            ->call('saveMove')
            ->assertHasNoErrors();
        $this->assertEquals($stockBefore + 5, (float) $item->fresh()->current_stock);

        // concluir atividade existente
        $act = \App\Models\AgriculturalActivity::firstOrFail();
        Livewire::test('activities.activity-index')->call('markDone', $act->id)->assertOk();
        $this->assertEquals('concluida', $act->fresh()->status);
    }

    public function test_reports_export_does_not_crash(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $c = Livewire::test('reports.reports-index');
        // exportCsv retorna streamDownload — em Livewire isso pode nao ser suportado;
        // ao menos o payload de graficos deve funcionar com filtros
        $c->set('from', now()->subMonths(6)->format('Y-m-d'))
            ->set('to', now()->format('Y-m-d'))
            ->assertOk();
        $view = $c->viewData('charts');
        $this->assertIsArray($view);
        $this->assertArrayHasKey('prodCrop', $view);

        // exportCsv nao deve lancar excecao
        $c->call('exportCsv', 'production');
        $c->call('exportCsv', 'expenses');
    }
}
