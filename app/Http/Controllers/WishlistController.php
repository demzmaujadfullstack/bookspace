<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Toggle wishlist status for a book.
     */
    public function toggle(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
            $isWishlisted = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'wishlisted' => $isWishlisted,
                'message' => $isWishlisted
                    ? "'{$book->title}' ditambahkan ke Favorit."
                    : "'{$book->title}' dihapus dari Favorit.",
            ]);
        }

        $message = $isWishlisted
            ? "'{$book->title}' ditambahkan ke Favorit."
            : "'{$book->title}' dihapus dari Favorit.";

        return back()->with('success', $message);
    }

    /**
     * Show the user's wishlist page.
     */
    public function index(Request $request): View
    {
        $wishlistItems = Wishlist::with(['book' => fn ($query) => $query->forCards()])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('user.wishlist', compact('wishlistItems'));
    }
}
