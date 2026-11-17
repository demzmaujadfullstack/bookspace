@extends('layouts.bookspace')

@section('title', 'Akun Saya')

@section('content')
@php
    $unpaid = $recentOrders->first(fn ($o) => in_array($o->status, [\App\Models\Order::PENDING, \App\Models\Order::MENUNGGU_PEMBAYARAN], true));
@endphp

<div class="shell py-6 md:py-8">

    <div class="mb-6">
        <x-breadcrumb :items="['Beranda' => route('home'), 'Akun Saya' => null]" class="mb-4" />
        <h1 class="page-title">Halo, {{ Str::before($user->name, ' ') }}</h1>
        <p class="muted mt-1">Pantau pesanan, kelola alamat, dan lanjutkan belanjaan Anda di satu tempat.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
        <x-account-nav :user="$user" class="lg:col-span-4 xl:col-span-3" />

        <div class="space-y-6 lg:col-span-8 xl:col-span-9">

            {{-- Tagihan yang menunggu tindakan --}}
            @if($unpaid)
                <div class="card !p-0 overflow-hidden border-[#f3d9c6]">
                    <div class="flex flex-wrap items-center justify-between gap-4 bg-[#fdf5ef] px-5 py-4">
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#e05c1a] text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <p class="text-[14px] font-bold text-[#8a3d0c]">Pesanan #{{ $unpaid->code }} menunggu pembayaran</p>
                                <p class="mt-0.5 text-[12px] text-[#a15b28]">Dibuat {{ $unpaid->created_at->translatedFormat('d M Y, H:i') }} &middot; Total Rp{{ number_format($unpaid->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('checkout.payment', $unpaid) }}" class="btn-orange btn-sm shrink-0">Bayar Sekarang</a>
                    </div>
                </div>
            @endif

            {{-- Statistik --}}
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach([
                    ['label' => 'Total pesanan', 'value' => $stats['total_orders'], 'note' => 'Sepanjang akun Anda', 'color' => '#0f2255'],
                    ['label' => 'Perlu tindakan', 'value' => $stats['pending_orders'], 'note' => 'Bayar atau verifikasi', 'color' => '#e05c1a'],
                    ['label' => 'Pesanan selesai', 'value' => $stats['completed_orders'], 'note' => 'Buku sudah diterima', 'color' => '#166534'],
                    ['label' => 'Nilai transaksi', 'value' => 'Rp'.number_format($stats['total_spent'], 0, ',', '.'), 'note' => 'Transaksi terverifikasi', 'color' => '#0f2255'],
                ] as $tile)
                    <div class="card card-pad !p-4">
                        <p class="eyebrow">{{ $tile['label'] }}</p>
                        <p class="mt-1.5 truncate font-serif-display text-[24px] font-bold leading-tight" style="color: {{ $tile['color'] }}">{{ $tile['value'] }}</p>
                        <p class="mt-1 text-[11px] text-[#9a9484]">{{ $tile['note'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Pesanan terbaru --}}
            <section class="card overflow-hidden">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                    <div>
                        <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Pesanan Terbaru</h2>
                        <p class="muted mt-0.5">Lima transaksi terakhir pada akun ini</p>
                    </div>
                    <a href="{{ route('user.orders') }}" class="text-[13px] font-semibold text-[#0f2255] hover:underline">Lihat semua pesanan &rarr;</a>
                </header>

                @if($recentOrders->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-[14px] font-semibold text-[#3f4451]">Belum ada pesanan di akun ini.</p>
                        <p class="muted mx-auto mt-1 max-w-sm">Mulai dari katalog, tambahkan buku ke keranjang, lalu selesaikan pembayaran.</p>
                        <a href="{{ route('books.index') }}" class="btn-primary btn-sm mt-4">Jelajahi Katalog Buku</a>
                    </div>
                @else
                    <ul class="divide-y divide-[#f0eee6]">
                        @foreach($recentOrders as $order)
                            <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('user.orders.show', $order) }}" class="font-mono text-[13px] font-bold text-[#0f2255] hover:underline">#{{ $order->code }}</a>
                                        <x-order-status :order="$order" />
                                    </div>
                                    <p class="mt-1 text-[12px] text-[#6b6f78]">
                                        {{ $order->created_at->translatedFormat('d M Y, H:i') }} &middot; {{ $order->items->count() }} judul
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-serif-display text-[16px] font-bold text-[#17181c]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                                    <a href="{{ route('user.orders.show', $order) }}" class="btn-ghost btn-sm shrink-0">Detail</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Rekomendasi singkat --}}
            @if($recommended->isNotEmpty())
                <section class="card overflow-hidden">
                    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                        <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Sedang Diskon</h2>
                        <a href="{{ route('books.index', ['promo' => 1]) }}" class="text-[13px] font-semibold text-[#0f2255] hover:underline">Semua promo &rarr;</a>
                    </header>
                    <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-3 xl:grid-cols-4">
                        @foreach($recommended as $promoBook)
                            @include('components.book-card', ['book' => $promoBook])
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
