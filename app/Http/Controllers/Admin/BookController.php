<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with('category');

        if ($request->filled('search')) {
            $term = '%'.$request->search.'%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)->orWhere('author', 'like', $term)->orWhere('isbn', 'like', $term));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $books = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:150'],
            'publisher' => ['nullable', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:30', 'unique:books,isbn'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:99'],
            'stock' => ['required', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_featured' => ['nullable', 'boolean'],
        ], [
            'isbn.unique' => 'Nomor ISBN ini sudah terdaftar untuk buku lain.',
            'category_id.required' => 'Pilih kategori buku.',
            'price.required' => 'Harga buku wajib diisi.',
            'stock.required' => 'Stok awal buku wajib diisi.',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books', 'public');
        }

        $slug = Str::slug($validated['title']);
        $counter = 1;
        $orig = $slug;
        while (Book::where('slug', $slug)->exists()) {
            $slug = $orig.'-'.$counter++;
        }

        $book = Book::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'isbn' => $validated['isbn'],
            'year' => $validated['year'] ?? date('Y'),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'stock' => $validated['stock'],
            'cover' => $coverPath,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        // Record initial stock movement
        if ($book->stock > 0) {
            StockMovement::create([
                'book_id' => $book->id,
                'user_id' => auth()->id(),
                'type' => 'masuk',
                'quantity' => $book->stock,
                'stock_after' => $book->stock,
                'reason' => 'Stok awal pendaftaran buku baru',
            ]);
        }

        return redirect()->route('admin.books.index')->with('success', "Buku '{$book->title}' berhasil ditambahkan.");
    }

    public function show(Book $book): View
    {
        $book->load(['category', 'movements.user', 'reviews.user']);

        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:150'],
            'publisher' => ['nullable', 'string', 'max:150'],
            'isbn' => ['required', 'string', 'max:30', Rule::unique('books', 'isbn')->ignore($book->id)],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:99'],
            'stock' => ['required', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = $request->file('cover')->store('books', 'public');
        }

        // Track stock change if modified here
        $oldStock = $book->stock;
        $newStock = (int) $validated['stock'];
        if ($oldStock !== $newStock) {
            $diff = $newStock - $oldStock;
            StockMovement::create([
                'book_id' => $book->id,
                'user_id' => auth()->id(),
                'type' => $diff > 0 ? 'masuk' : 'koreksi',
                'quantity' => $diff,
                'stock_after' => $newStock,
                'reason' => 'Perubahan stok manual via Edit Buku oleh Admin',
            ]);
        }

        if ($book->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $counter = 1;
            $orig = $slug;
            while (Book::where('slug', $slug)->where('id', '!=', $book->id)->exists()) {
                $slug = $orig.'-'.$counter++;
            }
            $book->slug = $slug;
        }

        $book->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'author' => $validated['author'],
            'publisher' => $validated['publisher'] ?? null,
            'isbn' => $validated['isbn'],
            'year' => $validated['year'] ?? date('Y'),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'stock' => $newStock,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.books.index')->with('success', "Buku '{$book->title}' berhasil diperbarui.");
    }

    public function destroy(Book $book): RedirectResponse
    {
        // Check if book exists in completed orders
        if ($book->orderItems()->count() > 0) {
            return back()->with('error', "Buku '{$book->title}' tidak dapat dihapus karena tercatat dalam riwayat transaksi pesanan.");
        }

        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $title = $book->title;
        $book->delete();

        return redirect()->route('admin.books.index')->with('success', "Buku '{$title}' berhasil dihapus.");
    }
}
