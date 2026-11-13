@extends('layouts.bookspace')

@section('title', 'Daftar Buku Favorit — Koleksi Pilihan Anda')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12">

    {{-- ── Breadcrumb ────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-8 pb-4 border-b border-[#e8e6df]">
        <a href="{{ route('home') }}" class="hover:text-[#0f2255] transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#0f2255] transition-colors">Akun Saya</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Daftar Favorit</span>
    </nav>

    {{-- ── Page Header ───────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-[#e05c1a]">Perpustakaan Pribadi</span>
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-[#0f2255] mt-1">Daftar Buku Favorit</h1>
            <p class="text-xs text-gray-500 mt-2 font-serif italic">Simpan buku impian Anda dan pantau ketersediaan stoknya sebelum melakukan pembelian.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 bg-white px-3.5 py-1.5 rounded-full border border-[#e8e6df] font-medium shadow-sm">
                {{ $wishlistItems->total() }} buku tersimpan
            </span>
            <a href="{{ route('books.index') }}" class="btn-outline text-xs inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Jelajahi Buku Lain
            </a>
        </div>
    </div>

    {{-- ── Wishlist Grid or Empty State ──────────────────────── --}}
    @if($wishlistItems->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6">
            @foreach($wishlistItems as $item)
                @if($item->book)
                    <div class="relative group">
                        <x-book-card :book="$item->book" />

                        {{-- Quick Remove Button --}}
                        <form method="POST" action="{{ route('wishlist.toggle', $item->book) }}" class="absolute top-2 right-2 z-10">
                            @csrf
                            <button
                                type="submit"
                                title="Hapus dari Favorit"
                                class="w-7 h-7 bg-white/90 hover:bg-red-50 text-red-500 hover:text-red-700 rounded-full flex items-center justify-center shadow-md transition-all duration-200"
                                onclick="return confirm('Hapus buku ini dari daftar favorit Anda?')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($wishlistItems->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $wishlistItems->links() }}
            </div>
        @endif
    @else
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-12 text-center max-w-lg mx-auto shadow-sm my-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#f4f2ec] text-[#e05c1a] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="font-serif text-xl font-bold text-[#0f2255] mb-2">Belum Ada Buku di Favorit</h3>
            <p class="text-xs text-gray-500 mb-6 font-serif">Simpan buku yang ingin Anda beli atau baca nanti dengan menekan tombol simpan favorit di halaman buku.</p>
            <a href="{{ route('books.index') }}" class="btn-primary inline-flex items-center gap-2 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Jelajahi Katalog Buku
            </a>
        </div>
    @endif

</div>
@endsection

