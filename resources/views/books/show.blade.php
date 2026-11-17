@extends('layouts.bookspace')

@section('title', $book->title . ' — ' . $book->author)
@section('meta_description', Str::limit($book->description, 160))

@php
    $rating = $book->averageRating();
    $reviewCount = (int) $book->reviews_count;
    $soldCount = $book->soldCount();
    $finalPrice = $book->finalPrice();
    $isWishlisted = auth()->check() && auth()->user()->hasWishlisted($book->id);
    $isLowStock = $book->stock > 0 && $book->stock <= \App\Models\Book::STOCK_LOW_THRESHOLD;
    $freeShip = (int) \App\Models\Setting::value('free_shipping_threshold', 75000);
@endphp

@section('content')
<div class="shell py-5 pb-28 md:py-7 lg:pb-14" x-data="{ qty: 1, max: {{ max($book->stock, 1) }}, previewOpen: false, tab: 'sinopsis' }">

    <x-breadcrumb class="mb-6" :items="[
        'Beranda' => route('home'),
        'Katalog' => route('books.index'),
        $book->category->name ?? 'Kategori' => $book->category ? route('books.index', ['category' => $book->category->slug]) : null,
        Str::limit($book->title, 42) => null,
    ]" />

    {{-- ── Ringkasan produk ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

        {{-- Sampul --}}
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="space-y-4 lg:sticky lg:top-[108px]">
                <div class="relative rounded-xl border border-[#e6e3db] bg-[#f4f2ec] p-5 sm:p-8">
                    <div class="relative mx-auto aspect-[2/3] w-full max-w-[190px] overflow-hidden rounded-md bg-white shadow-[0_20px_44px_-20px_rgba(11,23,54,.55)] sm:max-w-[230px] lg:max-w-[260px]">
                        <img src="{{ $book->coverUrl() }}" alt="Sampul buku {{ $book->title }}" class="h-full w-full object-cover"
                             onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                        <div class="pointer-events-none absolute inset-y-0 left-0 w-4 bg-gradient-to-r from-black/35 via-black/15 to-transparent"></div>
                        <div class="pointer-events-none absolute inset-y-0 right-0 w-px bg-white/40"></div>
                    </div>

                    @if($book->hasDiscount())
                        <span class="pill absolute left-4 top-4 bg-[#e05c1a] font-bold text-white">-{{ $book->discount_percent }}%</span>
                    @endif
                </div>

                <div class="card space-y-2.5 !p-4 text-[12px] text-[#3f4451]">
                    <div class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-[#0f2255]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>100% buku asli, dipasok langsung dari penerbit dan distributor resmi.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-[#0f2255]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Dikemas karton tebal dan bubble wrap sebelum diserahkan ke kurir.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-[#0f2255]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Bebas ongkir untuk total belanja di atas Rp{{ number_format($freeShip, 0, ',', '.') }}.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi dan pembelian --}}
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="flex flex-wrap items-center gap-2 text-[12px]">
                @if($book->category)
                    <a href="{{ route('books.index', ['category' => $book->category->slug]) }}"
                       class="pill bg-[#f4f2ec] font-semibold text-[#0f2255] transition-colors hover:bg-[#e8e6df]">{{ $book->category->name }}</a>
                @endif
                @if($book->year)
                    <span class="pill bg-[#f4f2ec] text-[#6b6f78]">Terbit {{ $book->year }}</span>
                @endif
                @if($soldCount > 0)
                    <span class="pill bg-[#f4f2ec] text-[#6b6f78]">{{ number_format($soldCount, 0, ',', '.') }} terjual</span>
                @endif
            </div>

            <h1 class="mt-3 font-serif-display text-[26px] font-bold leading-tight text-[#0f2255] sm:text-[32px]">{{ $book->title }}</h1>

            <p class="mt-1.5 text-sm text-[#6b6f78]">
                oleh <span class="font-semibold text-[#17181c]">{{ $book->author }}</span>
                @if($book->publisher)
                    &middot; {{ $book->publisher }}
                @endif
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[13px]">
                @if($rating > 0)
                    <div class="flex items-center gap-1 text-[#e0a80c]">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($rating) ? 'fill-current stroke-current' : 'fill-transparent text-[#d8d4c8] stroke-current' }}" viewBox="0 0 24 24" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        @endfor
                    </div>
                    <span class="font-bold text-[#17181c]">{{ number_format($rating, 1) }}</span>
                    <button type="button" @click="tab = 'ulasan'; document.getElementById('book-tabs')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                            class="text-[#6b6f78] underline decoration-[#d8d4c8] underline-offset-2 transition-colors hover:text-[#0f2255]">{{ $reviewCount }} ulasan</button>
                @else
                    <span class="text-[#9a9484]">Belum ada ulasan</span>
                @endif
            </div>

            {{-- Harga dan aksi pembelian --}}
            <div class="mt-6 card overflow-hidden">
                <div class="border-b border-[#e6e3db] bg-[#fafaf8] px-5 py-4">
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <span class="font-serif-display text-[28px] font-bold {{ $book->hasDiscount() ? 'text-[#e05c1a]' : 'text-[#0f2255]' }}">
                            Rp{{ number_format($finalPrice, 0, ',', '.') }}
                        </span>
                        @if($book->hasDiscount())
                            <span class="text-[15px] text-[#a8a498] line-through">Rp{{ number_format($book->price, 0, ',', '.') }}</span>
                            <span class="pill bg-[#fdece2] font-bold text-[#c54e12]">Hemat {{ $book->discount_percent }}%</span>
                        @endif
                    </div>

                    <div class="mt-2 flex items-center gap-2 text-[12px] font-semibold">
                        @if($book->stock === 0)
                            <span class="h-2 w-2 rounded-full bg-[#b42318]"></span>
                            <span class="text-[#b42318]">Stok habis — buku sedang tidak bisa dipesan</span>
                        @elseif($isLowStock)
                            <span class="h-2 w-2 animate-pulse rounded-full bg-[#e05c1a]"></span>
                            <span class="text-[#c54e12]">Sisa {{ $book->stock }} eksemplar, cepat habis</span>
                        @else
                            <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                            <span class="text-emerald-700">Stok tersedia ({{ $book->stock }} eksemplar siap kirim)</span>
                        @endif
                    </div>
                </div>

                <div class="p-5">
                    @if($book->stock > 0)
                        @auth
                            <form method="POST" action="{{ route('books.cart.add', $book) }}" id="buy-form" class="space-y-4">
                                @csrf
                                <div class="flex items-center gap-3">
                                    <span class="text-[12px] font-semibold uppercase tracking-wide text-[#3f4451]">Jumlah</span>
                                    <div class="flex h-11 items-center overflow-hidden rounded-lg border border-[#dcd8cd] bg-white">
                                        <button type="button" @click="if (qty > 1) qty--" :disabled="qty <= 1"
                                                class="grid h-full w-10 place-items-center text-lg text-[#3f4451] transition-colors hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Kurangi jumlah">&minus;</button>
                                        <input type="text" inputmode="numeric" name="quantity" x-model="qty"
                                               @change="qty = Math.max(1, Math.min(max, parseInt(qty) || 1))"
                                               aria-label="Jumlah eksemplar"
                                               class="h-full w-14 border-x border-[#dcd8cd] text-center text-sm font-bold text-[#0f2255] focus:outline-none" style="box-shadow:none">
                                        <button type="button" @click="if (qty < max) qty++" :disabled="qty >= max"
                                                class="grid h-full w-10 place-items-center text-lg text-[#3f4451] transition-colors hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Tambah jumlah">+</button>
                                    </div>
                                    <span class="text-[12px] text-[#9a9484]">maks {{ $book->stock }}</span>
                                </div>

                                <div class="flex flex-col gap-2.5 sm:flex-row">
                                    <button type="submit" class="btn-primary flex-1 !py-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        <span>Tambah ke Keranjang</span>
                                    </button>
                                    <a href="{{ route('cart.index') }}" class="btn-ghost sm:w-44">Lihat Keranjang</a>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary w-full !py-3">Masuk untuk Memesan</a>
                            <p class="mt-2.5 text-[12px] text-[#6b6f78]">Anda perlu masuk untuk menyimpan buku ke keranjang atau daftar favorit.</p>
                        @endauth

                        <div class="mt-4 flex flex-wrap items-center gap-2.5 border-t border-[#f0eee6] pt-4">
                            @auth
                                <form method="POST" action="{{ route('wishlist.toggle', $book) }}">
                                    @csrf
                                    <button type="submit"
                                            class="pill border transition-colors {{ $isWishlisted ? 'border-[#f3c9c2] bg-[#fdf1ef] text-[#b42318]' : 'border-[#dcd8cd] bg-white text-[#3f4451] hover:border-[#b42318] hover:text-[#b42318]' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        <span>{{ $isWishlisted ? 'Tersimpan di Favorit' : 'Simpan ke Favorit' }}</span>
                                    </button>
                                </form>
                            @endauth
                            <button type="button" @click="previewOpen = true" class="pill border border-[#dcd8cd] bg-white text-[#3f4451] transition-colors hover:border-[#0f2255] hover:text-[#0f2255]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#e05c1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Baca Cuplikan</span>
                            </button>
                        </div>
                    @else
                        <div class="rounded-lg border border-dashed border-[#dcd8cd] bg-[#fafaf8] p-4 text-center">
                            <p class="text-sm font-semibold text-[#3f4451]">Stok buku ini sedang habis.</p>
                            <p class="muted mt-1">Simpan ke favorit agar mudah menemukannya lagi saat stok bertambah.</p>
                            @auth
                                <form method="POST" action="{{ route('wishlist.toggle', $book) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn-ghost btn-sm">{{ $isWishlisted ? 'Buka dari Favorit' : 'Simpan ke Favorit' }}</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn-ghost btn-sm mt-3">Masuk untuk menyimpan</a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>

            {{-- Identitas buku --}}
            <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-3.5 border-t border-[#e6e3db] pt-5 text-[13px] sm:grid-cols-3">
                <div>
                    <dt class="eyebrow">ISBN</dt>
                    <dd class="mt-1 font-mono text-[#17181c]">{{ $book->isbn ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Penerbit</dt>
                    <dd class="mt-1 text-[#17181c]">{{ $book->publisher ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Tahun Terbit</dt>
                    <dd class="mt-1 text-[#17181c]">{{ $book->year ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Kategori</dt>
                    <dd class="mt-1 text-[#17181c]">{{ $book->category->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Pengarang</dt>
                    <dd class="mt-1 text-[#17181c]">{{ $book->author }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Status Stok</dt>
                    <dd class="mt-1 text-[#17181c]">{{ $book->stockStatus() }} ({{ $book->stock }})</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- ── Tabs: sinopsis / detail / ulasan ───────────────────── --}}
    @php
        $tabs = [
            'sinopsis' => 'Sinopsis',
            'detail' => 'Detail Buku',
            'ulasan' => 'Ulasan ('.$reviewCount.')',
        ];
    @endphp
    <div id="book-tabs" class="mt-12 card overflow-hidden">
        <div role="tablist" aria-label="Informasi buku" class="chip-rail flex gap-1 overflow-x-auto border-b border-[#e6e3db] bg-white px-2 sm:px-3">
            @foreach($tabs as $key => $label)
                <button type="button" role="tab" @click="tab = '{{ $key }}'"
                        class="shrink-0 border-b-2 px-3.5 py-3 text-[13px] font-semibold transition-colors"
                        :class="tab === '{{ $key }}' ? 'border-[#e05c1a] text-[#0f2255]' : 'border-transparent text-[#6b6f78] hover:text-[#0f2255]'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Sinopsis --}}
        <div x-show="tab === 'sinopsis'" x-cloak class="p-5 sm:p-8">
            <div class="max-w-3xl font-serif-display text-[15px] leading-[1.85] text-[#2c3038] sm:text-base">
                {!! nl2br(e($book->description)) !!}
            </div>

            @if($book->hasDiscount())
                <div class="mt-7 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#f3d9c6] bg-[#fdf5ef] px-4 py-3">
                    <p class="text-[13px] font-semibold text-[#8a3d0c]">Judul ini sedang didiskon {{ $book->discount_percent }}% — hemat Rp{{ number_format($book->price - $finalPrice, 0, ',', '.') }} per eksemplar.</p>
                    <a href="{{ route('books.index', ['promo' => 1]) }}" class="text-[12px] font-bold text-[#c54e12] hover:underline">Lihat semua promo &rarr;</a>
                </div>
            @endif
        </div>

        {{-- Detail --}}
        <div x-show="tab === 'detail'" x-cloak class="p-5 sm:p-8">
            <dl class="grid grid-cols-1 gap-x-10 text-[13px] sm:grid-cols-2">
                @php
                    $specs = [
                        'Judul' => $book->title,
                        'Pengarang' => $book->author,
                        'Penerbit' => $book->publisher ?: '—',
                        'ISBN' => $book->isbn ?: '—',
                        'Tahun terbit' => $book->year ?: '—',
                        'Kategori' => $book->category->name ?? '—',
                        'Harga cetak' => $book->hasDiscount() ? 'Rp'.number_format($book->price, 0, ',', '.') : '—',
                        'Harga jual' => 'Rp'.number_format($finalPrice, 0, ',', '.'),
                        'Stok tersedia' => (string) $book->stock,
                        'Total terjual' => number_format($soldCount, 0, ',', '.').' eksemplar',
                    ];
                @endphp
                @foreach($specs as $label => $value)
                    <div class="flex justify-between gap-4 border-b border-[#f0eee6] py-2.5">
                        <dt class="text-[#6b6f78]">{{ $label }}</dt>
                        <dd class="text-right font-semibold text-[#17181c]">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Ulasan --}}
        <div x-show="tab === 'ulasan'" x-cloak class="p-5 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#f0eee6] pb-5">
                <div class="flex items-end gap-3">
                    <span class="font-serif-display text-4xl font-bold text-[#0f2255]">{{ number_format($rating, 1) }}</span>
                    <div class="pb-1 text-[12px] text-[#6b6f78]">
                        <div class="flex items-center gap-0.5 text-[#e0a80c]">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-3.5 w-3.5 {{ $i <= round($rating) ? 'fill-current stroke-current' : 'fill-transparent text-[#d8d4c8] stroke-current' }}" viewBox="0 0 24 24" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            @endfor
                        </div>
                        <span>Dari {{ $reviewCount }} ulasan pembaca</span>
                    </div>
                </div>
                @if($book->category)
                    <a href="{{ route('books.index', ['category' => $book->category->slug, 'sort' => 'bestseller']) }}"
                       class="text-[12px] font-semibold text-[#0f2255] hover:underline">Lihat buku terlaris lain</a>
                @endif
            </div>

            @auth
                @if($userReview)
                    <div class="mt-6" x-data="{ editing: {{ $errors->has('rating') || $errors->has('comment') ? 'true' : 'false' }} }" @cancel-review-edit="editing = false">
                        <div x-show="!editing" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#e6e3db] bg-[#fafaf8] px-4 py-3 text-[13px] text-[#3f4451]">
                            <span>Ulasan Anda saat ini: <strong class="text-[#0f2255]">{{ $userReview->rating }} bintang</strong>. Terima kasih sudah berbagi.</span>
                            <div class="flex shrink-0 items-center gap-3 text-[12px] font-semibold">
                                <button type="button" @click="editing = true" class="text-[#0f2255] hover:underline">Ubah ulasan</button>
                                <form method="POST" action="{{ route('books.reviews.destroy', [$book, $userReview]) }}" onsubmit="return confirm('Hapus ulasan Anda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#b42318] hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <div x-show="editing" x-cloak class="mt-4">
                            <x-review-form :book="$book" :review="$userReview" />
                        </div>
                    </div>
                @else
                    <div class="mt-6">
                        <x-review-form :book="$book" />
                    </div>
                @endif
            @else
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#e6e3db] bg-[#fafaf8] px-4 py-3 text-[13px] text-[#3f4451]">
                    <span>Bagikan kesan Anda setelah buku sampai di rumah.</span>
                    <a href="{{ route('login') }}" class="font-semibold text-[#0f2255] hover:underline">Masuk untuk menulis ulasan &rarr;</a>
                </div>
            @endauth

            @if($reviews->isNotEmpty())
                <div class="mt-7 space-y-6">
                    @foreach($reviews as $rev)
                        <div class="flex items-start gap-3.5 border-b border-[#f0eee6] pb-6 last:border-0 last:pb-0">
                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[12px] font-bold text-white">
                                {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                    <h4 class="text-[13px] font-bold text-[#17181c]">{{ $rev->user->name ?? 'Pembaca' }}</h4>
                                    <div class="flex items-center gap-0.5 text-[#e0a80c]">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3.5 w-3.5 {{ $i <= $rev->rating ? 'fill-current stroke-current' : 'fill-transparent text-[#d8d4c8] stroke-current' }}" viewBox="0 0 24 24" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[11px] text-[#9a9484]">{{ $rev->created_at->translatedFormat('d M Y') }}</span>
                                    @if(auth()->check() && auth()->user()->isAdmin() && $rev->user_id !== auth()->id())
                                        <form method="POST" action="{{ route('books.reviews.destroy', [$book, $rev]) }}" class="ml-auto" onsubmit="return confirm('Hapus ulasan ini sebagai admin?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[11px] font-semibold text-[#b42318] hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                                <p class="mt-1.5 text-[14px] leading-relaxed text-[#3f4451]">{{ $rev->comment }}</p>
                            </div>
                        </div>
                    @endforeach

                    @if($reviews->hasPages())
                        <div class="border-t border-[#f0eee6] pt-5">{{ $reviews->links() }}</div>
                    @endif
                </div>
            @else
                <p class="py-8 text-center text-[13px] text-[#9a9484]">Belum ada ulasan untuk judul ini. Jadilah pembaca pertama yang menuliskan kesan Anda.</p>
            @endif
        </div>
    </div>

    {{-- ── Rak buku terkait ───────────────────────────────────── --}}
    @if($related->isNotEmpty())
        <section class="mt-12">
            <div class="mb-5 flex items-end justify-between gap-4 border-b border-[#e6e3db] pb-3">
                <div>
                    <span class="eyebrow">Dari kategori yang sama</span>
                    <h2 class="section-title mt-1">Buku Serupa</h2>
                </div>
                @if($book->category)
                    <a href="{{ route('books.index', ['category' => $book->category->slug]) }}" class="text-[13px] font-semibold text-[#0f2255] hover:underline">Semua {{ $book->category->name }}</a>
                @endif
            </div>

            <x-book-shelf :books="$related" />
        </section>
    @endif

    {{-- ── Bilah beli tempel (mobile) ─────────────────────────── --}}
    @auth
        @if($book->stock > 0)
            <div class="fixed inset-x-0 bottom-0 z-30 border-t border-[#e6e3db] bg-white/95 px-4 py-3 backdrop-blur no-print lg:hidden">
                <div class="mx-auto flex max-w-[1400px] items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[12px] font-semibold text-[#6b6f78]">{{ Str::limit($book->title, 24) }}</p>
                        <p class="font-serif-display text-[17px] font-bold {{ $book->hasDiscount() ? 'text-[#e05c1a]' : 'text-[#0f2255]' }}">Rp{{ number_format($finalPrice, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-10 shrink-0 items-center overflow-hidden rounded-lg border border-[#dcd8cd]">
                        <button type="button" @click="if (qty > 1) qty--" :disabled="qty <= 1" class="grid h-full w-8 place-items-center text-base hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Kurangi jumlah">&minus;</button>
                        <span class="w-8 text-center text-sm font-bold text-[#0f2255]" x-text="qty"></span>
                        <button type="button" @click="if (qty < max) qty++" :disabled="qty >= max" class="grid h-full w-8 place-items-center text-base hover:bg-[#f4f2ec] disabled:opacity-35" aria-label="Tambah jumlah">+</button>
                    </div>
                    <form method="POST" action="{{ route('books.cart.add', $book) }}" class="shrink-0">
                        @csrf
                        <input type="hidden" name="quantity" :value="qty">
                        <button type="submit" class="btn-primary !px-4 !py-2.5 text-[13px]">Tambah</button>
                    </form>
                </div>
            </div>
        @endif
    @endauth

    {{-- ── Modal cuplikan ──────────────────────────────────────── --}}
    <div x-show="previewOpen" x-cloak @keydown.escape.window="previewOpen = false"
         class="fixed inset-0 z-50 flex items-end justify-center bg-black/55 sm:items-center sm:p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="previewOpen = false"
             class="flex max-h-[88vh] w-full flex-col overflow-hidden rounded-t-2xl border border-[#e6e3db] bg-[#fafaf8] sm:max-w-2xl sm:rounded-xl"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex items-center justify-between gap-4 border-b border-[#e6e3db] bg-white px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <img src="{{ $book->coverUrl() }}" alt="" class="h-14 w-10 shrink-0 rounded border border-[#e6e3db] object-cover"
                         onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                    <div class="min-w-0">
                        <span class="eyebrow">Cuplikan bacaan</span>
                        <h3 class="truncate font-serif-display text-[17px] font-bold text-[#0f2255]">{{ $book->title }}</h3>
                        <p class="truncate text-[12px] text-[#6b6f78]">{{ $book->author }}@if($book->publisher) &middot; {{ $book->publisher }}@endif</p>
                    </div>
                </div>
                <button type="button" @click="previewOpen = false" class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-[#6b6f78] transition-colors hover:bg-[#f4f2ec] hover:text-[#17181c]" aria-label="Tutup cuplikan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-y-auto px-5 py-6 sm:px-8">
                <div class="border-b border-[#e8e6df] pb-4 text-center">
                    <span class="eyebrow">Bagian pembuka</span>
                    <h4 class="mt-1 font-serif-display text-xl font-bold text-[#0f2255]">{{ $book->title }}</h4>
                </div>

                <div class="mx-auto mt-5 max-w-prose space-y-4 font-serif-display text-[15px] leading-[1.85] text-[#2c3038]">
                    {!! nl2br(e($book->description)) !!}
                </div>

                <div class="mt-7 rounded-lg border border-[#e6e3db] bg-white p-4 text-center">
                    <p class="text-[12px] text-[#6b6f78]">Cuplikan berakhir di sini. Buku fisik edisi resmi memuat seluruh isi cerita.</p>
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2.5">
                        @if($book->stock > 0 && auth()->check())
                            <button type="button" @click="previewOpen = false; document.getElementById('buy-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                    class="btn-primary btn-sm">Beli Rp{{ number_format($finalPrice, 0, ',', '.') }}</button>
                        @endif
                        <button type="button" @click="previewOpen = false" class="btn-ghost btn-sm">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
