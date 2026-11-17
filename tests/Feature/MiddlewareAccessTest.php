<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<string, string> */
    protected array $adminRoutes = [
        'admin.dashboard' => '/admin',
        'admin.books.index' => '/admin/books',
        'admin.orders.index' => '/admin/transaksi',
        'admin.users.index' => '/admin/users',
        'admin.reports.index' => '/admin/laporan',
    ];

    public function test_guest_is_sent_to_login_when_opening_admin_pages(): void
    {
        foreach ($this->adminRoutes as $name => $uri) {
            $this->get(route($name))->assertRedirect(route('login'));
            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    public function test_guest_is_sent_to_login_when_opening_account_pages(): void
    {
        $this->get(route('user.dashboard'))->assertRedirect(route('login'));
        $this->get(route('user.orders'))->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_admin_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        foreach (array_keys($this->adminRoutes) as $name) {
            $this->get(route($name))
                ->assertForbidden()
                ->assertSee('Halaman ini khusus administrator.');
        }
    }

    public function test_regular_user_can_open_account_pages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('user.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('user.orders'))->assertOk();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('user.dashboard'))->assertOk();

        foreach ($this->adminRoutes as $name => $uri) {
            $this->actingAs($admin)->get(route($name))->assertOk();
        }
    }

    public function test_role_cannot_be_filled_from_request_body(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->patch(route('user.profile.update'), [
            'name' => 'Dimas Pratama',
            'email' => $user->email,
            'role' => 'admin',
        ])->assertSessionHasNoErrors();

        $this->assertSame('user', $user->fresh()->role);
    }
}
