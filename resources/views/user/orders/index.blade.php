@extends('layouts.bookspace')

@section('title', 'Pesanan Saya')

@section('content')
@php
    $user = auth()->user();
    $groupCount = fn (array $statuses) => $statusCounts->intersect($statuses)->sum();
@endphp

<div class="shell py-6 md:py-8">

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <x-breadcrumb :items="['Beranda' => route('home'), 'Akun Saya' => route('user.dashboard'), 'Pesanan' => null]" class="mb-4" />
            <h1 class="page-title">Pesanan Saya</h1>
            <p class="muted mt-1">{{ number_format($orders->total(), 0, ',', '.') }} transaksi tercatat pada akun ini.</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn-ghost btn-sm">Belanja Buku Lagi</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
        <x-account-nav :user="$user" class="lg:col-span-4 xl:col-span-3" />

        <div class="lg:col-span-8 xl:col-span-9">

            {{-- Chip penyaring status --}}
            <div class="chip-rail mb-5 flex gap-2 overflow-x-auto pb-1">
                <a href="{{ route('user.orders') }}"
                   class="pill shrink-0 border transition-colors {{ $status === null ? 'border-[#0f2255] bg-[#0f2255] text-white' : 'border-[#dcd8cd] bg-white text-[#3f4451] hover:border-[#0f2255] hover:text-[#0f2255]' }}">
                    Semua <span class="opacity-70">{{ $orders->total() }}</span>
                </a>
                @foreach($groups as $key => $group)
                    @php $count = $groupCount($group['statuses']); @endphp
                    @if($count > 0)
                        <a href="{{ route('user.orders', ['status' => $key]) }}"
                           class="pill shrink-0 border transition-colors {{ $status === $key ? 'border-[#0f2255] bg-[#0f2255] text-white' : 'border-[#dcd8cd] bg-white text-[#3f4451] hover:border-[#0f2255] hover:text-[#0f2255]' }}">
                            {{ $group['label'] }} <span class="opacity-70">{{ $count }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            @if($orders->isEmpty())
                <div class="card !p-10 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#f4f2ec] text-[#0f2255]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h2 class="section-title mt-4">{{ $status ? 'Tidak ada pesanan pada penyaring ini' : 'Belum Ada Pesanan' }}</h2>
                    <p class="muted mx-auto mt-2 max-w-sm">
                        {{ $status ? 'Coba pilih status lain, atau lihat seluruh riwayat transaksi Anda.' : 'Mulai belanja judul favorit Anda; pesanan akan muncul di sini setelah checkout selesai.' }}
                    </p>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2.5">
                        <a href="{{ route('books.index') }}" class="btn-primary">Jelajahi Katalog Buku</a>
                        @if($status)
                            <a href="{{ route('user.orders') }}" class="btn-ghost">Lihat Semua Pesanan</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <article class="card overflow-hidden">
                            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e3db] bg-[#fafaf8] px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="font-mono text-[13px] font-bold text-[#0f2255]">#{{ $order->code }}</span>
                                    <x-order-status :order="$order" />
                                    <span class="text-[12px] text-[#6b6f78]">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <span class="font-serif-display text-[16px] font-bold text-[#17181c]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </header>

                            <div class="px-5 py-4">
                                <x-order-stepper :order="$order" />

                                <ul class="mt-4 space-y-3">
                                    @foreach($order->items as $item)
                                        <li class="flex items-center gap-3">
                                            @if($item->book)
                                                <a href="{{ route('books.show', $item->book) }}" class="h-14 w-10 shrink-0 overflow-hidden rounded border border-[#e6e3db] bg-white">
                                                    <img src="{{ $item->book->coverUrl() }}" alt="" class="h-full w-full object-cover" loading="lazy"
                                                         onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                                </a>
                                            @else
                                                <span class="grid h-14 w-10 shrink-0 place-items-center rounded border border-[#e6e3db] bg-[#f4f2ec] text-[10px] text-[#9a9484]">tidak<br>ada</span>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-[13px] font-semibold text-[#17181c]">{{ $item->book_title }}</p>
                                                <p class="mt-0.5 text-[12px] text-[#6b6f78]">{{ $item->quantity }} &times; Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                            <span class="shrink-0 text-[13px] font-bold text-[#0f2255]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e3db] px-5 py-3.5">
                                <p class="text-[12px] text-[#6b6f78]">
                                    {{ $order->items->sum('quantity') }} eksemplar &middot;
                                    Bayar dengan {{ $order->latestPayment?->methodLabel() ?? 'metode yang dipilih saat checkout' }}
                                    @if($order->tracking_number)
                                        &middot; Resi <span class="font-mono text-[#17181c]">{{ $order->tracking_number }}</span>
                                    @endif
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
                                        <a href="{{ route('checkout.payment', $order) }}" class="btn-orange btn-sm">Lanjutkan Pembayaran</a>
                                    @endif
                                    @if($order->canConfirmReceipt())
                                        <form method="POST" action="{{ route('user.orders.receive', $order) }}"
                                              onsubmit="return confirm('Konfirmasi pesanan #{{ $order->code }} sudah Anda terima?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-primary btn-sm">Pesanan Sudah Diterima</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('user.orders.show', $order) }}" class="btn-ghost btn-sm">Lihat Rincian</a>
                                </div>
                            </footer>
                        </article>
                    @endforeach
                </div>

                @if($orders->hasPages())
                    <div class="mt-6 border-t border-[#e6e3db] pt-5">{{ $orders->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
