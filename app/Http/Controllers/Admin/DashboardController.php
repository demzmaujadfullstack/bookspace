<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $revenue = (int) Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)->sum('total');

        $monthStart = Carbon::now()->startOfMonth();
        $monthRevenue = (int) Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->whereBetween('created_at', [$monthStart, Carbon::now()->endOfMonth()])
            ->sum('total');

        $pendingOrders = Order::whereIn('status', [Order::PENDING, Order::MENUNGGU_PEMBAYARAN, Order::MENUNGGU_VERIFIKASI])->count();
        $lowStockBooks = Book::with('category')->where('stock', '<=', Book::STOCK_LOW_THRESHOLD)->orderBy('stock')->limit(6)->get();
        $recentOrders = Order::with(['user', 'latestPayment'])->latest()->limit(8)->get();

        // Penjualan 30 hari terakhir: satu query grouped, lalu diisi tanggal yang kosong dengan 0.
        $since = Carbon::today()->subDays(29);
        $salesByDate = Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date, sum(total) as amount')
            ->groupBy('date')
            ->pluck('amount', 'date');

        $days = collect();
        $sales = collect();
        for ($date = $since->copy(); $date->lte(Carbon::today()); $date->addDay()) {
            $days->push($date->format('d M'));
            $sales->push((int) ($salesByDate[$date->toDateString()] ?? 0));
        }

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalCategories',
            'totalUsers',
            'totalOrders',
            'revenue',
            'monthRevenue',
            'pendingOrders',
            'lowStockBooks',
            'recentOrders',
            'days',
            'sales'
        ));
    }
}
