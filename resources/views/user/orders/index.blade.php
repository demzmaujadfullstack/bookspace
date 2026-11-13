@extends('layouts.bookspace')

@section('title', 'Riwayat Pesanan Buku Saya')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-[#e8e6df]">
        <div>
            <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255]">Riwayat Pesanan Buku</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar seluruh transaksi pembelian dan status pengiriman pesanan Anda.</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn-primary text-xs py-2.5 px-4 rounded-lg shadow-sm">
            + Jelajahi Koleksi Baru
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-16 text-center shadow-sm my-6">
            <div class="w-16 h-16 bg-[#f4f2ec] rounded-full flex items-center justify-center mx-auto mb-4 text-[#0f2255]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="font-serif-display font-bold text-xl text-gray-900 mb-1">Belum Ada Pesanan Buku</h3>
            <p class="text-xs sm:text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                Anda belum pernah melakukan pemesanan buku di BOOKSPACE. Mulailah berbelanja judul favorit Anda sekarang.
            </p>
            <a href="{{ route('books.index') }}" class="btn-orange text-xs font-semibold px-6 py-3 rounded-lg shadow-md">
                Mulai Belanja Buku Sekarang
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($orders as $order)
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm hover:border-[#0f2255]/30 transition-all">
                {{-- Header bar --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#f0eee6] text-xs">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-gray-900 text-sm">#{{ $order->code }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-gray-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>

                    @php
                        $badgeClasses = match($order->status) {
                            'pending' => 'bg-gray-100 text-gray-800 border-gray-300',
                            'menunggu_pembayaran' => 'bg-amber-50 text-amber-900 border-amber-200',
                            'menunggu_verifikasi' => 'bg-blue-50 text-blue-900 border-blue-200',
                            'dibayar' => 'bg-indigo-50 text-indigo-900 border-indigo-200',
                            'diproses' => 'bg-purple-50 text-purple-900 border-purple-200',
                            'dikirim' => 'bg-cyan-50 text-cyan-900 border-cyan-200',
                            'selesai' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
                            'dibatalkan' => 'bg-red-50 text-red-900 border-red-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-300',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full font-semibold text-[11px] border {{ $badgeClasses }}">
                        {{ $order->statusLabel() }}
                    </span>
                </div>

                {{-- Book Items inside order --}}
                <div class="py-4 space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3.5 min-w-0">
                            @if($item->book)
                                <div class="w-10 h-14 rounded overflow-hidden shadow-sm bg-white shrink-0 border border-[#e8e6df]">
                                    <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-serif-display font-bold text-gray-900 text-xs sm:text-sm truncate">{{ $item->book_title }}</p>
                                <p class="text-[11px] text-gray-400 font-mono">{{ $item->quantity }} eksemplar &times; Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <span class="font-mono font-bold text-gray-900 shrink-0">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Bottom strip --}}
                <div class="pt-4 border-t border-[#f0eee6] flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-gray-500">Total Pembayaran:</span>
                        <span class="font-serif-display font-bold text-base text-[#e05c1a] ml-1.5 font-mono">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-gray-400 ml-2">({{ $order->latestPayment?->methodLabel() ?? 'Manual' }})</span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
                            <a href="{{ route('checkout.payment', $order) }}" class="btn-orange text-xs py-2 px-4 rounded-lg shadow-sm">
                                Bayar &amp; Unggah Bukti &rarr;
                            </a>
                        @endif
                        <a href="{{ route('user.orders.show', $order) }}" class="border border-[#dcd8cd] hover:border-[#0f2255] text-[#0f2255] px-4 py-2 rounded-lg font-semibold transition-colors bg-[#fafaf8]">
                            Lihat Rincian &amp; Faktur
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-8 flex justify-center">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

