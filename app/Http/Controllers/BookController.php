<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::forCards();

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('author', 'like', $term)->orWhere('isbn', 'like', $term));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->boolean('promo')) {
            $query->where('discount_percent', '>', 0);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->max_price);
        }

        $sortOptions = [
            'newest' => fn ($q) => $q->latest(),
            'price_asc' => fn ($q) => $q->orderBy('price'),
            'price_desc' => fn ($q) => $q->orderByDesc('price'),
            'title_asc' => fn ($q) => $q->orderBy('title'),
        ];

        $sort = $request->get('sort', 'newest');
        ($sortOptions[$sort] ?? $sortOptions['newest'])($query);

        $books = $query->paginate(16)->withQueryString();
        $categories = Category::withCount('books')->orderBy('name')->get();

        return view('books.index', compact('books', 'categories', 'sort'));
    }

    public function show(string $slug): View
    {
        $book = Book::with(['category', 'reviews.user'])->where('slug', $slug)->firstOrFail();

        $related = Book::forCards()
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->inStock()
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'related'));
    }

    public function addToCart(Request $request, Book $book): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $qty = (int) $request->quantity;

        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku ini sudah habis.');
        }

        /** @var User $user */
        $user = auth()->user();

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItem::where('cart_id', $cart->id)->where('book_id', $book->id)->first();

        if ($item) {
            $newQty = $item->quantity + $qty;
            if ($newQty > $book->stock) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia ('.$book->stock.').');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            if ($qty > $book->stock) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia ('.$book->stock.').');
            }
            CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $book->id,
                'quantity' => $qty,
            ]);
        }

        return back()->with('success', '«'.$book->title.'» berhasil ditambahkan ke keranjang.');
    }

    public function searchSuggest(Request $request): JsonResponse
    {
        $query = trim($request->string('q'));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $term = '%'.$query.'%';
        $books = Book::query()
            ->with('category')
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('author', 'like', $term)
                    ->orWhere('isbn', 'like', $term);
            })
            ->limit(6)
            ->get();

        return response()->json($books->map(fn ($b) => [
            'id' => $b->id,
            'title' => $b->title,
            'author' => $b->author,
            'slug' => $b->slug,
            'cover_url' => $b->coverUrl(),
            'category' => $b->category?->name,
            'price_formatted' => 'Rp'.number_format($b->finalPrice(), 0, ',', '.'),
            'original_price_formatted' => 'Rp'.number_format($b->price, 0, ',', '.'),
            'has_discount' => $b->hasDiscount(),
            'discount_percent' => $b->discount_percent,
            'url' => route('books.show', $b->slug),
        ]));
    }
}
