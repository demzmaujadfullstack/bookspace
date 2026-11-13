@extends('layouts.bookspace')

@section('title', $book->title . ' — ' . $book->author)
@section('meta_description', Str::limit($book->description, 160))

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12" x-data="{ previewOpen: false }">

    {{-- ── Breadcrumb ────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-8 pb-4 border-b border-[#e8e6df]">
        <a href="{{ route('home') }}" class="hover:text-[#0f2255] transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ route('books.index') }}" class="hover:text-[#0f2255] transition-colors">Katalog Buku</a>
        @if($book->category)
            <span>/</span>
            <a href="{{ route('books.index', ['category' => $book->category->slug]) }}" class="hover:text-[#0f2255] transition-colors">{{ $book->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-800 font-medium truncate max-w-xs">{{ $book->title }}</span>
    </nav>

    {{-- ── Product Detail Hero ────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 mb-16">

        {{-- Left: Book Visual Presentation (4 cols) --}}
        <div class="lg:col-span-5 flex flex-col items-center">
            <div class="sticky top-28 w-full max-w-sm">
                {{-- Physical Book Display Card --}}
                <div class="relative bg-[#f4f2ec] p-8 rounded-2xl border border-[#e8e6df] shadow-sm flex items-center justify-center">
                    <div class="relative w-full max-w-[240px] aspect-[2/3] rounded shadow-2xl overflow-hidden bg-white">
                        <img
                            src="{{ $book->coverUrl() }}"
                            alt="{{ $book->title }}"
                            class="w-full h-full object-cover"
                            onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'"
                        >
                        {{-- Book spine shadow effect --}}
                        <div class="absolute inset-y-0 left-0 w-4 bg-gradient-to-r from-black/35 via-black/15 to-transparent pointer-events-none"></div>
                        <div class="absolute inset-y-0 right-0 w-[2px] bg-white/40 pointer-events-none"></div>
                    </div>

                    {{-- Badges --}}
                    @if($book->hasDiscount())
                        <div class="absolute top-4 right-4 bg-[#e05c1a] text-white text-xs font-bold px-3 py-1 rounded shadow-md tracking-wide">
                            HEMAT {{ $book->discount_percent }}%
                        </div>
                    @endif
                </div>

                {{-- Trust Notes Under Cover --}}
                <div class="mt-4 bg-white border border-[#e8e6df] rounded-xl p-4 space-y-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0f2255] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Jaminan 100% Produk Original langsung dari Penerbit</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0f2255] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Pengemasan berlapis karton tebal &amp; bubble wrap</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Editorial Book Details (7 cols) --}}
        <div class="lg:col-span-7">
            {{-- Category Pill --}}
            @if($book->category)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#f4f2ec] text-[#e05c1a] text-xs font-semibold uppercase tracking-wider mb-3">
                    <span>{{ $book->category->name }}</span>
                    @if($book->year)
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-600 font-normal">{{ $book->year }}</span>
                    @endif
                </div>
            @endif

            {{-- Title --}}
            <h1 class="font-serif-display font-bold text-2xl sm:text-3xl lg:text-4xl text-[#0f2255] mb-2 leading-tight">
                {{ $book->title }}
            </h1>

            {{-- Author Attribution --}}
            <p class="text-base text-gray-600 font-serif-display italic mb-4">
                Oleh <span class="font-bold text-gray-900 not-italic">{{ $book->author }}</span>
                @if($book->publisher)
                    &bull; Diterbitkan oleh <span class="text-gray-800 font-medium not-italic">{{ $book->publisher }}</span>
                @endif
            </p>

            {{-- Rating & Social Proof --}}
            @php $rating = $book->averageRating(); $reviewCount = $book->reviews->count(); @endphp
            <div class="flex items-center gap-3 pb-6 mb-6 border-b border-[#e8e6df] text-xs">
                @if($rating > 0)
                    <div class="flex items-center text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($rating) ? 'fill-current' : 'text-gray-200 fill-transparent stroke-current' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="font-bold text-gray-800 text-sm">{{ number_format($rating, 1) }}</span>
                    <span class="text-gray-400">({{ $reviewCount }} Ulasan Pembaca)</span>
                @else
                    <span class="text-gray-400 font-sans">Belum ada ulasan pembaca</span>
                @endif
                <span class="text-gray-300">•</span>
                <span class="text-gray-600 font-medium">{{ $book->soldCount() }} eksemplar terjual</span>
            </div>

            {{-- Price Display Box --}}
            <div class="bg-[#fafaf8] border border-[#e8e6df] rounded-xl p-5 mb-6">
                <div class="flex items-baseline gap-3 mb-2">
                    @if($book->hasDiscount())
                        <span class="font-serif-display font-bold text-3xl text-[#e05c1a]">
                            Rp{{ number_format($book->finalPrice(), 0, ',', '.') }}
                        </span>
                        <span class="text-base text-gray-400 line-through">
                            Rp{{ number_format($book->price, 0, ',', '.') }}
                        </span>
                        <span class="bg-[#e05c1a] text-white text-xs font-bold px-2 py-0.5 rounded">
                            Diskon {{ $book->discount_percent }}%
                        </span>
                    @else
                        <span class="font-serif-display font-bold text-3xl text-[#0f2255]">
                            Rp{{ number_format($book->price, 0, ',', '.') }}
                        </span>
                    @endif
                </div>

                {{-- Stock Status Indicator --}}
                <div class="flex items-center gap-2 text-xs">
                    @if($book->stock === 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span class="text-red-600 font-bold">Stok Habis (Sementara Tidak Tersedia)</span>
                    @elseif($book->stock <= \App\Models\Book::STOCK_LOW_THRESHOLD)
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-amber-700 font-semibold">Stok Terbatas: Sisa {{ $book->stock }} eksemplar</span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-emerald-700 font-semibold">Stok Tersedia ({{ $book->stock }} eksemplar siap kirim)</span>
                    @endif
                </div>
            </div>

            {{-- Purchase Box / Add to Cart --}}
            <div class="mb-8 space-y-3">
                @if($book->stock > 0)
                    @auth
                        <form method="POST" action="{{ route('books.cart.add', $book) }}" class="flex flex-col sm:flex-row items-stretch gap-3" x-data="{ qty: 1, max: {{ $book->stock }} }">
                            @csrf
                            {{-- Quantity Selector — Alpine.js stepper (avoids @tailwindcss/forms padding bug on type=number) --}}
                            <div class="flex items-center border-2 border-[#dcd8cd] rounded-xl overflow-hidden bg-white w-full sm:w-40 shrink-0">
                                <button type="button" @click="if (qty > 1) qty--" class="w-11 h-12 flex items-center justify-center text-gray-600 hover:bg-[#f4f2ec] transition-colors font-bold text-xl select-none" :disabled="qty <= 1">−</button>
                                <input type="text" inputmode="numeric" id="qty" name="quantity" x-model="qty" @change="qty = Math.max(1, Math.min(max, parseInt(qty) || 1))" class="flex-1 h-12 text-center font-bold text-base text-[#0f2255] focus:outline-none focus:ring-0 p-0 border-x border-[#dcd8cd]" style="box-shadow:none">
                                <button type="button" @click="if (qty < max) qty++" class="w-11 h-12 flex items-center justify-center text-gray-600 hover:bg-[#f4f2ec] transition-colors font-bold text-xl select-none" :disabled="qty >= max">+</button>
                            </div>

                            {{-- CTA Button --}}
                            <button type="submit" class="flex-1 bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-2.5 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span>Tambahkan ke Keranjang</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2.5 w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Masuk untuk Melakukan Pemesanan</span>
                        </a>
                    @endauth
                @else
                    <button disabled class="w-full bg-gray-200 text-gray-500 font-semibold py-3.5 px-6 rounded-xl cursor-not-allowed text-sm">
                        Stok Buku Sedang Habis
                    </button>
                @endif

                {{-- Action Row: Wishlist & Baca Cuplikan --}}
                <div class="flex items-center gap-2.5 pt-1">
                    {{-- Baca Cuplikan Button --}}
                    <button
                        type="button"
                        @click="previewOpen = true"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-[#f4f2ec] hover:bg-[#ebe7de] text-[#0f2255] border border-[#dcd8cd] rounded-xl text-xs font-bold transition-all shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#e05c1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Baca Cuplikan Bab Pertama</span>
                    </button>

                    {{-- Wishlist Toggle Button --}}
                    @auth
                        @php
                            $isWishlisted = auth()->user()->hasWishlisted($book->id);
                        @endphp
                        <form method="POST" action="{{ route('wishlist.toggle', $book) }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex items-center justify-center gap-1.5 px-4 py-3 border rounded-xl text-xs font-bold transition-all shadow-sm {{ $isWishlisted ? 'bg-red-50 border-red-200 text-red-600' : 'bg-white border-[#dcd8cd] text-gray-700 hover:border-red-300 hover:text-red-500 hover:bg-red-50/50' }}"
                                title="{{ $isWishlisted ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $isWishlisted ? 'Tersimpan' : 'Favorit' }}</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>

            {{-- ── Book Specifications Table ──────────────────── --}}
            <div class="border-t border-[#e8e6df] pt-6">
                <h3 class="font-serif-display font-bold text-base text-[#0f2255] mb-4">Informasi Bibliografi &amp; Spesifikasi</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-6 text-xs bg-white p-5 rounded-xl border border-[#e8e6df]">
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">ISBN</span>
                        <span class="font-mono font-medium text-gray-800">{{ $book->isbn }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">Penerbit</span>
                        <span class="font-medium text-gray-800">{{ $book->publisher }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">Tahun Terbit</span>
                        <span class="font-medium text-gray-800">{{ $book->year }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">Kategori</span>
                        <span class="font-medium text-[#e05c1a]">{{ $book->category->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">Format Buku</span>
                        <span class="font-medium text-gray-800">Buku Fisik (Cetakan Resmi)</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 uppercase tracking-wider mb-0.5">Bahasa</span>
                        <span class="font-medium text-gray-800">Bahasa Indonesia</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Book Synopsis / Editorial Review ───────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-10 mb-14 shadow-sm">
        <div class="max-w-3xl">
            <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Sinopsis &amp; Ikhtisar</span>
            <h2 class="font-serif-display font-bold text-2xl text-[#0f2255] mt-1 mb-6">Tentang Buku Ini</h2>

            <div class="prose prose-slate max-w-none text-gray-700 leading-relaxed font-serif text-base space-y-4">
                {!! nl2br(e($book->description)) !!}
            </div>
        </div>
    </div>

    {{-- ── Reader Reviews ─────────────────────────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-10 mb-14 shadow-sm">
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-[#e8e6df]">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Tinjauan Pembaca</span>
                <h2 class="font-serif-display font-bold text-2xl text-[#0f2255] mt-1">Ulasan &amp; Kesan Pembaca</h2>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold font-serif-display text-[#0f2255]">{{ number_format($rating, 1) }}</span>
                <span class="text-xs text-gray-400">/ 5.0</span>
            </div>
        </div>

        {{-- Review Submission Form --}}
        @auth
            @php
                $userReview = $book->reviews->firstWhere('user_id', auth()->id());
            @endphp
            @if($userReview)
                <div class="mb-8 p-4 bg-[#f4f2ec] border border-[#e8e6df] rounded-xl flex items-center gap-3 text-xs text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Anda telah memberikan ulasan rating <strong>{{ $userReview->rating }} bintang</strong> untuk buku ini. Terima kasih atas partisipasi Anda!</span>
                </div>
            @else
                <div class="mb-10 bg-[#fafaf8] border border-[#e8e6df] rounded-2xl p-6 shadow-sm" x-data="{
                    rating: 5,
                    hoverRating: 0,
                    get ratingLabel() {
                        const labels = { 1: '1 Bintang — Kurang', 2: '2 Bintang — Cukup', 3: '3 Bintang — Bagus', 4: '4 Bintang — Sangat Bagus', 5: '5 Bintang — Luar Biasa (Rekomendasi)' };
                        return labels[this.hoverRating || this.rating] || '';
                    }
                }">
                    <h3 class="font-serif-display font-bold text-lg text-[#0f2255] mb-1">Tulis Ulasan &amp; Kesan Anda</h3>
                    <p class="text-xs text-gray-500 mb-5 font-serif italic">Bagikan impresi dan pengalaman membaca Anda untuk membantu pembaca lain.</p>

                    <form method="POST" action="{{ route('books.reviews.store', $book) }}" class="space-y-4">
                        @csrf
                        {{-- Star Rating Picker --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Rating Bintang</label>
                            <div class="flex items-center gap-1.5">
                                <template x-for="star in 5" :key="star">
                                    <button
                                        type="button"
                                        @click="rating = star"
                                        @mouseover="hoverRating = star"
                                        @mouseleave="hoverRating = 0"
                                        class="p-1 focus:outline-none transition-transform hover:scale-110"
                                        :title="star + ' Bintang'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 transition-colors" viewBox="0 0 24 24" :fill="(hoverRating || rating) >= star ? '#f59e0b' : 'transparent'" :stroke="(hoverRating || rating) >= star ? '#f59e0b' : '#d1d5db'" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </button>
                                </template>
                                <span class="text-xs font-semibold text-[#e05c1a] ml-2" x-text="ratingLabel"></span>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                        </div>

                        {{-- Comment Textarea --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Ulasan Anda</label>
                            <textarea
                                name="comment"
                                rows="3"
                                placeholder="Tuliskan pengalaman Anda membaca buku ini, gaya bahasa penulis, alur cerita, atau pesan penting yang Anda dapatkan..."
                                class="w-full text-xs sm:text-sm border border-[#e8e6df] rounded-xl p-3.5 focus:outline-none focus:border-[#0f2255] bg-white text-gray-800 placeholder-gray-400 font-serif"
                                required
                                minlength="5"
                                maxlength="500"
                            >{{ old('comment') }}</textarea>
                            <p class="text-[10px] text-gray-400 mt-1">Minimal 5 karakter, maksimal 500 karakter.</p>
                        </div>

                        <button type="submit" class="btn-primary text-xs py-2.5 px-5">
                            Kirim Ulasan Pembaca
                        </button>
                    </form>
                </div>
            @endif
        @else
            <div class="mb-8 p-4 bg-[#f4f2ec] border border-[#e8e6df] rounded-xl flex items-center justify-between text-xs text-gray-600">
                <span>Ingin membagikan ulasan Anda untuk buku ini?</span>
                <a href="{{ route('login') }}" class="font-bold text-[#0f2255] hover:underline">Masuk untuk Menulis Ulasan &rarr;</a>
            </div>
        @endauth

        @if($book->reviews->isNotEmpty())
            <div class="space-y-6">
                @foreach($book->reviews as $rev)
                    <div class="border-b border-[#f0eee6] last:border-0 pb-6 last:pb-0">
                        <div class="flex items-start gap-3.5">
                            <div class="w-9 h-9 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="font-bold text-sm text-gray-900">{{ $rev->user->name ?? 'Pembaca' }}</h4>
                                    <div class="flex items-center text-amber-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3.5 w-3.5 {{ $i <= $rev->rating ? 'fill-current' : 'text-gray-200 fill-transparent stroke-current' }}" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-[11px] text-gray-400 font-mono">{{ $rev->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed font-serif italic">
                                    &ldquo;{{ $rev->comment }}&rdquo;
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-gray-400 text-sm">
                <p>Belum ada ulasan untuk judul ini. Jadilah pembaca pertama yang menuliskan kesan Anda setelah menerima pesanan!</p>
            </div>
        @endif
    </div>

    {{-- ── Related Books Shelf ────────────────────────────────── --}}
    @if($related->isNotEmpty())
        <div>
            <div class="flex items-end justify-between mb-8 pb-4 border-b border-[#e8e6df]">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Rekomendasi Lainnya</span>
                    <h2 class="font-serif-display font-bold text-2xl text-[#0f2255] mt-1">Buku Serupa yang Mungkin Anda Sukai</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($related as $rel)
                    @include('components.book-card', ['book' => $rel])
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── First Chapter Preview Modal ("Baca Cuplikan") ──────── --}}
    <div
        x-show="previewOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="previewOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div
            @click.away="previewOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-[#fafaf8] rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col border border-[#e8e6df] overflow-hidden"
        >
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 pb-4 border-b border-[#e8e6df] bg-white">
                <div class="flex items-center gap-3">
                    <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded shadow-sm border border-gray-200" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#e05c1a]">Cuplikan Bacaan Eksklusif</span>
                        <h3 class="font-serif-display font-bold text-lg text-[#0f2255] leading-tight">{{ $book->title }}</h3>
                        <p class="text-xs text-gray-500 font-serif italic mt-0.5">Karya {{ $book->author }} &bull; {{ $book->publisher }}</p>
                    </div>
                </div>
                <button @click="previewOpen = false" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body: Reading Experience --}}
            <div class="overflow-y-auto p-6 sm:p-8 space-y-4 font-serif text-gray-800 leading-relaxed text-sm sm:text-base">
                <div class="text-center pb-4 border-b border-[#e8e6df]">
                    <span class="text-xs uppercase tracking-widest text-gray-400 font-sans">Bagian Pembuka</span>
                    <h4 class="font-serif-display text-xl font-bold text-[#0f2255] mt-1">Bab 1: Awal Mula Perjalanan</h4>
                </div>

                <p class="first-letter:text-4xl first-letter:font-bold first-letter:text-[#0f2255] first-letter:float-left first-letter:mr-2 first-letter:font-serif">
                    {{ $book->description }}
                </p>

                <p>
                    Waktu bergerak perlahan di antara hembusan angin pagi. Setiap kata yang tertulis di lembaran ini adalah saksi bisu dari pergulatan jiwa dan pencarian makna yang tak pernah usai. Ada misteri yang tersembunyi di balik setiap langkah, dan rahasia yang hanya akan terbuka bagi mereka yang berani terus membaca hingga lembar terakhir.
                </p>

                <p>
                    "Sebuah buku yang baik tidak sekadar menceritakan sebuah kisah," tulis seorang kritikus sastra, "tetapi ia membangun sebuah cermin tempat kita melihat pantulan diri kita sendiri, ketakutan kita, dan harapan terbesar kita."
                </p>

                <div class="mt-8 p-5 bg-[#f4f2ec] rounded-xl border border-[#e8e6df] text-center">
                    <p class="text-xs text-gray-600 font-serif italic mb-3">Ini adalah akhir dari cuplikan bab pembuka. Miliki buku fisik edisi resmi ini untuk menikmati keseluruhan kisah dan kedalaman pesonanya.</p>
                    <div class="flex items-center justify-center gap-3">
                        @if($book->stock > 0)
                            <form method="POST" action="{{ route('books.cart.add', $book) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-primary text-xs py-2 px-5">
                                    Beli Buku Sekarang (Rp{{ number_format($book->finalPrice(), 0, ',', '.') }})
                                </button>
                            </form>
                        @endif
                        <button type="button" @click="previewOpen = false" class="btn-outline text-xs py-2 px-4">
                            Tutup Cuplikan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

