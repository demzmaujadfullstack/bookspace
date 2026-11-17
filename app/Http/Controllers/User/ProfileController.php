<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function dashboard(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $recentOrders = $user->orders()
            ->with(['items.book', 'latestPayment'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', [Order::PENDING, Order::MENUNGGU_PEMBAYARAN, Order::MENUNGGU_VERIFIKASI])->count(),
            'completed_orders' => $user->orders()->where('status', Order::SELESAI)->count(),
            'total_spent' => (int) $user->orders()->whereIn('status', Order::STOCK_REDUCED_STATUSES)->sum('total'),
        ];

        $recommended = Book::forCards()
            ->where('discount_percent', '>', 0)
            ->inStock()
            ->latest()
            ->take(4)
            ->get();

        return view('user.dashboard', compact('user', 'recentOrders', 'stats', 'recommended'));
    }

    public function edit(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $addresses = $user->addresses()->orderByDesc('is_default')->orderBy('id')->get();

        return view('user.profile', compact('user', 'addresses'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email belum benar.',
            'email.unique' => 'Email tersebut sudah dipakai akun lain.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Kata sandi Anda berhasil diganti.');
    }
}
