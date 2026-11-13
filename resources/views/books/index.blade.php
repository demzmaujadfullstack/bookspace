@extends('layouts.bookspace')

@section('title', 'Katalog Buku Lengkap')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-10">

    {{-- ── Breadcrumb & Header ────────────────────────────────── --}}
    <div class="mb-8 pb-5 border-b border-[#e8e6df]">
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-[#0f2255] transition-colors">Beranda</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Katalog Buku</span>
            @if(request('category'))
                <span>/</span>
                <span class="text-[#e05c1a] font-medium">{{ \App\Models\Category::where('slug', request('category'))->value('name') ?? request('category') }}</span>
            @endif
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255]">
                    @if(request('search'))
                        Hasil Pencarian: &ldquo;{{ request('search') }}&rdquo;
                    @elseif(request('category'))
                        Koleksi {{ \App\Models\Category::where('slug', request('category'))->value('name') ?? 'Kategori' }}
                    @elseif(request('promo'))
                        Buku Pilihan Diskon &amp; Promo
                    @else
                        Seluruh Koleksi Buku
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Menemukan <strong class="text-gray-800">{{ $books->total() }}</strong> judul buku berkualitas siap kirim.
                </p>
            </div>

            {{-- Sorting Bar --}}
            <div class="flex items-center gap-3">
                <label for="catalog-sort" class="text-xs text-gray-500 font-medium whitespace-nowrap">Urutkan Berdasarkan:</label>
                <select id="catalog-sort" onchange="window.location.href=this.value" class="text-xs sm:text-sm bg-white border border-[#dcd8cd] rounded-lg px-3 py-2 text-gray-800 focus:outline-none focus:border-[#0f2255] focus:ring-1 focus:ring-[#0f2255]">
                    @php
                        $baseQuery = request()->except('sort', 'page');
                    @endphp
                    @foreach([
                        'newest' => 'Terbaru & Terkini',
                        'price_asc' => 'Harga: Terendah ke Tertinggi',
                        'price_desc' => 'Harga: Tertinggi ke Terendah',
                        'title_asc' => 'Judul Buku: A - Z',
                    ] as $val => $label)
                        <option value="{{ route('books.index', array_merge($baseQuery, ['sort' => $val])) }}" {{ $sort === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Active Filters Pills --}}
        @if(request('search') || request('category') || request('promo') || request('min_price') || request('max_price'))
            <div class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-[#f0eee6]">
                <span class="text-xs text-gray-400 font-medium">Filter Aktif:</span>

                @if(request('search'))
                    <a href="{{ route('books.index', request()->except('search', 'page')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#f4f2ec] text-[#0f2255] text-xs hover:bg-[#e8e6df] transition-colors">
                        <span>Pencarian: {{ request('search') }}</span>
                        <span class="text-gray-400 hover:text-red-500">✕</span>
                    </a>
                @endif

                @if(request('category'))
                    <a href="{{ route('books.index', request()->except('category', 'page')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#f4f2ec] text-[#0f2255] text-xs hover:bg-[#e8e6df] transition-colors">
                        <span>Kategori: {{ \App\Models\Category::where('slug', request('category'))->value('name') }}</span>
                        <span class="text-gray-400 hover:text-red-500">✕</span>
                    </a>
                @endif

                @if(request('promo'))
                    <a href="{{ route('books.index', request()->except('promo', 'page')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-orange-100 text-[#e05c1a] text-xs hover:bg-orange-200 transition-colors">
                        <span>Hanya Diskon</span>
                        <span class="text-gray-400 hover:text-red-500">✕</span>
                    </a>
                @endif

                @if(request('min_price') || request('max_price'))
                    <a href="{{ route('books.index', request()->except('min_price', 'max_price', 'page')) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#f4f2ec] text-[#0f2255] text-xs hover:bg-[#e8e6df] transition-colors">
                        <span>Rentang Harga</span>
                        <span class="text-gray-400 hover:text-red-500">✕</span>
                    </a>
                @endif

                <a href="{{ route('books.index') }}" class="text-xs text-red-600 hover:underline ml-2">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    {{-- ── Main Layout: Sidebar Filter + Product Grid ─────────── --}}
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Mobile Filter Trigger --}}
        <div class="lg:hidden flex items-center justify-between pb-3 border-b border-[#e8e6df]">
            <button onclick="document.getElementById('catalog-filter-sidebar').classList.toggle('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#dcd8cd] rounded-lg text-xs font-semibold text-gray-800 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#0f2255]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zM6 10h12a1 1 0 010 2H6a1 1 0 010-2zM9 16h6a1 1 0 010 2H9a1 1 0 010-2z"/>
                </svg>
                <span>Saring &amp; Filter Koleksi</span>
            </button>
            <span class="text-xs text-gray-500">{{ $books->total() }} buku</span>
        </div>

        {{-- Sidebar Filter --}}
        <aside id="catalog-filter-sidebar" class="w-full lg:w-64 shrink-0 hidden lg:block">
            <div class="bg-white border border-[#e8e6df] rounded-xl p-5 sticky top-28 shadow-sm space-y-6">
                <form id="filter-form" method="GET" action="{{ route('books.index') }}">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                    {{-- Header Sidebar --}}
                    <div class="flex items-center justify-between pb-3 border-b border-[#f0eee6]">
                        <h3 class="font-serif-display font-bold text-sm text-[#0f2255]">Filter Buku</h3>
                        <a href="{{ route('books.index') }}" class="text-[11px] text-[#e05c1a] hover:underline">Reset</a>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="pt-4">
                        <h4 class="font-semibold text-xs text-gray-800 uppercase tracking-wider mb-3">Genre / Kategori</h4>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1 text-sm custom-scrollbar">
                            <label class="flex items-center justify-between text-xs py-1 px-1.5 rounded hover:bg-[#fafaf8] cursor-pointer {{ !request('category') ? 'font-bold text-[#0f2255]' : 'text-gray-600' }}">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#0f2255] focus:ring-0">
                                    <span>Semua Kategori</span>
                                </span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center justify-between text-xs py-1 px-1.5 rounded hover:bg-[#fafaf8] cursor-pointer {{ request('category') === $cat->slug ? 'font-bold text-[#0f2255]' : 'text-gray-600' }}">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} onchange="this.form.submit()" class="text-[#0f2255] focus:ring-0">
                                    <span>{{ $cat->name }}</span>
                                </span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $cat->books_count }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-[#f0eee6] pt-4">
                        <h4 class="font-semibold text-xs text-gray-800 uppercase tracking-wider mb-3">Rentang Harga (Rp)</h4>
                        <div class="space-y-2">
                            <input
                                type="number"
                                name="min_price"
                                placeholder="Harga Min (Rp)"
                                value="{{ request('min_price') }}"
                                class="w-full text-xs border border-[#dcd8cd] rounded-lg px-3 py-2 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255]"
                            >
                            <input
                                type="number"
                                name="max_price"
                                placeholder="Harga Max (Rp)"
                                value="{{ request('max_price') }}"
                                class="w-full text-xs border border-[#dcd8cd] rounded-lg px-3 py-2 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255]"
                            >
                        </div>
                    </div>

                    <div class="border-t border-[#f0eee6] pt-4">
                        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="promo"
                                value="1"
                                {{ request('promo') ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-gray-300 text-[#e05c1a] focus:ring-[#e05c1a]"
                            >
                            <span class="font-medium">Hanya Buku Berdiskon</span>
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white py-2.5 rounded-lg text-xs font-semibold tracking-wide transition-colors shadow-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        {{-- ── Main Books Grid ────────────────────────────────── --}}
        <div class="flex-1 min-w-0">
            @if($books->isEmpty())
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-12 text-center my-6">
                    <div class="w-16 h-16 bg-[#f5f1ea] rounded-full flex items-center justify-center mx-auto mb-4 text-[#0f2255]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="font-serif-display font-bold text-xl text-gray-900 mb-2">Tidak Ada Buku yang Sesuai</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                        Maaf, kami tidak menemukan buku dengan kriteria pencarian Anda. Silakan coba kata kunci lain atau setel ulang filter.
                    </p>
                    <a href="{{ route('books.index') }}" class="btn-orange text-xs font-semibold px-6 py-2.5 rounded-lg shadow-sm">
                        Kembali ke Seluruh Buku
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-4 md:gap-5">
                    @foreach($books as $book)
                        @include('components.book-card', ['book' => $book])
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($books->hasPages())
                    <div class="mt-10 pt-6 border-t border-[#e8e6df] flex justify-center">
                        {{ $books->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

