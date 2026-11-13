<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $cart = $user->cart()->with('items.book.category')->first();

        return view('cart.index', compact('cart'));
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $qty = (int) $request->quantity;

        if ($qty > $item->book->stock) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: '.$item->book->stock);
        }

        $item->update([
            'quantity' => $qty,
        ]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);
        $item->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    private function authorizeItem(CartItem $item): void
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($item->cart->user_id === $user->id, 403);
    }
}
