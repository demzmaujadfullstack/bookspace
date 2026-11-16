@extends('layouts.bookspace')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12">

    {{-- ── Checkout Steps Indicator ───────────────────────────── --}}
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between text-xs font-semibold">
            <div class="flex items-center gap-2 text-[#0f2255]">
                <span class="w-7 h-7 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold">1</span>
                <span>Keranjang Belanja</span>
            </div>
            <div class="flex-1 h-0.5 bg-[#e8e6df] mx-4"></div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">2</span>
                <span>Pengiriman &amp; Alamat</span>
            </div>
            <div class="flex-1 h-0.5 bg-[#e8e6df] mx-4"></div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">3</span>
                <span>Pembayaran</span>
            </div>
        </div>
    </div>

    @if(!$cart || $cart->items->isEmpty())
        {{-- Empty State --}}
        <div class="max-w-xl mx-auto bg-white border border-[#e8e6df] rounded-2xl p-12 text-center shadow-sm my-8">
            <div class="w-20 h-20 bg-[#f4f2ec] rounded-full flex items-center justify-center mx-auto mb-5 text-[#0f2255]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h2 class="font-serif-display font-bold text-2xl text-[#0f2255] mb-2">Keranjang Belanja Masih Kosong</h2>
            <p class="text-sm text-gray-500 mb-8 max-w-sm mx-auto leading-relaxed">
                Anda belum memasukkan judul buku apa pun ke dalam keranjang. Mulailah menjelajahi koleksi kami untuk menemukan bacaan berikutnya.
            </p>
            <a href="{{ route('books.index') }}" class="btn-orange text-sm font-semibold px-8 py-3.5 rounded-lg shadow-md">
                Jelajahi Koleksi Buku
            </a>
        </div>
    @else
        @php
            $items = $cart->items;
            $subtotal = $items->sum(fn($item) => $item->subtotal());
            $freeShippingThreshold = (int) \App\Models\Setting::value('free_shipping_threshold', 200000) ?: 200000;
            $remainingForFreeShipping = max(0, $freeShippingThreshold - $subtotal);
            $progressPercent = min(100, round(($subtotal / $freeShippingThreshold) * 100));
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ── Left: Items Table / List (8 cols) ──────────── --}}
            <div class="lg:col-span-8">
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm mb-6">
                    <div class="flex items-center justify-between pb-4 border-b border-[#f0eee6] mb-6">
                        <h1 class="font-serif-display font-bold text-xl text-[#0f2255]">
                            Daftar Buku Pilihan Anda ({{ $items->sum('quantity') }} Item)
                        </h1>
                        <a href="{{ route('books.index') }}" class="text-xs text-[#0f2255] hover:text-[#e05c1a] font-semibold flex items-center gap-1 transition-colors">
                            <span>+ Tambah Buku Lain</span>
                        </a>
                    </div>

                    {{-- Items List --}}
                    <div class="divide-y divide-[#f0eee6]">
                        @foreach($items as $item)
                        <div class="py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="cart-item-{{ $item->id }}">

                            {{-- Book Cover + Info --}}
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <a href="{{ route('books.show', $item->book->slug) }}" class="relative block w-16 h-24 shrink-0 rounded shadow-sm overflow-hidden bg-white border border-[#e8e6df]">
                                    <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book->title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                    <div class="absolute inset-y-0 left-0 w-2 bg-gradient-to-r from-black/20 to-transparent pointer-events-none"></div>
                                </a>

                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#e05c1a] font-bold uppercase tracking-wider block mb-0.5">{{ $item->book->category->name ?? 'Buku' }}</span>
                                    <a href="{{ route('books.show', $item->book->slug) }}" class="font-serif-display font-bold text-sm text-gray-900 hover:text-[#0f2255] transition-colors leading-snug line-clamp-2 block mb-1">
                                        {{ $item->book->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 font-serif italic mb-1.5">{{ $item->book->author }}</p>

                                    {{-- Stock check indicator --}}
                                    @if($item->book->stock === 0)
                                        <span class="inline-block bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded">Stok Habis</span>
                                    @elseif($item->book->stock < $item->quantity)
                                        <span class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded">Melebihi stok (Tersisa {{ $item->book->stock }})</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Quantity Modifier & Price --}}
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-0 border-gray-100">
                                {{-- Qty Form — Alpine.js stepper, avoids @tailwindcss/forms padding bug on type=number --}}
                                <form
                                    method="POST"
                                    action="{{ route('cart.items.update', $item) }}"
                                    class="flex items-center"
                                    x-data="{ qty: {{ $item->quantity }}, max: {{ $item->book->stock }} }"
                                >
                                    @csrf @method('PATCH')
                                    <div class="flex items-center border border-[#dcd8cd] rounded-lg overflow-hidden bg-white shadow-xs">
                                        <button
                                            type="button"
                                            @click="if (qty > 1) { qty--; $nextTick(() => $el.closest('form').submit()); }"
                                            class="w-8 h-9 flex items-center justify-center text-gray-600 hover:bg-[#f4f2ec] hover:text-[#0f2255] font-bold text-base select-none transition-colors"
                                            :disabled="qty <= 1"
                                        >−</button>
                                        <input
                                            type="text"
                                            inputmode="numeric"
                                            name="quantity"
                                            x-model="qty"
                                            @change="qty = Math.max(1, Math.min(max, parseInt(qty) || 1)); $nextTick(() => $el.closest('form').submit());"
                                            class="w-12 h-9 text-center text-sm font-bold text-gray-900 bg-white border-x border-[#dcd8cd] focus:outline-none focus:ring-0 p-0 m-0"
                                            style="box-shadow:none"
                                        >
                                        <button
                                            type="button"
                                            @click="if (qty < max) { qty++; $nextTick(() => $el.closest('form').submit()); }"
                                            class="w-8 h-9 flex items-center justify-center text-gray-600 hover:bg-[#f4f2ec] hover:text-[#0f2255] font-bold text-base select-none transition-colors"
                                            :disabled="qty >= max"
                                        >+</button>
                                    </div>
                                </form>

                                {{-- Price calculation --}}
                                <div class="text-right min-w-[110px]">
                                    <p class="font-serif-display font-bold text-base text-[#0f2255]">
                                        Rp{{ number_format($item->subtotal(), 0, ',', '.') }}
                                    </p>
                                    <p class="text-[11px] text-gray-400">
                                        Rp{{ number_format($item->book->finalPrice(), 0, ',', '.') }}/eks
                                    </p>
                                </div>

                                {{-- Remove item button --}}
                                <form method="POST" action="{{ route('cart.items.destroy', $item) }}" onsubmit="return confirm('Keluarkan buku ini dari keranjang?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors rounded hover:bg-red-50" title="Hapus dari keranjang">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Guarantee Note in Cart --}}
                <div class="bg-[#fafaf8] border border-[#e8e6df] rounded-xl p-4 flex items-center gap-3 text-xs text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span><strong>Ketentuan Stok USK:</strong> Stok buku belum berkurang saat masih di keranjang. Stok hanya akan terpotong secara resmi setelah pembayaran Anda terverifikasi oleh admin.</span>
                </div>
            </div>

            {{-- ── Right: Order Summary Box (4 cols) ──────────── --}}
            <div class="lg:col-span-4">
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sticky top-28 shadow-sm">
                    <h3 class="font-serif-display font-bold text-lg text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                        Ringkasan Belanja
                    </h3>

                    {{-- Free shipping progress bar --}}
                    <div class="bg-[#fafaf8] border border-[#e8e6df] rounded-xl p-3.5 mb-5 text-xs">
                        @if($remainingForFreeShipping > 0)
                            <p class="text-gray-600 mb-2">
                                Belanja <strong class="text-[#e05c1a]">Rp{{ number_format($remainingForFreeShipping, 0, ',', '.') }}</strong> lagi untuk mendapatkan <strong>Gratis Ongkir</strong>!
                            </p>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-[#e05c1a] h-full rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                                <span>🎉 Selamat! Pesanan Anda berhak atas <strong>Gratis Ongkir</strong>.</span>
                            </div>
                        @endif
                    </div>

                    {{-- Cost details --}}
                    <div class="space-y-3 text-xs mb-5">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Buku ({{ $items->sum('quantity') }} eks)</span>
                            <span class="font-semibold text-gray-900 font-mono">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Perkiraan Ongkos Kirim</span>
                            <span class="text-gray-500 italic">Dihitung di checkout</span>
                        </div>
                    </div>

                    <div class="border-t border-[#f0eee6] pt-4 mb-6">
                        <div class="flex items-baseline justify-between">
                            <span class="font-serif-display font-bold text-base text-gray-900">Total Sementara</span>
                            <span class="font-serif-display font-bold text-2xl text-[#e05c1a]">
                                Rp{{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Checkout Button --}}
                    <a href="{{ route('checkout.index') }}" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold py-3.5 px-4 rounded-xl text-center block transition-all shadow-md text-sm tracking-wide">
                        Lanjut ke Alamat Pengiriman &rarr;
                    </a>

                    <a href="{{ route('books.index') }}" class="block w-full text-center text-xs text-gray-500 hover:text-[#0f2255] mt-4 transition-colors font-medium">
                        &larr; Kembali Menjelajah Koleksi
                    </a>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection

