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
    /** Kelompok status untuk chip penyaring daftar pesanan. */
    private const STATUS_GROUPS = [
        'perlu-tindakan' => ['label' => 'Perlu Tindakan', 'statuses' => [Order::PENDING, Order::MENUNGGU_PEMBAYARAN, Order::MENUNGGU_VERIFIKASI]],
        'diproses' => ['label' => 'Diproses', 'statuses' => [Order::DIBAYAR, Order::DIPROSES]],
        'dikirim' => ['label' => 'Dikirim', 'statuses' => [Order::DIKIRIM]],
        'selesai' => ['label' => 'Selesai', 'statuses' => [Order::SELESAI]],
        'dibatalkan' => ['label' => 'Dibatalkan', 'statuses' => [Order::DIBATALKAN]],
    ];

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $status = $request->query('status');
        $status = is_string($status) && array_key_exists($status, self::STATUS_GROUPS) ? $status : null;

        $orders = $user->orders()
            ->with(['items.book', 'latestPayment'])
            ->when($status, fn ($query) => $query->whereIn('status', self::STATUS_GROUPS[$status]['statuses']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = $user->orders()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $groups = self::STATUS_GROUPS;

        return view('user.orders.index', compact('orders', 'status', 'statusCounts', 'groups'));
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
