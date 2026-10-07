<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_button_logs_in_with_one_click(): void
    {
        $org = Organization::factory()->create();
        User::factory()->create(['email' => 'admin@fazenda.com', 'organization_id' => $org->id, 'role' => 'admin', 'active' => true]);

        $response = $this->post(route('login.demo'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@fazenda.com')->first());
    }

    public function test_demo_login_returns_404_when_disabled(): void
    {
        config()->set('farmmanager.demo_login', false);

        $this->post(route('login.demo'))->assertNotFound();
        $this->assertGuest();
    }
}
