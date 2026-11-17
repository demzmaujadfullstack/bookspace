@extends('layouts.bookspace')

@section('title', 'Daftar Favorit')

@section('content')
@php
    $savedBooks = collect($wishlistItems->items())->map(fn ($item) => $item->book)->filter()->values();
@endphp

<div class="shell py-6 md:py-8">

    <div class="mb-6">
        <x-breadcrumb :items="['Beranda' => route('home'), 'Akun Saya' => route('user.dashboard'), 'Daftar Favorit' => null]" class="mb-4" />
        <h1 class="page-title">Daftar Favorit</h1>
        <p class="muted mt-1">Buku yang Anda simpan untuk dibeli nanti. Harga dan stok mengikuti perubahan terbaru di katalog.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
        <x-account-nav class="lg:col-span-4 xl:col-span-3" />

        <div class="lg:col-span-8 xl:col-span-9">
            @if($savedBooks->isNotEmpty())
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-[13px] text-[#6b6f78]">
                        <span class="font-bold text-[#17181c]">{{ $wishlistItems->total() }}</span> judul tersimpan
                        &middot; <span class="font-bold text-[#17181c]">{{ $savedBooks->where('stock', '>', 0)->count() }}</span> tersedia
                    </p>
                    <a href="{{ route('books.index') }}" class="btn-ghost btn-sm">Jelajahi Buku Lain</a>
                </div>

                <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 sm:gap-4 xl:grid-cols-4">
                    @foreach($savedBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>

                @if($wishlistItems->hasPages())
                    <div class="mt-8">
                        {{ $wishlistItems->links() }}
                    </div>
                @endif
            @else
                <div class="card flex flex-col items-center px-6 py-14 text-center">
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-[#fdf5ef] text-[#e05c1a]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </span>
                    <h2 class="mt-4 font-serif-display text-[19px] font-bold text-[#0f2255]">Belum ada buku favorit</h2>
                    <p class="mt-1.5 max-w-sm text-[13px] leading-relaxed text-[#6b6f78]">
                        Tekan ikon hati pada sampul buku mana pun untuk menyimpannya di sini, lalu pantau stok dan harganya sebelum membeli.
                    </p>
                    <a href="{{ route('books.index') }}" class="btn-primary btn-sm mt-6">Mulai Jelajahi Katalog</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
