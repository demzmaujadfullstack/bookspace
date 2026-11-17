@extends('layouts.bookspace')

@section('title', 'Halaman tidak ditemukan')

@section('content')
<div class="shell py-12 md:py-16">
    <div class="card mx-auto max-w-xl overflow-hidden">

        <div class="border-b border-[#e6e3db] bg-[#fafaf8] px-6 py-9 text-center">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full border border-[#dcd8cd] bg-white text-[#0f2255]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
            </span>

            <p class="eyebrow mt-5 text-[#e05c1a]">Kesalahan 404 &middot; Tidak ditemukan</p>
            <h1 class="page-title mt-1.5">Halamannya tidak ada di rak kami</h1>
            <p class="mx-auto mt-2.5 max-w-md text-[14px] leading-relaxed text-[#3f4451]">
                Buku atau halaman yang Anda cari mungkin sudah dihapus, salah ketik alamat, atau tautannya sudah berpindah.
            </p>
        </div>

        <div class="card-pad">
            <form method="GET" action="{{ route('books.index') }}" class="flex gap-2.5">
                <input type="search" name="q" placeholder="Cari judul, penulis, atau ISBN..."
                       class="field flex-1" autofocus>
                <button type="submit" class="btn-primary shrink-0">Cari</button>
            </form>

            <div class="mt-6 flex flex-wrap gap-2.5">
                <a href="{{ route('home') }}" class="btn-outline">Kembali ke Beranda</a>
                <a href="{{ route('books.index') }}" class="btn-outline">Semua Koleksi</a>
                <a href="{{ route('help.index') }}" class="btn-ghost">Lapor ke Pusat Bantuan</a>
            </div>
        </div>
    </div>
</div>
@endsection
