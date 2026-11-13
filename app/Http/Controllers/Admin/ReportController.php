<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $ordersQuery = Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalRevenue = (int) $ordersQuery->sum('total');
        $totalOrders = (int) $ordersQuery->count();

        // Best sellers during this period
        $bestSellers = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereIn('status', Order::STOCK_REDUCED_STATUSES)
                ->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->selectRaw('book_id, book_title, sum(quantity) as total_sold, sum(subtotal) as total_amount')
            ->groupBy('book_id', 'book_title')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Daily breakdown
        $dailySales = Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, count(*) as count, sum(total) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.reports.index', compact('startDate', 'endDate', 'totalRevenue', 'totalOrders', 'bestSellers', 'dailySales'));
    }

    public function export(Request $request): StreamedResponse
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::with('user', 'latestPayment')
            ->whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-penjualan-bookspace-'.date('YmdHis').'.csv"',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nomor Invoice', 'Nama Pelanggan', 'Metode Pembayaran', 'Total Tagihan', 'Tanggal Transaksi', 'Status']);

            $no = 1;
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $no++,
                    $order->code,
                    $order->recipient_name,
                    $order->latestPayment?->methodLabel() ?? 'Manual',
                    $order->total,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->statusLabel(),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
