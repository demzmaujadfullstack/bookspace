<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private User $admin;

    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->book = Book::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'price' => 100000,
            'discount_percent' => 0,
            'stock' => 10,
        ]);
    }

    private function checkoutTwoCopies(): Order
    {
        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 2]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), [
            'recipient_name' => 'Rina Kartika',
            'phone' => '081234567890',
            'address' => 'Jl. Kenanga No. 12, Bandung',
            'payment_method' => 'transfer',
        ])->assertRedirect();

        return $this->buyer->orders()->latest('id')->firstOrFail();
    }

    public function test_full_flow_from_cart_to_received_package(): void
    {
        Storage::fake('local');

        $order = $this->checkoutTwoCopies();
        $this->assertSame(Order::MENUNGGU_PEMBAYARAN, $order->status);
        $this->assertSame(200000, (int) $order->total);
        $this->assertSame(10, $this->book->refresh()->stock, 'Stok belum boleh berkurang sebelum pembayaran diverifikasi.');

        // 1. Buyer uploads proof
        $this->actingAs($this->buyer)->post(route('checkout.payment.upload', $order), [
            'proof_image' => UploadedFile::fake()->image('bukti-transfer.png'),
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::MENUNGGU_VERIFIKASI, $order->status);
        $this->assertSame(Payment::MENUNGGU_VERIFIKASI, $order->latestPayment->status);
        Storage::disk('local')->assertExists($order->latestPayment->proof_image);

        // 2. Admin verifies payment: stock is deducted inside a transaction
        $this->actingAs($this->admin)->patch(route('admin.orders.payment.verify', $order))->assertRedirect();

        $this->assertSame(Order::DIBAYAR, $order->refresh()->status);
        $this->assertSame(8, $this->book->refresh()->stock);
        $this->assertNotNull($order->paid_at);
        $movement = StockMovement::where('order_id', $order->id)->where('type', 'keluar')->firstOrFail();
        $this->assertSame(-2, (int) $movement->quantity);
        $this->assertSame(8, (int) $movement->stock_after);

        // 3. Admin packs and ships with a tracking number
        $this->actingAs($this->admin)->patch(route('admin.orders.status', $order), ['status' => Order::DIPROSES]);
        $this->actingAs($this->admin)->patch(route('admin.orders.status', $order), [
            'status' => Order::DIKIRIM,
            'tracking_number' => 'TIKI9876543210',
        ])->assertRedirect();

        $this->assertSame(Order::DIKIRIM, $order->refresh()->status);
        $this->assertSame('TIKI9876543210', $order->tracking_number);

        // 4. Buyer confirms the package arrived
        $this->actingAs($this->buyer)->patch(route('user.orders.receive', $order))->assertRedirect();

        $this->assertSame(Order::SELESAI, $order->refresh()->status);
        $this->assertSame(8, $this->book->refresh()->stock, 'Kartu terima tidak mengubah stok.');
    }

    public function test_checkout_rejects_quantity_that_exceeds_stock(): void
    {
        $this->book->update(['stock' => 1]);

        $this->actingAs($this->buyer)->post(route('books.cart.add', $this->book), ['quantity' => 3]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), [
            'recipient_name' => 'Rina Kartika',
            'phone' => '081234567890',
            'address' => 'Jl. Kenanga No. 12, Bandung',
            'payment_method' => 'transfer',
        ])->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $this->book->refresh()->stock);
    }

    public function test_guest_cannot_reach_checkout(): void
    {
        $this->post(route('checkout.store'), ['payment_method' => 'transfer'])->assertRedirect('/login');
        $this->get(route('user.orders'))->assertRedirect('/login');
    }

    public function test_buyer_can_cancel_before_payment_but_not_after_verification(): void
    {
        $order = $this->checkoutTwoCopies();

        $this->actingAs($this->buyer)->post(route('checkout.cancel', $order))->assertRedirect();
        $this->assertSame(Order::DIBATALKAN, $order->refresh()->status);
        $this->assertSame(10, $this->book->refresh()->stock);

        $paidOrder = $this->checkoutTwoCopies();
        $this->actingAs($this->admin)->patch(route('admin.orders.payment.verify', $paidOrder));

        $this->actingAs($this->buyer)->post(route('checkout.cancel', $paidOrder))->assertSessionHas('error');
        $this->assertSame(Order::DIBAYAR, $paidOrder->refresh()->status);
    }

    public function test_admin_cannot_verify_the_same_payment_twice(): void
    {
        $order = $this->checkoutTwoCopies();

        $this->actingAs($this->admin)->patch(route('admin.orders.payment.verify', $order));
        $this->actingAs($this->admin)->patch(route('admin.orders.payment.verify', $order))->assertSessionHas('error');

        $this->assertSame(8, $this->book->refresh()->stock, 'Stok hanya boleh dipotong satu kali.');
    }

    public function test_buyer_cannot_open_another_customers_order(): void
    {
        $order = $this->checkoutTwoCopies();
        $other = User::factory()->create();

        $this->actingAs($other)->get(route('user.orders.show', $order))->assertStatus(403);
    }
}
