<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $this->validateReview($request);

        $user = $request->user();

        // Check if user already reviewed this book
        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk buku ini.');
        }

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil diterbitkan.');
    }

    /**
     * Update the owner's existing review.
     */
    public function update(Request $request, Book $book, Review $review): RedirectResponse
    {
        $user = $request->user();

        abort_unless($review->user_id === $user->id, 403, 'Anda tidak dapat mengubah ulasan ini.');
        abort_unless($review->book_id === $book->id, 404);

        $validated = $this->validateReview($request);

        $review->update($validated);

        return back()->with('success', 'Ulasan Anda berhasil diperbarui.');
    }

    /**
     * Remove a review — its owner or any admin.
     */
    public function destroy(Request $request, Book $book, Review $review): RedirectResponse
    {
        $user = $request->user();

        abort_unless($review->user_id === $user->id || $user->isAdmin(), 403, 'Anda tidak dapat menghapus ulasan ini.');
        abort_unless($review->book_id === $book->id, 404);

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    /**
     * @return array{rating: int, comment: string}
     */
    private function validateReview(Request $request): array
    {
        return $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rating.required' => 'Silakan pilih rating bintang (1-5).',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Ulasan tidak boleh kosong.',
            'comment.min' => 'Ulasan minimal 5 karakter.',
            'comment.max' => 'Ulasan maksimal 500 karakter.',
        ]);
    }
}
