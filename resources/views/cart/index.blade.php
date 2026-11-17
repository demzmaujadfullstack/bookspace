@extends('layouts.bookspace')

@section('title', 'Keranjang Belanja')

@section('content')
@php
    $items = $cart?->items ?? collect();
    $subtotal = $items->sum(fn ($item) => $item->subtotal());
    $totalQty = (int) $items->sum('quantity');
    $freeShipThreshold = (int) \App\Models\Setting::value('free_shipping_threshold', 200000) ?: 200000;
    $remaining = max(0, $freeShipThreshold - $subtotal);
    $progress = min(100, (int) round(($subtotal / $freeShipThreshold) * 100));
@endphp

<div class="shell py-6 md:py-8">

    <x-checkout-steps :step="1" />

    @if($items->isEmpty())
        <div class="card mx-auto max-w-xl !p-10 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f4f2ec] text-[#0f2255]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h1 class="page-title mt-5">Keranjang Anda Masih Kosong</h1>
            <p class="muted mx-auto mt-2 max-w-sm">Belum ada judul yang disimpan. Jelajahi koleksi kami untuk menemukan bacaan berikutnya.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5">
                <a href="{{ route('books.index') }}" class="btn-primary">Jelajahi Semua Buku</a>
                <a href="{{ route('books.index', ['promo' => 1]) }}" class="btn-ghost">Lihat Buku Diskon</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

            {{-- Daftar item --}}
            <div class="lg:col-span-8">
                <div class="card overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                        <h1 class="section-title !text-xl">Keranjang Belanja</h1>
                        <span class="text-[13px] text-[#6b6f78]">{{ $totalQty }} eksemplar dari {{ $items->count() }} judul</span>
                    </div>

                    <ul class="divide-y divide-[#f0eee6]">
                        @foreach($items as $item)
                            <li class="flex gap-4 px-5 py-4">
                                <a href="{{ route('books.show', $item->book->slug) }}" class="relative block h-24 w-16 shrink-0 overflow-hidden rounded border border-[#e6e3db] bg-white">
                                    <img src="{{ $item->book->coverUrl() }}" alt="Sampul {{ $item->book->title }}" class="h-full w-full object-cover" loading="lazy"
                                         onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 w-1.5 bg-gradient-to-r from-black/20 to-transparent"></span>
                                </a>

                                <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <span class="eyebrow">{{ $item->book->category->name ?? 'Buku' }}</span>
                                        <a href="{{ route('books.show', $item->book->slug) }}"
                                           class="mt-1 block font-serif-display text-[15px] font-bold leading-snug text-[#0f2255] transition-colors hover:text-[#e05c1a]">
                                            {{ $item->book->title }}
                                        </a>
                                        <p class="mt-0.5 text-[12px] text-[#6b6f78]">{{ $item->book->author }}</p>

                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            <span class="text-[13px] font-semibold text-[#17181c]">Rp{{ number_format($item->book->finalPrice(), 0, ',', '.') }}</span>
                                            @if($item->book->hasDiscount())
                                                <span class="text-[12px] text-[#a8a498] line-through">Rp{{ number_format($item->book->price, 0, ',', '.') }}</span>
                                                <span class="pill bg-[#fdece2] font-bold text-[#c54e12]">-{{ $item->book->discount_percent }}%</span>
                                            @endif
                                        </div>

                                        @if($item->book->stock === 0)
                                            <p class="mt-2 text-[12px] font-semibold text-[#b42318]">Stok habis — keluarkan item ini untuk melanjutkan.</p>
                                        @elseif($item->book->stock < $item->quantity)
                                            <p class="mt-2 text-[12px] font-semibold text-[#c54e12]">Melebihi stok, sisa {{ $item->book->stock }} eksemplar.</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:gap-2">
                                        <form method="POST" action="{{ route('cart.items.update', $item) }}" x-data="{ qty: {{ $item->quantity }}, max: {{ max($item->book->stock, 1) }} }">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex h-9 items-center overflow-hidden rounded-lg border border-[#dcd8cd] bg-white">
                                                <button type="button" @click="if (qty > 1) { qty--; $el.closest('form').submit(); }" :disabled="qty <= 1"
                                                        class="grid h-full w-8 place-items-center text-base text-[#3f4451] hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Kurangi jumlah {{ $item->book->title }}">&minus;</button>
                                                <input type="text" inputmode="numeric" name="quantity" x-model="qty" aria-label="Jumlah {{ $item->book->title }}"
                                                       @change="qty = Math.max(1, Math.min(max, parseInt(qty) || 1)); $el.closest('form').submit();"
                                                       class="h-full w-10 border-x border-[#dcd8cd] text-center text-[13px] font-bold text-[#0f2255] focus:outline-none" style="box-shadow:none">
                                                <button type="button" @click="if (qty < max) { qty++; $el.closest('form').submit(); }" :disabled="qty >= max"
                                                        class="grid h-full w-8 place-items-center text-base text-[#3f4451] hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Tambah jumlah {{ $item->book->title }}">+</button>
                                            </div>
                                        </form>

                                        <p class="text-right font-serif-display text-[15px] font-bold text-[#0f2255]">Rp{{ number_format($item->subtotal(), 0, ',', '.') }}</p>

                                        <form method="POST" action="{{ route('cart.items.destroy', $item) }}" onsubmit="return confirm('Keluarkan buku ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#9a9484] transition-colors hover:text-[#b42318]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4h6v3m-8 0l1 13h8l1-13"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="border-t border-[#e6e3db] bg-[#fafaf8] px-5 py-3.5">
                        <a href="{{ route('books.index') }}" class="text-[13px] font-semibold text-[#0f2255] hover:underline">&larr; Tambah buku lain dari katalog</a>
                    </div>
                </div>

                <p class="muted mt-4 flex items-start gap-2 rounded-lg border border-[#e6e3db] bg-white px-4 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-[#0f2255]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Stok belum berkurang selama buku berada di keranjang. Jumlah baru dipotong setelah pembayaran pesanan Anda terverifikasi oleh admin.
                </p>
            </div>

            {{-- Ringkasan --}}
            <div class="lg:col-span-4">
                <div class="card sticky top-[108px] overflow-hidden">
                    <h2 class="border-b border-[#e6e3db] px-5 py-4 font-serif-display text-[17px] font-bold text-[#0f2255]">Ringkasan Belanja</h2>

                    <div class="px-5 pt-4">
                        @if($remaining > 0)
                            <p class="text-[12px] text-[#3f4451]">Tambah Rp{{ number_format($remaining, 0, ',', '.') }} lagi untuk <strong class="text-[#c54e12]">gratis ongkir</strong>.</p>
                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eae7de]">
                                <div class="h-full rounded-full bg-[#e05c1a] transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        @else
                            <p class="flex items-center gap-2 rounded-lg bg-[#eaf5ee] px-3 py-2 text-[12px] font-semibold text-emerald-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Total belanja Anda sudah memenuhi syarat gratis ongkir.
                            </p>
                        @endif
                    </div>

                    <dl class="space-y-2.5 px-5 py-4 text-[13px]">
                        <div class="flex justify-between">
                            <dt class="text-[#6b6f78]">Subtotal ({{ $totalQty }} eks)</dt>
                            <dd class="font-semibold text-[#17181c]">Rp{{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        @if($items->contains(fn ($item) => $item->book->hasDiscount()))
                            <div class="flex justify-between">
                                <dt class="text-[#6b6f78]">Total potongan</dt>
                                <dd class="font-semibold text-[#c54e12]">-Rp{{ number_format($items->sum(fn ($item) => ($item->book->price - $item->book->finalPrice()) * $item->quantity), 0, ',', '.') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-[#6b6f78]">Ongkos kirim</dt>
                            <dd class="text-[#9a9484]">Dihitung di langkah berikutnya</dd>
                        </div>
                    </dl>

                    <div class="flex items-baseline justify-between border-t border-[#e6e3db] px-5 py-4">
                        <span class="text-[13px] font-semibold text-[#17181c]">Total sementara</span>
                        <span class="font-serif-display text-[22px] font-bold text-[#0f2255]">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5 px-5 pb-5">
                        <a href="{{ route('checkout.index') }}" class="btn-primary w-full !py-3">Lanjut ke Pengiriman</a>
                        <a href="{{ route('wishlist.index') }}" class="block text-center text-[12px] font-semibold text-[#6b6f78] transition-colors hover:text-[#0f2255]">Pindah dari daftar favorit</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
