<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $orders = $user->orders()
            ->with(['items.book', 'latestPayment'])
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($order->user_id === $user->id, 403);

        $order->load(['items.book.category', 'latestPayment']);

        return view('user.orders.show', compact('order'));
    }

    /** Pembeli mengonfirmasi paketnya sudah sampai, status ditutup menjadi Selesai. */
    public function receive(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->canConfirmReceipt(), 400, 'Pesanan ini belum berstatus dikirim.');

        $order->update(['status' => Order::SELESAI]);

        return back()->with('success', "Pesanan #{$order->code} sudah Anda terima. Terima kasih atas belanjanya!");
    }
}
