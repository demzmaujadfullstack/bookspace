<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAndTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_does_not_decrease_when_adding_to_cart_or_checkout(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $cat = Category::create(['name' => 'Novel', 'slug' => 'novel']);
        $book = Book::create([
            'category_id' => $cat->id,
            'title' => 'Laskar Pelangi',
            'slug' => 'laskar-pelangi',
            'author' => 'Andrea Hirata',
            'isbn' => '9789793062792',
            'price' => 100000,
            'stock' => 10,
        ]);

        // 1. Add to cart
        $this->actingAs($user)->post(route('books.cart.add', $book), [
            'quantity' => 2,
        ]);

        $book->refresh();
        $this->assertSame(10, $book->stock, 'Stok tidak boleh berkurang saat masuk keranjang.');

        // 2. Checkout
        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'payment_method' => 'transfer',
        ]);

        $book->refresh();
        $this->assertSame(10, $book->stock, 'Stok tidak boleh berkurang saat checkout belum diverifikasi.');

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame(Order::MENUNGGU_PEMBAYARAN, $order->status);
    }

    public function test_stock_decreases_only_after_admin_verifies_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $cat = Category::create(['name' => 'Novel', 'slug' => 'novel']);
        $book = Book::create([
            'category_id' => $cat->id,
            'title' => 'Bumi Manusia',
            'slug' => 'bumi-manusia',
            'author' => 'Pramoedya',
            'isbn' => '9789794175842',
            'price' => 100000,
            'stock' => 5,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'BKSP-TEST-001',
            'status' => Order::MENUNGGU_VERIFIKASI,
            'recipient_name' => 'Dimas',
            'phone' => '081234567890',
            'address' => 'Surabaya',
            'total' => 200000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'book_title' => $book->title,
            'price' => 100000,
            'quantity' => 2,
            'subtotal' => 200000,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'transfer',
            'amount' => 200000,
            'status' => Payment::MENUNGGU_VERIFIKASI,
        ]);

        // Verify payment by Admin
        $response = $this->actingAs($admin)->patch(route('admin.orders.payment.verify', $order));

        $book->refresh();
        $order->refresh();

        $this->assertSame(3, $book->stock, 'Stok buku berkurang 2 dari 5 menjadi 3 setelah admin verifikasi pembayaran.');
        $this->assertSame(Order::DIBAYAR, $order->status);
        $this->assertSame(Payment::DITERIMA, $order->latestPayment->status);
    }
}
