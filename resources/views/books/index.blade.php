@extends('layouts.bookspace')

@section('title', 'Katalog Buku Lengkap')

@php
    $categoryName = request('category')
        ? (\App\Models\Category::where('slug', request('category'))->value('name') ?? request('category'))
        : null;

    $pageTitle = request('search')
        ? 'Hasil pencarian: "'.request('search').'"'
        : ($categoryName ? 'Kategori '.$categoryName : (request('promo') ? 'Buku Promo & Diskon' : 'Semua Koleksi Buku'));

    $activeFilters = collect([
        'search' => request('search') ? 'Pencarian: '.request('search') : null,
        'category' => $categoryName ? 'Kategori: '.$categoryName : null,
        'promo' => request('promo') ? 'Hanya diskon' : null,
    ])->filter()->when(request('min_price') || request('max_price'), fn ($c) => $c->put('price', 'Rentang harga'))->filter();

    $sortLinks = [
        'newest' => 'Terbaru',
        'bestseller' => 'Terlaris',
        'price_asc' => 'Harga terendah',
        'price_desc' => 'Harga tertinggi',
        'title_asc' => 'Judul A&ndash;Z',
    ];
@endphp

@section('content')
<div class="shell py-6 md:py-8" x-data="{ filters: false }">

    <x-breadcrumb :items="[
        'Beranda' => route('home'),
        'Katalog' => request('category') || request('search') || request('promo') ? route('books.index') : null,
        ($categoryName ?: ($pageTitle)) => null,
    ]" class="mb-4" />

    <div class="mb-6 border-b border-[#e6e3db] pb-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="page-title">{!! $pageTitle !!}</h1>
                <p class="muted mt-1">{{ number_format($books->total(), 0, ',', '.') }} judul tersedia dan siap dikirim.</p>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto chip-rail">
                @foreach($sortLinks as $val => $label)
                    <a href="{{ route('books.index', array_merge(request()->except('sort', 'page'), ['sort' => $val])) }}"
                       class="pill shrink-0 border transition-colors {{ $sort === $val ? 'border-[#0f2255] bg-[#0f2255] text-white' : 'border-[#dcd8cd] bg-white text-[#3f4451] hover:border-[#0f2255] hover:text-[#0f2255]' }}">
                        {!! $label !!}
                    </a>
                @endforeach
            </div>
        </div>

        @if($activeFilters->isNotEmpty())
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#f0eee6] pt-3.5">
                <span class="eyebrow">Filter aktif</span>
                @foreach($activeFilters as $key => $label)
                    <a href="{{ route('books.index', request()->except($key === 'price' ? ['min_price', 'max_price'] : [$key], 'page')) }}"
                       class="pill border border-[#e6e3db] bg-white text-[#3f4451] transition-colors hover:border-[#b42318] hover:text-[#b42318]">
                        {{ $label }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                <a href="{{ route('books.index') }}" class="text-[12px] font-semibold text-[#b42318] hover:underline">Hapus semua</a>
            </div>
        @endif
    </div>

    <div class="flex gap-8">
        {{-- Rail filter (desktop) --}}
        <aside class="hidden w-[264px] shrink-0 lg:block">
            <div class="card card-pad sticky top-[150px]">
                @include('books.partials.filter-form')
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            {{-- Pemicu sheet filter (mobile) --}}
            <div class="mb-4 flex items-center justify-between gap-3 lg:hidden">
                <button type="button" @click="filters = true" class="btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zM6 10h12a1 1 0 010 2H6a1 1 0 010-2zM9 16h6a1 1 0 010 2H9a1 1 0 010-2z"/></svg>
                    Saring
                    @if($activeFilters->isNotEmpty())
                        <span class="pill bg-[#e05c1a] text-white">{{ $activeFilters->count() }}</span>
                    @endif
                </button>
                <p class="muted">{{ number_format($books->total(), 0, ',', '.') }} buku</p>
            </div>

            @if($books->isEmpty())
                <div class="card mt-2 p-10 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#f4f2ec] text-[#0f2255]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <h2 class="section-title mt-4">Tidak ada buku yang cocok</h2>
                    <p class="muted mx-auto mt-2 max-w-md">
                        Coba kata kunci lain, lebar rentang harganya, atau hapus filter yang sedang aktif.
                    </p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('books.index') }}" class="btn-primary inline-flex h-10 items-center rounded-lg px-5 text-sm font-semibold">Lihat semua buku</a>
                        <a href="{{ route('help.index') }}" class="btn-ghost inline-flex h-10 items-center rounded-lg px-5 text-sm font-semibold">Butuh bantuan?</a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:gap-5">
                    @foreach($books as $book)
                        @include('components.book-card', ['book' => $book])
                    @endforeach
                </div>

                @if($books->hasPages())
                    <div class="mt-8 border-t border-[#e6e3db] pt-6">
                        {{ $books->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Sheet filter (mobile) --}}
    <div x-show="filters" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div x-show="filters" x-transition.opacity class="absolute inset-0 bg-[#0b1736]/50" @click="filters = false"></div>
        <div x-show="filters"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-2xl bg-white p-4 shadow-2xl">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[15px] font-bold text-[#0f2255]">Saring Koleksi</h2>
                <button type="button" @click="filters = false" aria-label="Tutup filter" class="grid h-9 w-9 place-items-center rounded-lg text-[#3f4451] hover:bg-[#f4f2ec]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @include('books.partials.filter-form')
        </div>
    </div>
</div>
@endsection
