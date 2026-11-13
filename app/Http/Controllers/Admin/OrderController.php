<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'latestPayment', 'items.book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)
                    ->orWhere('recipient_name', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $countsByStatus = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $statusCounts = [
            'all' => (int) $countsByStatus->sum(),
            'pending' => (int) ($countsByStatus[Order::PENDING] ?? 0),
            'menunggu_pembayaran' => (int) ($countsByStatus[Order::MENUNGGU_PEMBAYARAN] ?? 0),
            'menunggu_verifikasi' => (int) ($countsByStatus[Order::MENUNGGU_VERIFIKASI] ?? 0),
            'dibayar' => (int) ($countsByStatus[Order::DIBAYAR] ?? 0),
            'diproses' => (int) ($countsByStatus[Order::DIPROSES] ?? 0),
            'dikirim' => (int) ($countsByStatus[Order::DIKIRIM] ?? 0),
            'selesai' => (int) ($countsByStatus[Order::SELESAI] ?? 0),
            'dibatalkan' => (int) ($countsByStatus[Order::DIBATALKAN] ?? 0),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.book', 'payments.verifier', 'movements']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Verifikasi & Konfirmasi Pembayaran oleh Admin.
     * MENGURANGI STOK DENGAN DATABASE TRANSACTION & ROW LOCKING (FOR UPDATE)
     * Mencegah overselling dan menjamin konsistensi stok!
     */
    public function verifyPayment(Request $request, Order $order): RedirectResponse
    {
        if (in_array($order->status, Order::STOCK_REDUCED_STATUSES)) {
            return back()->with('error', 'Pembayaran pesanan ini sudah pernah dikonfirmasi sebelumnya.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Lock each book row and check availability
                foreach ($order->items as $item) {
                    /** @var Book $book */
                    $book = Book::where('id', $item->book_id)->lockForUpdate()->first();

                    if (! $book || $book->stock < $item->quantity) {
                        $available = $book ? $book->stock : 0;
                        throw new \Exception("Stok buku '{$item->book_title}' tidak mencukupi untuk memenuhi pesanan ini (Tersisa: {$available}, Dibutuhkan: {$item->quantity}).");
                    }

                    // Deduct stock
                    $newStock = $book->stock - $item->quantity;
                    $book->update(['stock' => $newStock]);

                    // Record stock movement
                    StockMovement::create([
                        'book_id' => $book->id,
                        'order_id' => $order->id,
                        'user_id' => auth()->id(),
                        'type' => 'keluar',
                        'quantity' => -$item->quantity,
                        'stock_after' => $newStock,
                        'reason' => "Pengurangan stok otomatis pesanan #{$order->code}",
                    ]);
                }

                // Update order status
                $order->update([
                    'status' => Order::DIBAYAR,
                    'paid_at' => now(),
                ]);

                // Update payment record
                if ($payment = $order->latestPayment) {
                    $payment->update([
                        'status' => Payment::DITERIMA,
                        'verified_at' => now(),
                        'verified_by' => auth()->id(),
                    ]);
                }
            });

            return back()->with('success', "Pembayaran untuk pesanan #{$order->code} berhasil diverifikasi dan stok buku telah otomatis dikurangi!");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order->update([
            'status' => Order::DIBATALKAN,
        ]);

        if ($payment = $order->latestPayment) {
            $payment->update([
                'status' => Payment::DITOLAK,
                'note' => $request->reason ?? 'Pembayaran ditolak oleh admin',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);
        }

        return back()->with('success', "Pembayaran untuk pesanan #{$order->code} telah ditolak dan status diubah menjadi Dibatalkan.");
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,menunggu_pembayaran,menunggu_verifikasi,dibayar,diproses,dikirim,selesai,dibatalkan'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If transitioning into stock-reduced status from a non-stock-reduced status, verify payment first
        if (! in_array($oldStatus, Order::STOCK_REDUCED_STATUSES) && in_array($newStatus, Order::STOCK_REDUCED_STATUSES)) {
            return $this->verifyPayment($request, $order);
        }

        // If transitioning from paid to cancelled, restore stock
        if (in_array($oldStatus, Order::STOCK_REDUCED_STATUSES) && $newStatus === Order::DIBATALKAN) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $book = Book::where('id', $item->book_id)->lockForUpdate()->first();
                    if ($book) {
                        $newStock = $book->stock + $item->quantity;
                        $book->update(['stock' => $newStock]);

                        StockMovement::create([
                            'book_id' => $book->id,
                            'order_id' => $order->id,
                            'user_id' => auth()->id(),
                            'type' => 'masuk',
                            'quantity' => $item->quantity,
                            'stock_after' => $newStock,
                            'reason' => "Pengembalian stok akibat pembatalan pesanan #{$order->code}",
                        ]);
                    }
                }
                $order->update(['status' => Order::DIBATALKAN]);
            });

            return back()->with('success', "Pesanan #{$order->code} berhasil dibatalkan dan stok dikembalikan.");
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', "Status pesanan #{$order->code} berhasil diubah menjadi '{$order->statusLabel()}'.");
    }
}
