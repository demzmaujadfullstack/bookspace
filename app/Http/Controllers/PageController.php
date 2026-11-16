<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'titleCount' => Book::count(),
            'categoryCount' => Category::count(),
            'reviewCount' => Review::count(),
            'shipmentCount' => Order::whereIn('status', Order::STOCK_REDUCED_STATUSES)->count(),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
