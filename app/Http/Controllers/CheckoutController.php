<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $cart = $user->cart()->with('items.book')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        // Validate stock availability before showing checkout
        foreach ($cart->items as $item) {
            if ($item->book->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "Stok untuk buku '{$item->book->title}' tidak mencukupi (Tersisa: {$item->book->stock}). Silakan sesuaikan jumlah pembelian.");
            }
        }

        $settings = Setting::values();
        $subtotal = $cart->items->sum(fn ($i) => $i->subtotal());
        $addresses = $user->addresses()->orderByDesc('is_default')->orderBy('id')->get();
        $defaultAddress = $addresses->first();

        return view('checkout.index', compact('cart', 'subtotal', 'settings', 'user', 'addresses', 'defaultAddress'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $cart = $user->cart()->with('items.book')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,transfer,qris'],
            'city' => [$request->boolean('save_address') ? 'required' : 'nullable', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'save_address' => ['nullable', 'boolean'],
            'address_label' => ['nullable', 'string', 'max:60'],
        ], [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / telepon penerima wajib diisi.',
            'address.required' => 'Alamat pengiriman lengkap wajib diisi.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'city.required' => 'Isi kota/kabupaten agar alamat ini bisa disimpan ke buku alamat Anda.',
        ]);

        $saveAddress = $request->boolean('save_address');

        return DB::transaction(function () use ($user, $cart, $validated, $saveAddress) {
            // Re-validate stock
            foreach ($cart->items as $item) {
                if ($item->book->stock < $item->quantity) {
                    throw new \Exception("Stok untuk buku '{$item->book->title}' tidak mencukupi (Tersisa: {$item->book->stock}).");
                }
            }

            $subtotal = $cart->items->sum(fn ($i) => $i->subtotal());
            $orderCode = 'BKSP-'.date('Ymd').'-'.strtoupper(Str::random(5));

            // Status depends on method
            // COD starts as pending confirmation from admin
            // Transfer/QRIS starts as menunggu_pembayaran
            $initialStatus = match ($validated['payment_method']) {
                'cod' => Order::MENUNGGU_VERIFIKASI,
                default => Order::MENUNGGU_PEMBAYARAN,
            };

            $order = Order::create([
                'user_id' => $user->id,
                'code' => $orderCode,
                'status' => $initialStatus,
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'total' => $subtotal,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $finalPrice = $item->book->finalPrice();
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'book_title' => $item->book->title,
                    'price' => $finalPrice,
                    'discount_percent' => $item->book->discount_percent,
                    'quantity' => $item->quantity,
                    'subtotal' => $finalPrice * $item->quantity,
                ]);
            }

            // Create initial payment record
            $settings = Setting::values();
            Payment::create([
                'order_id' => $order->id,
                'method' => $validated['payment_method'],
                'amount' => $subtotal,
                'status' => $validated['payment_method'] === 'cod' ? Payment::MENUNGGU_VERIFIKASI : Payment::MENUNGGU,
                'bank_name' => $settings['bank_name'] ?? 'BCA',
                'account_number' => $settings['bank_account_number'] ?? '-',
                'account_holder' => $settings['bank_account_name'] ?? 'BOOKSPACE',
            ]);

            if ($saveAddress) {
                $this->rememberAddress($user, $validated);
            }

            // Clear the cart
            $cart->items()->delete();

            if ($validated['payment_method'] === 'cod') {
                return redirect()->route('checkout.success', $order)->with('success', 'Pesanan COD berhasil dibuat! Menunggu verifikasi admin.');
            }

            return redirect()->route('checkout.payment', $order)->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
        });
    }

    public function payment(Order $order): View|RedirectResponse
    {
        $this->authorizeOrder($order);

        if ($order->status === Order::DIBAYAR || $order->status === Order::SELESAI) {
            return redirect()->route('checkout.success', $order);
        }

        $payment = $order->latestPayment;
        $settings = Setting::values();

        return view('checkout.payment', compact('order', 'payment', 'settings'));
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if (! in_array($order->status, [Order::MENUNGGU_PEMBAYARAN, Order::PENDING])) {
            return back()->with('error', 'Status pesanan tidak memungkinkan pengunggahan bukti pembayaran.');
        }

        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'proof_image.required' => 'Unggah foto bukti pembayaran Anda.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.max' => 'Ukuran gambar maksimal 3MB.',
        ]);

        $path = $request->file('proof_image')->store('payments', 'local');

        $payment = $order->latestPayment ?? new Payment(['order_id' => $order->id, 'method' => 'transfer', 'amount' => $order->total]);
        $payment->proof_image = $path;
        $payment->status = Payment::MENUNGGU_VERIFIKASI;
        if ($request->filled('note')) {
            $payment->note = $request->note;
        }
        $payment->save();

        $order->update(['status' => Order::MENUNGGU_VERIFIKASI]);

        return redirect()->route('checkout.success', $order)->with('success', 'Bukti pembayaran berhasil diunggah! Admin akan segera memverifikasinya.');
    }

    public function success(Order $order): View
    {
        $this->authorizeOrder($order);
        $order->load(['items.book', 'latestPayment']);

        return view('checkout.success', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        if (! $order->canCancel()) {
            return back()->with('error', 'Pesanan ini sudah tidak dapat dibatalkan.');
        }

        $order->update(['status' => Order::DIBATALKAN]);

        if ($payment = $order->latestPayment) {
            $payment->update(['status' => Payment::DITOLAK]);
        }

        return redirect()->route('user.orders')->with('success', "Pesanan #{$order->code} berhasil dibatalkan.");
    }

    /**
     * Simpan alamat checkout ke buku alamat, atau perbarui entri yang teksnya sama.
     *
     * @param  array<string, mixed>  $validated
     */
    private function rememberAddress(User $user, array $validated): void
    {
        $attributes = [
            'label' => $validated['address_label'] ?? null,
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'] ?? null,
        ];

        $existing = $user->addresses()->where('address', $validated['address'])->first();

        if ($existing) {
            $existing->update($attributes);

            return;
        }

        $user->addresses()->create($attributes + ['is_default' => $user->addresses()->count() === 0]);
    }

    private function authorizeOrder(Order $order): void
    {
        /** @var User $user */
        $user = auth()->user();
        abort_unless($order->user_id === $user->id || $user->isAdmin(), 403);
    }
}
