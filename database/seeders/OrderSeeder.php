<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@bookspace.id')->first();
        if (! $user) {
            $user = User::first();
        }

        $admin = User::where('role', 'admin')->first();
        $books = Book::all();

        if (! $user || $books->isEmpty()) {
            return;
        }

        // Only seed if orders are fewer than 15 to prevent excessive duplicate seeding
        if (Order::count() > 15) {
            return;
        }

        $statuses = [
            'selesai', 'selesai', 'selesai',
            'dikirim', 'dikirim',
            'diproses',
            'dibayar',
            'menunggu_verifikasi',
            'menunggu_pembayaran',
        ];

        $paymentMethods = ['transfer', 'qris'];

        for ($i = 29; $i >= 0; $i--) {
            $orderDate = Carbon::now()->subDays($i)->setHour(rand(8, 20))->setMinute(rand(0, 59));

            // 1 to 2 orders per day
            $dailyOrders = rand(1, 2);

            for ($j = 0; $j < $dailyOrders; $j++) {
                $status = ($i > 5) ? 'selesai' : $statuses[array_rand($statuses)];
                $method = $paymentMethods[array_rand($paymentMethods)];

                // Pick 1 to 3 random books
                $selectedBooks = $books->random(min(rand(1, 3), $books->count()));

                $total = 0;
                $itemsData = [];

                foreach ($selectedBooks as $book) {
                    $qty = rand(1, 2);
                    $price = $book->finalPrice();
                    $subtotal = $price * $qty;
                    $total += $subtotal;

                    $itemsData[] = [
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'price' => $book->price,
                        'discount_percent' => $book->discount_percent,
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                    ];
                }

                $code = 'INV-'.$orderDate->format('ymd').'-'.strtoupper(Str::random(4));

                $order = Order::create([
                    'user_id' => $user->id,
                    'code' => $code,
                    'status' => $status,
                    'recipient_name' => $user->name,
                    'phone' => $user->phone ?? '081298765432',
                    'address' => 'Jl. Merdeka No. 45, RT 02/RW 04, Menteng, Jakarta Pusat, 10310',
                    'total' => $total,
                    'note' => $j === 0 ? 'Mohon kemas rapi dengan bubble wrap tebal.' : null,
                    'paid_at' => in_array($status, Order::STOCK_REDUCED_STATUSES) ? $orderDate->copy()->addMinutes(rand(10, 60)) : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate->copy()->addMinutes(rand(15, 120)),
                ]);

                foreach ($itemsData as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'book_id' => $item['book_id'],
                        'book_title' => $item['book_title'],
                        'price' => $item['price'],
                        'discount_percent' => $item['discount_percent'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }

                // Create payment record
                $paymentStatus = match ($status) {
                    'dibayar', 'diproses', 'dikirim', 'selesai' => Payment::DITERIMA,
                    'menunggu_verifikasi' => Payment::MENUNGGU_VERIFIKASI,
                    'dibatalkan' => Payment::DITOLAK,
                    default => Payment::MENUNGGU,
                };

                Payment::create([
                    'order_id' => $order->id,
                    'method' => $method,
                    'amount' => $total,
                    'status' => $paymentStatus,
                    'bank_name' => $method === 'transfer' ? 'BCA' : null,
                    'account_number' => $method === 'transfer' ? '8830592811' : null,
                    'account_holder' => $method === 'transfer' ? 'PT BOOKSPACE INDONESIA' : null,
                    'verified_at' => in_array($paymentStatus, [Payment::DITERIMA, Payment::DITOLAK]) ? $orderDate->copy()->addMinutes(rand(30, 90)) : null,
                    'verified_by' => in_array($paymentStatus, [Payment::DITERIMA, Payment::DITOLAK]) ? $admin?->id : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }
        }
    }
}
