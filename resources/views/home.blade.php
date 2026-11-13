@extends('layouts.bookspace')

@section('title', 'BOOKSPACE — Toko Buku Online & Sastra Pilihan')

@push('styles')
<style>
/* Full-bleed panoramic carousel — no cropping */
#hero-carousel { position: relative; width: 100%; }
.carousel-slide {
    display: none;
    position: relative;
    width: 100%;
    height: 480px;
    align-items: center;
}
@media (min-width: 640px)  { .carousel-slide { height: 520px; } }
@media (min-width: 1024px) { .carousel-slide { height: 580px; } }
.carousel-slide.active { display: flex; }
.carousel-slide .slide-bg-img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; object-position: center;
}
.carousel-slide .slide-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(
        to right,
        rgba(11,23,54,0.90) 0%,
        rgba(11,23,54,0.65) 45%,
        rgba(11,23,54,0.20) 70%,
        rgba(0,0,0,0.05) 100%
    );
}
/* For slides without an image: bottom fade-out instead */
.carousel-slide .slide-overlay-plain {
    position: absolute; inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.05), rgba(11,23,54,0.35));
}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO CAROUSEL: FULL-BLEED PANORAMIC BOOKSTORE BANNER
═══════════════════════════════════════════════════════════════════ --}}
@if($banners->isNotEmpty())
<section id="hero-carousel" class="relative bg-[#0b1736] overflow-hidden select-none w-full">

    {{-- Slides --}}
    @foreach($banners as $i => $banner)
    @php
        $bannerTarget = $banner->cta_link ?: route('books.index');
        $hasImage = $banner->image && file_exists(public_path('storage/'.$banner->image));
    @endphp
    <div class="carousel-slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
        @if($hasImage)
            {{-- Clean full-bleed image banner: click whole banner, no obstructive text/title overlay --}}
            <a href="{{ $bannerTarget }}" class="absolute inset-0 block cursor-pointer group" title="{{ $banner->title }}">
                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" class="slide-bg-img transition-transform duration-700 group-hover:scale-[1.01]">
            </a>
        @else
            {{-- Fallback: atmospheric gradient with centered content --}}
            @php
                $gradPalettes = [
                    'from-[#0b1736] via-[#12224d] to-[#1e1730]',
                    'from-[#12224d] via-[#1c2d5a] to-[#2b1820]',
                    'from-[#191924] via-[#0b1736] to-[#142926]',
                    'from-[#241318] via-[#0b1736] to-[#12224d]',
                ];
                $currentGrad = $gradPalettes[$i % count($gradPalettes)];
            @endphp
            <div class="absolute inset-0 bg-gradient-to-r {{ $currentGrad }}">
                <div class="absolute -right-12 -bottom-12 opacity-5 text-white pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>

            {{-- Centered Content inside gradient slide --}}
            <div class="relative z-10 px-6 md:px-8 max-w-3xl mx-auto w-full text-center flex flex-col items-center justify-center">
                <h1 class="font-serif-display font-bold text-white text-3xl sm:text-4xl lg:text-5xl leading-tight mb-4 tracking-tight drop-shadow-md">
                    {{ $banner->title }}
                </h1>

                @if($banner->subtitle)
                    <p class="text-white/80 text-base sm:text-lg mb-8 leading-relaxed font-sans max-w-xl mx-auto">
                        {{ $banner->subtitle }}
                    </p>
                @endif

                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ $bannerTarget }}" class="inline-flex items-center gap-2.5 bg-[#e05c1a] hover:bg-[#c54e12] text-white font-semibold px-7 py-3.5 rounded-lg text-sm sm:text-base tracking-wide transition-all shadow-lg hover:scale-[1.02]">
                        <span>{{ $banner->cta_text ?: 'Jelajahi Sekarang' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="{{ route('books.index', ['promo' => 1]) }}" class="inline-flex items-center gap-2 text-white/90 hover:text-white px-5 py-3 rounded-lg border border-white/25 hover:border-white/50 text-sm font-medium transition-all">
                        Lihat Buku Diskon
                    </a>
                </div>
            </div>
        @endif
    </div>
    @endforeach

    {{-- Carousel Navigation Controls --}}
    @if($banners->count() > 1)
        <button id="carousel-prev" aria-label="Banner Sebelumnya" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur-md border border-white/10 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button id="carousel-next" aria-label="Banner Selanjutnya" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur-md border border-white/10 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Dots Indicators --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            @foreach($banners as $i => $banner)
                <button class="carousel-dot h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-[#e05c1a] w-8' : 'bg-white/40 w-2.5' }}" data-index="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>

@push('scripts')
<script>
(function() {
    const slides = document.querySelectorAll('.carousel-slide');
    if (!slides.length) return;
    const dots = document.querySelectorAll('.carousel-dot');
    const duration = {{ \App\Models\Setting::where('key','carousel_duration')->value('value') ?? 5000 }};
    let current = 0, timer = null, paused = false;

    function goTo(n) {
        slides[current].classList.remove('active');
        if (dots.length) {
            dots[current].classList.remove('bg-[#e05c1a]', 'w-8');
            dots[current].classList.add('bg-white/40', 'w-2.5');
        }
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots.length) {
            dots[current].classList.add('bg-[#e05c1a]', 'w-8');
            dots[current].classList.remove('bg-white/40', 'w-2.5');
        }
    }

    function startTimer() {
        if (slides.length <= 1) return;
        timer = setInterval(() => { if (!paused) goTo(current + 1); }, duration);
    }
    function resetTimer() { clearInterval(timer); startTimer(); }

    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetTimer(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetTimer(); });
    dots.forEach(dot => dot.addEventListener('click', () => { goTo(+dot.dataset.index); resetTimer(); }));

    const hero = document.getElementById('hero-carousel');
    if (hero) {
        hero.addEventListener('mouseenter', () => paused = true);
        hero.addEventListener('mouseleave', () => paused = false);

        let touchX = 0;
        hero.addEventListener('touchstart', e => touchX = e.touches[0].clientX, { passive: true });
        hero.addEventListener('touchend', e => {
            const diff = touchX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { goTo(current + (diff > 0 ? 1 : -1)); resetTimer(); }
        }, { passive: true });
    }

    startTimer();
})();
</script>
@endpush
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     TRUST BADGES / BOOKSTORE COMMITMENT STRIP
═══════════════════════════════════════════════════════════════════ --}}
<section class="border-b border-[#e8e6df] bg-white">
    <div class="px-5 md:px-8 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-[#f4f2ec] text-[#0f2255] flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 tracking-wide uppercase">100% Buku Asli</h4>
                    <p class="text-[11px] text-gray-500">Langsung dari penerbit resmi</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-[#f4f2ec] text-[#0f2255] flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 tracking-wide uppercase">Pengemasan Aman</h4>
                    <p class="text-[11px] text-gray-500">Lapisan kardus &amp; bubble wrap</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-[#f4f2ec] text-[#0f2255] flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 tracking-wide uppercase">Pembayaran Fleksibel</h4>
                    <p class="text-[11px] text-gray-500">Bank Transfer, QRIS &amp; COD</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-[#f4f2ec] text-[#0f2255] flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900 tracking-wide uppercase">Layanan Responsif</h4>
                    <p class="text-[11px] text-gray-500">Dukungan penuh tim redaksi</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     KATEGORI BUKU / GENRE NAVIGATION
═══════════════════════════════════════════════════════════════════ --}}
<section class="px-5 md:px-8 py-12 md:py-16">
    <div class="flex items-end justify-between mb-8 pb-4 border-b border-[#e8e6df]">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Jelajahi Genre</span>
            <h2 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">Kategori Buku Pilihan</h2>
        </div>
        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-[#0f2255] hover:text-[#e05c1a] flex items-center gap-1.5 transition-colors uppercase tracking-wider">
            <span>Lihat Semua Kategori</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
        @foreach($categories->take(12) as $cat)
        @php
            /** Category-specific icons keyed by slug */
            $catIcons = [
                'novel' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                'pengembangan-diri' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                'bisnis-ekonomi' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                'teknologi-komputer' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>',
                'sejarah-budaya' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                'sains-alam' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
                'agama-spiritualitas' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                'anak-remaja' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                'komik-manga' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'pendidikan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>',
            ];
            $iconPath = $catIcons[$cat->slug] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>';
        @endphp
        <a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="group bg-white p-4 rounded-xl border border-[#e8e6df] hover:border-[#0f2255] hover:shadow-md transition-all flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-[#f5f1ea] group-hover:bg-[#0f2255] text-[#0f2255] group-hover:text-white flex items-center justify-center transition-colors mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $iconPath !!}
                </svg>
            </div>
            <h3 class="font-serif-display font-semibold text-xs sm:text-sm text-gray-900 group-hover:text-[#0f2255] leading-snug line-clamp-1 mb-1">
                {{ $cat->name }}
            </h3>
            <span class="text-[11px] text-gray-400 font-sans">{{ $cat->books_count }} judul</span>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     BUKU TERLARIS (BESTSELLERS SHELF)
═══════════════════════════════════════════════════════════════════ --}}
@if($bestSellers->isNotEmpty())
<section class="bg-[#f5f1ea] py-14 md:py-16 border-y border-[#e8e6df]">
    <div class="px-5 md:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Buku Terlaris</span>
                <h2 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">Paling Banyak Dibaca Pekan Ini</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Judul-judul yang mendominasi meja pembaca di seluruh tanah air.</p>
            </div>
            <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-[#0f2255] hover:text-[#e05c1a] uppercase tracking-wider transition-colors">
                <span>Daftar Lengkap</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($bestSellers->take(8) as $index => $book)
                @include('components.book-card', ['book' => $book])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     EDITORIAL SPOTLIGHT: BUKU PILIHAN REDAKSI (BOOK OF THE MONTH)
═══════════════════════════════════════════════════════════════════ --}}
@php
    $spotlightBook = $featured->first() ?? $bestSellers->first();
@endphp
@if($spotlightBook)
<section class="px-5 md:px-8 py-14 md:py-16">
    <div class="bg-[#0b1736] text-white rounded-2xl overflow-hidden p-6 sm:p-10 lg:p-12 shadow-xl relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            {{-- Left: Book Cover Display with Real Spine Shadow --}}
            <div class="lg:col-span-4 flex justify-center">
                <div class="relative w-48 sm:w-60 aspect-[2/3] rounded-lg shadow-2xl overflow-hidden bg-white border border-white/20">
                    <img
                        src="{{ $spotlightBook->coverUrl() }}"
                        alt="{{ $spotlightBook->title }}"
                        class="w-full h-full object-cover"
                        onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'"
                    >
                    <div class="absolute inset-y-0 left-0 w-4 bg-gradient-to-r from-black/40 via-black/15 to-transparent pointer-events-none"></div>
                </div>
            </div>

            {{-- Right: Editorial Notes & Details --}}
            <div class="lg:col-span-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#e05c1a] text-white text-xs font-bold uppercase tracking-wider mb-4">
                    <span>★ Rekomendasi Redaksi BOOKSPACE</span>
                </div>

                <h2 class="font-serif-display font-bold text-2xl sm:text-4xl text-white mb-2 leading-tight">
                    {{ $spotlightBook->title }}
                </h2>

                <p class="text-sm sm:text-base text-white/70 font-serif-display italic mb-4">
                    Karya {{ $spotlightBook->author }} &bull; Diterbitkan oleh {{ $spotlightBook->publisher }}
                </p>

                <p class="text-white/80 text-sm sm:text-base leading-relaxed mb-6 font-sans line-clamp-4">
                    {{ $spotlightBook->description }}
                </p>

                {{-- Price & Action --}}
                <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-white/10">
                    <div>
                        <span class="block text-[11px] text-white/50 uppercase tracking-wider mb-0.5">Harga Spesial</span>
                        @if($spotlightBook->hasDiscount())
                            <div class="flex items-baseline gap-2">
                                <span class="font-serif-display font-bold text-2xl text-[#e05c1a]">
                                    Rp{{ number_format($spotlightBook->finalPrice(), 0, ',', '.') }}
                                </span>
                                <span class="text-sm text-white/40 line-through">
                                    Rp{{ number_format($spotlightBook->price, 0, ',', '.') }}
                                </span>
                            </div>
                        @else
                            <span class="font-serif-display font-bold text-2xl text-white">
                                Rp{{ number_format($spotlightBook->price, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('books.show', $spotlightBook->slug) }}" class="btn-orange text-sm font-semibold px-6 py-3 rounded-lg shadow-md">
                            Lihat Rincian Buku
                        </a>
                        @if($spotlightBook->stock > 0)
                            @auth
                                <form method="POST" action="{{ route('books.cart.add', $spotlightBook) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-white/10 hover:bg-white/20 text-white font-medium px-5 py-3 rounded-lg text-sm border border-white/20 transition-colors flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        Beli Sekarang
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="bg-white/10 hover:bg-white/20 text-white font-medium px-5 py-3 rounded-lg text-sm border border-white/20 transition-colors flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Beli Sekarang
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     BUKU TERBARU (FRESH ARRIVALS)
═══════════════════════════════════════════════════════════════════ --}}
@if($newBooks->isNotEmpty())
<section class="px-5 md:px-8 py-12 md:py-16">
    <div class="flex items-end justify-between mb-8 pb-4 border-b border-[#e8e6df]">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Rilis Baru</span>
            <h2 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">Koleksi Terbaru di Rak Kami</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Buku-buku baru yang siap melengkapi perpustakaan pribadi Anda.</p>
        </div>
        <a href="{{ route('books.index', ['sort' => 'newest']) }}" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-[#0f2255] hover:text-[#e05c1a] uppercase tracking-wider transition-colors">
            <span>Lihat Semua Baru</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
        @foreach($newBooks->take(8) as $book)
            @include('components.book-card', ['book' => $book])
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     PROMO & DISKON SPESIAL (DEALS)
═══════════════════════════════════════════════════════════════════ --}}
@if($promoBooks->isNotEmpty())
<section class="bg-[#faf5ee] py-14 md:py-16 border-y border-[#ebdcc8]">
    <div class="px-5 md:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Penawaran Terbatas</span>
                <h2 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">Diskon Istimewa Pekan Ini</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Nikmati potongan harga spesial untuk judul-judul berkualitas.</p>
            </div>
            <a href="{{ route('books.index', ['promo' => 1]) }}" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-[#e05c1a] hover:text-[#c54e12] uppercase tracking-wider transition-colors">
                <span>Semua Promo</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
            @foreach($promoBooks->take(4) as $book)
                @include('components.book-card', ['book' => $book])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     ULASAN PEMBACA / TESTIMONIALS
═══════════════════════════════════════════════════════════════════ --}}
<section class="px-5 md:px-8 py-16 md:py-20">
    <div class="text-center max-w-xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Kesan &amp; Catatan Pembaca</span>
        <h2 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">Kata Mereka Tentang Pengalaman di BOOKSPACE</h2>
        <div class="w-12 h-0.5 bg-[#e05c1a] mx-auto mt-4"></div>
    </div>

    @if($reviews->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($reviews->take(3) as $review)
            <div class="bg-white p-6 rounded-xl border border-[#e8e6df] shadow-sm flex flex-col justify-between">
                <div>
                    {{-- Stars --}}
                    <div class="flex items-center text-amber-500 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-200 fill-transparent stroke-current' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    {{-- Comment in quote style --}}
                    <p class="text-sm text-gray-700 leading-relaxed font-serif-display italic mb-4">
                        &ldquo;{{ Str::limit($review->comment, 180) }}&rdquo;
                    </p>
                </div>
                <div class="pt-4 border-t border-[#f0eee6] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">{{ $review->user->name ?? 'Pembaca' }}</h4>
                        <p class="text-[11px] text-gray-400 truncate max-w-[180px]">{{ $review->book->title ?? 'Buku Pilihan' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border border-[#e8e6df] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center text-amber-500 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed font-serif-display italic mb-4">
                        &ldquo;Koleksinya sangat lengkap, terutama buku-buku sastra klasik dan pengayaan diri. Pengemasan sangat rapi dan buku tiba tanpa cacat sedikit pun.&rdquo;
                    </p>
                </div>
                <div class="pt-4 border-t border-[#f0eee6] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold text-xs">A</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Ananda Pratama</h4>
                        <p class="text-[11px] text-gray-400">Pembaca Setia &bull; Jakarta</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-[#e8e6df] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center text-amber-500 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed font-serif-display italic mb-4">
                        &ldquo;Navigasi situsnya sangat nyaman dipandang, mirip belanja di toko buku independen Eropa. Layanan verifikasi transaksinya juga sigap.&rdquo;
                    </p>
                </div>
                <div class="pt-4 border-t border-[#f0eee6] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold text-xs">D</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Dewi Anggraini</h4>
                        <p class="text-[11px] text-gray-400">Pustakawan &bull; Yogyakarta</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-[#e8e6df] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center text-amber-500 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed font-serif-display italic mb-4">
                        &ldquo;Program diskonnya sungguh membantu pelajar dan mahasiswa yang ingin membangun kebiasaan membaca. Terima kasih BOOKSPACE!&rdquo;
                    </p>
                </div>
                <div class="pt-4 border-t border-[#f0eee6] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold text-xs">R</div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Rizky Maulana</h4>
                        <p class="text-[11px] text-gray-400">Mahasiswa &bull; Bandung</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

@endsection


