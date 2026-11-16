<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function shippedOrder(): Order
    {
        return Order::factory()->create([
            'status' => Order::DIKIRIM,
            'tracking_number' => 'JX8812345678',
            'shipped_at' => now()->subDay(),
        ]);
    }

    public function test_admin_must_fill_tracking_number_when_marking_sent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => Order::DIPROSES]);

        $rejected = $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'status' => Order::DIKIRIM,
        ]);

        $rejected->assertSessionHasErrors('tracking_number');
        $this->assertSame(Order::DIPROSES, $order->refresh()->status);

        $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'status' => Order::DIKIRIM,
            'tracking_number' => 'JPI9988776655',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::DIKIRIM, $order->status);
        $this->assertSame('JPI9988776655', $order->tracking_number);
        $this->assertNotNull($order->shipped_at);
    }

    public function test_buyer_can_confirm_received_order(): void
    {
        $order = $this->shippedOrder();

        $this->actingAs($order->user)
            ->patch(route('user.orders.receive', $order))
            ->assertRedirect();

        $this->assertSame(Order::SELESAI, $order->refresh()->status);
    }

    public function test_buyer_cannot_confirm_order_that_is_not_sent_yet(): void
    {
        $order = Order::factory()->create(['status' => Order::DIPROSES]);

        $this->actingAs($order->user)
            ->patch(route('user.orders.receive', $order))
            ->assertStatus(400);

        $this->assertSame(Order::DIPROSES, $order->refresh()->status);
    }

    public function test_buyer_cannot_confirm_another_users_order(): void
    {
        $order = $this->shippedOrder();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->patch(route('user.orders.receive', $order))
            ->assertStatus(403);

        $this->assertSame(Order::DIKIRIM, $order->refresh()->status);
    }

    public function test_tracking_number_is_visible_on_the_buyer_order_page(): void
    {
        $order = $this->shippedOrder();

        $this->actingAs($order->user)
            ->get(route('user.orders.show', $order))
            ->assertStatus(200)
            ->assertSee('JX8812345678')
            ->assertSee('Pesanan Sudah Diterima');
    }
}
