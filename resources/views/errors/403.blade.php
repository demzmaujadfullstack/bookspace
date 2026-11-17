@extends('layouts.bookspace')

@section('title', 'Akses ditolak')

@section('content')
@php
    $reason = filled($exception?->getMessage())
        ? $exception->getMessage()
        : 'Halaman ini tidak dapat dibuka dari akun Anda saat ini.';
@endphp

<div class="shell py-12 md:py-16">
    <div class="card mx-auto max-w-xl overflow-hidden">

        <div class="border-b border-[#e6e3db] bg-[#fafaf8] px-6 py-9 text-center">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full border border-[#f3d6d3] bg-[#fdf2f2] text-[#b42318]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 3h.01M10.29 3.86l-7.06 12.2A1.9 1.9 0 004.9 19h14.2a1.9 1.9 0 001.67-2.94l-7.06-12.2a1.9 1.9 0 00-3.42 0z"/>
                </svg>
            </span>

            <p class="eyebrow mt-5 text-[#b42318]">Kesalahan 403 &middot; Akses ditolak</p>
            <h1 class="page-title mt-1.5">Anda tidak berhak masuk ke sini</h1>
            <p class="mx-auto mt-2.5 max-w-md text-[14px] leading-relaxed text-[#3f4451]">{{ $reason }}</p>
        </div>

        <div class="card-pad">
            <p class="text-[12px] font-semibold uppercase tracking-wider text-[#6b6f78]">Yang bisa Anda lakukan</p>
            <ul class="mt-3 space-y-2.5 text-[13px] text-[#3f4451]">
                <li class="flex gap-2.5">
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#e05c1a]"></span>
                    Cek kembali alamat yang Anda buka &mdash; kemungkinan halaman tersebut khusus administrator.
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#e05c1a]"></span>
                    <span>
                        Masuk memakai akun dengan hak akses yang sesuai.
                        @auth
                            <span class="mt-0.5 block text-[12px] text-[#6b6f78]">
                                Anda sedang masuk sebagai
                                <strong class="font-semibold text-[#0f2255]">{{ auth()->user()->name }}</strong>
                                ({{ auth()->user()->role }}).
                            </span>
                        @endauth
                    </span>
                </li>
                <li class="flex gap-2.5">
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[#e05c1a]"></span>
                    Lanjutkan belanja dari katalog, atau tanyakan lewat Pusat Bantuan.
                </li>
            </ul>

            <div class="mt-6 flex flex-wrap gap-2.5">
                <a href="{{ route('home') }}" class="btn-primary">Kembali ke Beranda</a>
                <a href="{{ route('books.index') }}" class="btn-outline">Lihat Katalog</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn-outline">Buka Dasbor Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-outline">Masuk ke Akun</a>
                @endauth
            </div>

            <p class="mt-5 border-t border-[#f0eee6] pt-4 text-[12px] text-[#9a9484]">
                Masih merasa seharusnya punya akses?
                <a href="{{ route('help.index') }}" class="font-semibold text-[#0f2255] hover:underline">Hubungi Pusat Bantuan</a>
                dengan menyebutkan alamat halaman ini.
            </p>
        </div>
    </div>
</div>
@endsection
