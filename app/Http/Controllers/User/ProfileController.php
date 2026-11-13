<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        ];

        return view('user.dashboard', compact('user', 'recentOrders', 'stats'));
    }

    public function edit(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('user.profile', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', Password::defaults(), 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
