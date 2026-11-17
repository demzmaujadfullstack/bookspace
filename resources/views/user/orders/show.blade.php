@extends('layouts.bookspace')

@section('title', 'Detail Pesanan #' . $order->code)

@section('content')
@php
    $payment = $order->latestPayment;
    $isPaid = in_array($order->status, \App\Models\Order::STOCK_REDUCED_STATUSES, true);
    $itemTotal = $order->items->sum(fn ($item) => $item->price * $item->quantity);
    $discountTotal = max(0, $itemTotal - $order->total);
@endphp

<div class="shell-narrow py-6 md:py-8">

    <x-breadcrumb :items="[
        'Beranda' => route('home'),
        'Akun Saya' => route('user.dashboard'),
        'Pesanan' => route('user.orders'),
        '#'.$order->code => null,
    ]" class="mb-4" />

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="page-title">Pesanan #{{ $order->code }}</h1>
                <x-order-status :order="$order" />
            </div>
            <p class="muted mt-1.5">Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB &middot; {{ $order->items->count() }} judul buku</p>
        </div>
        <div class="no-print flex items-center gap-2">
            <button type="button" onclick="window.print()" class="btn-ghost btn-sm">Cetak Faktur</button>
            <a href="{{ route('user.orders') }}" class="btn-ghost btn-sm">&larr; Semua Pesanan</a>
        </div>
    </div>

    {{-- Status & langkah --}}
    <section class="card mb-6 overflow-hidden">
        <div class="p-5">
            <x-order-stepper :order="$order" />
        </div>

        @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
            <div class="no-print flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e3db] bg-[#fdf5ef] px-5 py-4">
                <div>
                    <p class="text-[13px] font-bold text-[#8a3d0c]">Pesanan menunggu pembayaran</p>
                    <p class="mt-0.5 text-[12px] text-[#a15b28]">Transfer sesuai total tagihan, lalu unggah buktinya agar pesanan segera diproses.</p>
                </div>
                <a href="{{ route('checkout.payment', $order) }}" class="btn-orange btn-sm shrink-0">Unggah Bukti Pembayaran</a>
            </div>
        @elseif($order->status === \App\Models\Order::MENUNGGU_VERIFIKASI)
            <div class="border-t border-[#e6e3db] bg-[#f2f7fd] px-5 py-4">
                <p class="text-[13px] font-bold text-[#1d4ed8]">Bukti pembayaran sedang diverifikasi admin</p>
                <p class="mt-0.5 text-[12px] text-[#3b5fa8]">Setelah disetujui, stok dipotong dan paket disiapkan untuk dikirim.</p>
            </div>
        @elseif($order->status === \App\Models\Order::DIKIRIM)
            <div class="no-print flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e3db] bg-[#eef7f9] px-5 py-4">
                <div>
                    <p class="text-[13px] font-bold text-[#0d6b82]">Paket dalam pengiriman</p>
                    <p class="mt-0.5 text-[12px] text-[#33788c]">
                        @if($order->tracking_number)
                            Resi <span class="font-mono font-bold">{{ $order->tracking_number }}</span>
                            @if($order->shipped_at) &middot; dikirim {{ $order->shipped_at->translatedFormat('d M Y') }} @endif
                        @else
                            Paket sudah meninggalkan gudang. Nomor resi akan ditampilkan di halaman ini.
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('user.orders.receive', $order) }}" class="shrink-0"
                      onsubmit="return confirm('Konfirmasi pesanan #{{ $order->code }} sudah Anda terima?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary btn-sm">Pesanan Sudah Diterima</button>
                </form>
            </div>
        @endif
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- Barang --}}
        <div class="lg:col-span-7">
            <section class="card overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Daftar Buku Dipesan</h2>
                </header>
                <ul class="divide-y divide-[#f0eee6] px-5">
                    @foreach($order->items as $item)
                        <li class="flex items-center gap-3.5 py-4">
                            @if($item->book)
                                <a href="{{ route('books.show', $item->book) }}" class="h-20 w-14 shrink-0 overflow-hidden rounded border border-[#e6e3db] bg-white">
                                    <img src="{{ $item->book->coverUrl() }}" alt="Sampul {{ $item->book_title }}" class="h-full w-full object-cover" loading="lazy"
                                         onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                </a>
                            @else
                                <span class="grid h-20 w-14 shrink-0 place-items-center rounded border border-dashed border-[#dcd8cd] bg-[#fafaf8] text-[10px] text-[#9a9484]">buku<br>hapus</span>
                            @endif

                            <div class="min-w-0 flex-1">
                                <a href="{{ $item->book ? route('books.show', $item->book) : '#' }}"
                                   class="block font-serif-display text-[14px] font-bold leading-snug text-[#0f2255] hover:text-[#e05c1a]">{{ $item->book_title }}</a>
                                <p class="mt-1 text-[12px] text-[#6b6f78]">{{ $item->quantity }} &times; Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                @if($item->discount_percent > 0)
                                    <span class="pill mt-1.5 bg-[#fdece2] font-bold text-[#c54e12]">Diskon {{ $item->discount_percent }}%</span>
                                @endif
                            </div>

                            <span class="shrink-0 text-[14px] font-bold text-[#17181c]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>

                @if($payment)
                    <footer class="border-t border-[#e6e3db] bg-[#fafaf8] px-5 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="eyebrow">Pembayaran</p>
                                <p class="mt-1 text-[13px] font-semibold text-[#17181c]">{{ $payment->methodLabel() }} &middot; {{ $payment->statusLabel() }}</p>
                                @if($payment->verified_at)
                                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">Diverifikasi {{ $payment->verified_at->translatedFormat('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>
                            @if($payment->proof_image)
                                <a href="{{ route('payment.proof', $payment) }}" target="_blank" class="no-print inline-flex items-center gap-2 text-[12px] font-semibold text-[#0f2255] hover:underline">
                                    <img src="{{ route('payment.proof', $payment) }}" alt="Bukti transfer" class="h-12 w-12 rounded border border-[#e6e3db] object-cover">
                                    Lihat bukti
                                </a>
                            @endif
                        </div>
                    </footer>
                @endif
            </section>
        </div>

        {{-- Ringkasan & tujuan --}}
        <div class="space-y-6 lg:col-span-5">
            <section class="card overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Rincian Biaya</h2>
                </header>
                <dl class="space-y-2.5 px-5 py-4 text-[13px]">
                    <div class="flex justify-between">
                        <dt class="text-[#6b6f78]">Subtotal ({{ $order->items->sum('quantity') }} eks)</dt>
                        <dd class="font-semibold text-[#17181c]">Rp{{ number_format($itemTotal, 0, ',', '.') }}</dd>
                    </div>
                    @if($discountTotal > 0)
                        <div class="flex justify-between">
                            <dt class="text-[#6b6f78]">Potongan diskon</dt>
                            <dd class="font-semibold text-[#c54e12]">-Rp{{ number_format($discountTotal, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-[#6b6f78]">Ongkos kirim</dt>
                        <dd class="font-semibold {{ $isPaid ? 'text-emerald-700' : 'text-[#17181c]' }}">{{ $isPaid ? 'Gratis' : 'Dihitung admin' }}</dd>
                    </div>
                </dl>
                <div class="flex items-baseline justify-between border-t border-[#e6e3db] px-5 py-4">
                    <span class="text-[13px] font-bold text-[#17181c]">Total dibayar</span>
                    <span class="font-serif-display text-[22px] font-bold text-[#0f2255]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </section>

            <section class="card overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Dikirim ke</h2>
                </header>
                <div class="space-y-3 px-5 py-4 text-[13px]">
                    <div>
                        <p class="eyebrow">Penerima</p>
                        <p class="mt-1 font-semibold text-[#17181c]">{{ $order->recipient_name }}</p>
                        <p class="text-[#6b6f78]">{{ $order->phone }}</p>
                    </div>
                    <div>
                        <p class="eyebrow">Alamat</p>
                        <p class="mt-1 leading-relaxed text-[#3f4451]">{{ $order->address }}</p>
                    </div>
                    @if($order->note)
                        <p class="rounded-lg border border-[#e6e3db] bg-[#fafaf8] px-3 py-2 text-[12px] italic text-[#6b6f78]">&ldquo;{{ $order->note }}&rdquo;</p>
                    @endif
                </div>
            </section>

            @if($order->canCancel())
                <form method="POST" action="{{ route('checkout.cancel', $order) }}" class="no-print card !p-4"
                      onsubmit="return confirm('Batalkan pesanan #{{ $order->code }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-[#f3c9c2] bg-[#fdf1ef] py-2.5 text-[13px] font-semibold text-[#b42318] transition-colors hover:bg-[#fbe3df]">
                        Batalkan Pesanan Ini
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
