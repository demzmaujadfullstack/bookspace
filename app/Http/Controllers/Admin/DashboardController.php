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
        $monthRevenue = (int) Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');
        $pendingOrders = Order::whereIn('status', [Order::PENDING, Order::MENUNGGU_PEMBAYARAN, Order::MENUNGGU_VERIFIKASI])->count();
        $lowStockBooks = Book::where('stock', '<=', Book::STOCK_LOW_THRESHOLD)->orderBy('stock')->limit(6)->get();
        $recentOrders = Order::with(['user', 'latestPayment'])->latest()->limit(8)->get();

        // 30-day sales data for chart
        $days = collect();
        $sales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days->push($date->format('d M'));
            $sum = Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)
                ->whereDate('created_at', $date)
                ->sum('total');
            $sales->push((int) $sum);
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
