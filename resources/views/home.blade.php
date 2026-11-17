@extends('layouts.bookspace')

@section('title', 'BOOKSPACE — Toko Buku Online & Sastra Pilihan')

@push('styles')
<style>
    #hero-carousel { position: relative; width: 100%; }
    .hero-slide { display: none; position: relative; width: 100%; height: 300px; align-items: center; }
    @media (min-width: 640px)  { .hero-slide { height: 400px; } }
    @media (min-width: 1024px) { .hero-slide { height: 460px; } }
    @media (min-width: 1440px) { .hero-slide { height: 520px; } }
    .hero-slide.is-active { display: flex; }
    .hero-slide .hero-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .hero-dot { width: 8px; height: 8px; border-radius: 9999px; background: rgba(255,255,255,.45); transition: all .25s ease; }
    .hero-dot.is-active { width: 26px; background: #e05c1a; }
</style>
@endpush

@section('content')

{{-- ══════════════ HERO ══════════════ --}}
@if($banners->isNotEmpty())
<section id="hero-carousel" class="select-none overflow-hidden bg-[#0b1736]" aria-label="Promo utama">
    @foreach($banners as $i => $banner)
        @php
            $bannerTarget = $banner->cta_link ?: route('books.index');
            $hasImage = $banner->image && file_exists(public_path('storage/'.$banner->image));
        @endphp
        <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}" role="group" aria-label="Banner {{ $i + 1 }} dari {{ $banners->count() }}">
            @if($hasImage)
                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" class="hero-img">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0b1736]/95 via-[#0b1736]/75 to-[#0b1736]/25"></div>
            @else
                <div class="absolute inset-0 bg-[#12224d]"></div>
            @endif

            <div class="shell relative z-10 w-full">
                <div class="max-w-2xl text-white">
                    <h2 class="font-serif-display text-2xl font-bold leading-tight sm:text-3xl lg:text-4xl">{{ $banner->title }}</h2>
                    @if($banner->subtitle)
                        <p class="mt-3 text-sm leading-relaxed text-white/80 sm:text-base">{{ $banner->subtitle }}</p>
                    @endif
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ $bannerTarget }}" class="btn-orange inline-flex h-11 items-center rounded-lg px-6 text-sm font-semibold">
                            {{ $banner->cta_text ?: 'Jelajahi Katalog' }}
                        </a>
                        <a href="{{ route('books.index', ['promo' => 1]) }}" class="inline-flex h-11 items-center rounded-lg border border-white/30 px-5 text-sm font-semibold text-white transition-colors hover:bg-white/10">
                            Lihat Buku Diskon
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if($banners->count() > 1)
        <div class="shell absolute inset-x-0 bottom-6 z-20">
            <div class="flex gap-2">
                @foreach($banners as $i => $banner)
                    <button class="hero-dot {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}" aria-label="Tampilkan banner {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>
        <div class="absolute bottom-4 right-4 z-20 flex gap-2 sm:right-8">
            <button id="hero-prev" aria-label="Banner sebelumnya" class="grid h-10 w-10 place-items-center rounded-full bg-black/30 text-white backdrop-blur transition-colors hover:bg-black/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button id="hero-next" aria-label="Banner berikutnya" class="grid h-10 w-10 place-items-center rounded-full bg-black/30 text-white backdrop-blur transition-colors hover:bg-black/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    @endif
</section>
@else
{{-- Tanpa banner aktif: hero informatif dengan akses cepat ke rak. --}}
<section class="border-b border-[#e6e3db] bg-white">
    <div class="shell grid items-center gap-8 py-10 lg:grid-cols-12 lg:py-14">
        <div class="lg:col-span-6">
            <p class="eyebrow text-[#e05c1a]">Toko buku daring sejak 2019</p>
            <h1 class="mt-2 font-serif-display text-[26px] font-bold leading-tight text-[#0f2255] sm:text-4xl">
                Ribuan judul sastra, fiksi, dan buku kerja — dikirim aman ke seluruh Indonesia
            </h1>
            <p class="muted mt-3 max-w-xl">
                Kurasi redaksi, harga transparan, dan verifikasi pembayaran di hari kerja yang sama.
                Gratis ongkir untuk belanja di atas Rp{{ number_format((int) \App\Models\Setting::value('free_shipping_threshold', 200000), 0, ',', '.') }}.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('books.index') }}" class="btn-primary inline-flex h-11 items-center rounded-lg px-6 text-sm font-semibold">Mulai Belanja</a>
                <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="btn-ghost inline-flex h-11 items-center rounded-lg px-5 text-sm font-semibold">Lihat Terlaris</a>
            </div>
        </div>
        @if($bestSellers->count() >= 3)
            <div class="lg:col-span-6">
                <div class="grid grid-cols-3 gap-3 sm:gap-4">
                    @foreach($bestSellers->take(3) as $book)
                        <a href="{{ route('books.show', $book->slug) }}" class="group block">
                            <div class="aspect-[2/3] overflow-hidden rounded-lg border border-[#e6e3db] bg-white shadow-sm">
                                <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" loading="lazy"
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                                     onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                            </div>
                            <p class="mt-2 line-clamp-2 text-[12px] font-semibold leading-snug text-[#3f4451] group-hover:text-[#0f2255]">{{ $book->title }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endif

{{-- ══════════════ NILAI TUKAR / TRUST STRIP ══════════════ --}}
<section class="border-b border-[#e6e3db] bg-white">
    <div class="shell grid grid-cols-2 gap-x-6 gap-y-5 py-5 lg:grid-cols-4">
        @foreach([
            ['Buku 100% asli', 'Dipasok langsung oleh penerbit resmi', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['Ongkir gratis', 'Untuk belanja di atas Rp'.number_format((int) \App\Models\Setting::value('free_shipping_threshold', 200000), 0, ',', '.'), 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['Banyak metode bayar', 'Transfer bank, QRIS, dan COD', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['Dukungan pelanggan', 'Balasan chat di hari kerja', 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
        ] as [$title, $sub, $path])
            <div class="flex items-start gap-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#f4f2ec] text-[#0f2255]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-[13px] font-bold leading-tight text-[#1c1c1c]">{{ $title }}</span>
                    <span class="mt-0.5 block text-[12px] leading-snug text-[#6b6f78]">{{ $sub }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

{{-- ══════════════ KATEGORI ══════════════ --}}
@if($categories->isNotEmpty())
<section class="shell py-10 md:py-12">
    <div class="mb-5 flex items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Jelajahi per rak</p>
            <h2 class="section-title mt-1">Kategori Buku</h2>
        </div>
        <a href="{{ route('books.index') }}" class="text-[13px] font-semibold text-[#0f2255] hover:text-[#e05c1a]">Semua katalog &rarr;</a>
    </div>

    <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        @foreach($categories->take(16) as $cat)
            <a href="{{ route('books.index', ['category' => $cat->slug]) }}"
               class="card card-pad flex !flex-col items-start justify-between gap-3 !p-3.5 transition-colors hover:border-[#0f2255]">
                <span class="block truncate text-[13px] font-bold leading-snug text-[#1c1c1c]">{{ $cat->name }}</span>
                <span class="text-[12px] text-[#6b6f78]">{{ $cat->books_count }} judul</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ══════════════ TERLARIS ══════════════ --}}
@if($bestSellers->isNotEmpty())
<section class="border-y border-[#e6e3db] bg-[#f5f1ea] py-10 md:py-12">
    <div class="shell">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow text-[#e05c1a]">Berdasarkan penjualan</p>
                <h2 class="section-title mt-1">Paling Banyak Dibeli</h2>
            </div>
            <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="text-[13px] font-semibold text-[#0f2255] hover:text-[#e05c1a]">Lihat semua &rarr;</a>
        </div>
        <x-book-shelf :books="$bestSellers->take(10)" />
    </div>
</section>
@endif

{{-- ══════════════ SOROTAN REDAKSI ══════════════ --}}
@php $spotlightBook = $featured->first() ?? $bestSellers->first(); @endphp
@if($spotlightBook)
<section class="shell py-10 md:py-14">
    <div class="overflow-hidden rounded-2xl border border-[#e6e3db] bg-white">
        <div class="grid lg:grid-cols-12">
            <div class="flex items-center justify-center bg-[#0b1736] p-8 lg:col-span-4 lg:p-12">
                <a href="{{ route('books.show', $spotlightBook->slug) }}" class="block w-40 sm:w-52 lg:w-full lg:max-w-[240px]">
                    <div class="aspect-[2/3] overflow-hidden rounded-lg shadow-2xl">
                        <img src="{{ $spotlightBook->coverUrl() }}" alt="{{ $spotlightBook->title }}"
                             class="h-full w-full object-cover"
                             onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                    </div>
                </a>
            </div>

            <div class="p-6 sm:p-8 lg:col-span-8 lg:p-12">
                <p class="eyebrow text-[#e05c1a]">Sorotan Redaksi</p>
                <h2 class="mt-1.5 font-serif-display text-2xl font-bold leading-tight text-[#0f2255] sm:text-3xl">{{ $spotlightBook->title }}</h2>
                <p class="mt-2 text-[13px] text-[#6b6f78]">
                    {{ $spotlightBook->author }}
                    @if($spotlightBook->publisher) &middot; {{ $spotlightBook->publisher }} @endif
                    @if($spotlightBook->year) &middot; {{ $spotlightBook->year }} @endif
                </p>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-[#3f4451]">{{ Str::limit($spotlightBook->description, 320) }}</p>

                <dl class="mt-6 grid max-w-xl grid-cols-2 gap-x-6 gap-y-3 border-t border-[#f0eee6] pt-5 text-[12px] sm:grid-cols-4">
                    @foreach([
                        ['Kategori', $spotlightBook->category->name ?? 'Koleksi'],
                        ['Penerbit', $spotlightBook->publisher ?: '—'],
                        ['Tahun Terbit', $spotlightBook->year ?: '—'],
                        ['Stok', $spotlightBook->stock > 0 ? $spotlightBook->stock.' eksemplar' : 'Habis'],
                    ] as [$term, $desc])
                        <div>
                            <dt class="text-[#9a9484]">{{ $term }}</dt>
                            <dd class="mt-0.5 font-semibold text-[#1c1c1c]">{{ $desc }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-7 flex flex-wrap items-center gap-4 border-t border-[#f0eee6] pt-6">
                    <div class="mr-auto">
                        @if($spotlightBook->hasDiscount())
                            <span class="font-serif-display text-2xl font-bold text-[#e05c1a]">Rp{{ number_format($spotlightBook->finalPrice(), 0, ',', '.') }}</span>
                            <span class="ml-2 text-sm text-[#9a9484] line-through">Rp{{ number_format($spotlightBook->price, 0, ',', '.') }}</span>
                        @else
                            <span class="font-serif-display text-2xl font-bold text-[#0f2255]">Rp{{ number_format($spotlightBook->price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('books.show', $spotlightBook->slug) }}" class="btn-ghost inline-flex h-11 items-center rounded-lg px-5 text-sm font-semibold">Detail Buku</a>
                    @if($spotlightBook->stock > 0)
                        @auth
                            <form method="POST" action="{{ route('books.cart.add', $spotlightBook) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-orange inline-flex h-11 items-center rounded-lg px-6 text-sm font-semibold">Tambah ke Keranjang</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-orange inline-flex h-11 items-center rounded-lg px-6 text-sm font-semibold">Masuk untuk Membeli</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════ RISIL TERBARU ══════════════ --}}
@if($newBooks->isNotEmpty())
<section class="shell py-10 md:py-12">
    <div class="mb-5 flex items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Barang masuk rak</p>
            <h2 class="section-title mt-1">Rilis Terbaru</h2>
        </div>
        <a href="{{ route('books.index', ['sort' => 'newest']) }}" class="text-[13px] font-semibold text-[#0f2255] hover:text-[#e05c1a]">Lihat semua &rarr;</a>
    </div>
    <x-book-shelf :books="$newBooks->take(10)" />
</section>
@endif

{{-- ══════════════ PROMO ══════════════ --}}
@if($promoBooks->isNotEmpty())
<section class="border-y border-[#ebdcc8] bg-[#faf5ee] py-10 md:py-12">
    <div class="shell">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow text-[#e05c1a]">Penawaran terbatas</p>
                <h2 class="section-title mt-1">Buku Sedang Diskon</h2>
            </div>
            <a href="{{ route('books.index', ['promo' => 1]) }}" class="text-[13px] font-semibold text-[#e05c1a] hover:text-[#c54e12]">Semua promo &rarr;</a>
        </div>
        <x-book-shelf :books="$promoBooks->take(10)" />
    </div>
</section>
@endif

{{-- ══════════════ ULASAN ══════════════ --}}
@if($reviews->isNotEmpty())
<section class="shell py-10 md:py-14">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Dari pembaca yang sudah berbelanja</p>
            <h2 class="section-title mt-1">Ulasan Terbaru</h2>
        </div>
        <a href="{{ route('books.index') }}" class="text-[13px] font-semibold text-[#0f2255] hover:text-[#e05c1a]">Telusuri katalog &rarr;</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($reviews->take(3) as $review)
            <figure class="card card-pad flex h-full flex-col justify-between">
                <div>
                    <div class="flex items-center gap-1 text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($review->rating) ? 'fill-current' : 'fill-transparent stroke-current text-[#e6e3db]' }}" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                        <span class="ml-1 text-[12px] font-semibold text-[#6b6f78]">{{ number_format($review->rating, 0) }}/5</span>
                    </div>
                    <blockquote class="mt-3 text-[13px] leading-relaxed text-[#3f4451]">&ldquo;{{ Str::limit($review->comment, 180) }}&rdquo;</blockquote>
                </div>
                <figcaption class="mt-4 flex items-center gap-3 border-t border-[#f0eee6] pt-4">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[12px] font-bold text-white">{{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}</span>
                    <span class="min-w-0">
                        <span class="block truncate text-[12px] font-bold text-[#1c1c1c]">{{ $review->user->name ?? 'Pembaca' }}</span>
                        @if($review->book)
                            <a href="{{ route('books.show', $review->book->slug) }}" class="block truncate text-[12px] text-[#6b6f78] hover:text-[#0f2255]">{{ $review->book->title }}</a>
                        @endif
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

@endsection

@if($banners->count() > 1)
@push('scripts')
<script>
(function () {
    const root = document.getElementById('hero-carousel');
    if (!root) return;
    const slides = root.querySelectorAll('.hero-slide');
    const dots = root.querySelectorAll('.hero-dot');
    const duration = {{ (int) \App\Models\Setting::value('carousel_duration', 5000) ?: 5000 }};
    let current = 0, timer = null, paused = false;

    function render() {
        slides.forEach((s, i) => s.classList.toggle('is-active', i === current));
        dots.forEach((d, i) => d.classList.toggle('is-active', i === current));
    }
    function goTo(n) {
        current = (n + slides.length) % slides.length;
        render();
    }
    function restart() {
        clearInterval(timer);
        if (slides.length > 1) timer = setInterval(() => { if (!paused) goTo(current + 1); }, duration);
    }

    document.getElementById('hero-prev')?.addEventListener('click', () => { goTo(current - 1); restart(); });
    document.getElementById('hero-next')?.addEventListener('click', () => { goTo(current + 1); restart(); });
    dots.forEach(dot => dot.addEventListener('click', () => { goTo(+dot.dataset.index); restart(); }));

    root.addEventListener('mouseenter', () => paused = true);
    root.addEventListener('mouseleave', () => paused = false);

    let touchX = 0;
    root.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; paused = true; }, { passive: true });
    root.addEventListener('touchend', e => {
        const diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
        paused = false;
        restart();
    }, { passive: true });

    restart();
})();
</script>
@endpush
@endif
