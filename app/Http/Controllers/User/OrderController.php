<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $orders = $user->orders()
            ->with(['items.book', 'latestPayment'])
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($order->user_id === $user->id, 403);

        $order->load(['items.book.category', 'latestPayment']);

        return view('user.orders.show', compact('order'));
    }
}
