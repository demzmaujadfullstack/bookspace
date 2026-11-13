<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banners = Banner::where('is_active', true)->orderBy('sort_order')->get();

        $categories = Category::navList()->sortByDesc('books_count')->values();

        $bestSellers = Book::forCards()->withSoldCount()->orderByDesc('sold_count')->limit(8)->get();

        $newBooks = Book::forCards()->latest()->limit(8)->get();

        $featured = Book::forCards()->where('is_featured', true)->inStock()->limit(8)->get();

        $promoBooks = Book::forCards()->where('discount_percent', '>', 0)->orderByDesc('discount_percent')->limit(8)->get();

        $reviews = Review::with('user', 'book')->latest()->limit(6)->get();

        return view('home', compact('banners', 'categories', 'bestSellers', 'newBooks', 'featured', 'promoBooks', 'reviews'));
    }
}
