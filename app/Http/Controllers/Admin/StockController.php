<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with('category');

        if ($request->filled('status')) {
            if ($request->status === 'habis') {
                $query->where('stock', 0);
            } elseif ($request->status === 'menipis') {
                $query->where('stock', '>', 0)->where('stock', '<=', Book::STOCK_LOW_THRESHOLD);
            } elseif ($request->status === 'tersedia') {
                $query->where('stock', '>', Book::STOCK_LOW_THRESHOLD);
            }
        }

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('isbn', 'like', $term));
        }

        $books = $query->orderBy('stock')->paginate(15)->withQueryString();

        $stats = [
            'total_books' => Book::count(),
            'out_of_stock' => Book::where('stock', 0)->count(),
            'low_stock' => Book::where('stock', '>', 0)->where('stock', '<=', Book::STOCK_LOW_THRESHOLD)->count(),
            'in_stock' => Book::where('stock', '>', Book::STOCK_LOW_THRESHOLD)->count(),
        ];

        return view('admin.stock.index', compact('books', 'stats'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'stock.required' => 'Jumlah stok baru wajib diisi.',
            'reason.required' => 'Alasan perubahan stok wajib dicantumkan.',
        ]);

        $oldStock = $book->stock;
        $newStock = (int) $request->stock;
        $diff = $newStock - $oldStock;

        if ($diff === 0) {
            return back()->with('error', 'Jumlah stok tidak mengalami perubahan.');
        }

        $book->update(['stock' => $newStock]);

        StockMovement::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            'type' => $diff > 0 ? 'masuk' : 'koreksi',
            'quantity' => $diff,
            'stock_after' => $newStock,
            'reason' => $request->reason,
        ]);

        return back()->with('success', "Stok untuk buku '{$book->title}' berhasil diperbarui menjadi {$newStock} eksemplar.");
    }

    public function history(Book $book): View
    {
        $movements = $book->movements()->with(['user', 'order'])->latest()->paginate(20);

        return view('admin.stock.history', compact('book', 'movements'));
    }
}
