@extends('layouts.bookspace')

@section('title', 'Invoice Pesanan #' . $order->code)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 md:py-12">

    {{-- ── Status Header Card ─────────────────────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-10 shadow-sm text-center mb-8">
        @php
            $isCancelled = $order->status === 'dibatalkan';
            $isPaid = in_array($order->status, ['dibayar', 'diproses', 'dikirim', 'selesai']);
        @endphp

        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 {{ $isCancelled ? 'bg-red-50 text-red-600' : ($isPaid ? 'bg-emerald-50 text-emerald-600' : 'bg-[#f4f2ec] text-[#0f2255]') }}">
            @if($isCancelled)
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>

        <h1 class="font-serif-display font-bold text-2xl sm:text-3xl text-[#0f2255] mb-1">
            {{ $isCancelled ? 'Pesanan Telah Dibatalkan' : 'Pesanan Berhasil Dicatat!' }}
        </h1>
        <p class="text-xs text-gray-500 font-mono tracking-widest uppercase">Nomor Invoice: #{{ $order->code }}</p>

        {{-- Status Pill --}}
        <div class="mt-4">
            @php
                $badgeClasses = match($order->status) {
                    'pending' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'menunggu_pembayaran' => 'bg-amber-50 text-amber-900 border-amber-200',
                    'menunggu_verifikasi' => 'bg-blue-50 text-blue-950 border-blue-200',
                    'dibayar' => 'bg-indigo-50 text-indigo-900 border-indigo-200',
                    'diproses' => 'bg-purple-50 text-purple-900 border-purple-200',
                    'dikirim' => 'bg-cyan-50 text-cyan-900 border-cyan-200',
                    'selesai' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
                    'dibatalkan' => 'bg-red-50 text-red-900 border-red-200',
                    default => 'bg-gray-100 text-gray-800 border-gray-300',
                };
            @endphp
            <span class="inline-block text-xs font-semibold px-4 py-1.5 rounded-full border {{ $badgeClasses }}">
                Status Saat Ini: {{ $order->statusLabel() }}
            </span>
        </div>

        {{-- Next Action CTA if still awaiting payment --}}
        @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
            <div class="mt-6">
                <a href="{{ route('checkout.payment', $order) }}" class="btn-orange text-xs font-semibold px-6 py-3 rounded-lg shadow-md">
                    Lanjut Bayar &amp; Unggah Bukti Transfer &rarr;
                </a>
            </div>
        @endif
    </div>

    {{-- ── Invoice Receipt Card ───────────────────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-10 shadow-sm space-y-6">

        {{-- Store & Invoice Meta --}}
        <div class="flex flex-col sm:flex-row justify-between pb-6 border-b border-[#f0eee6] gap-4">
            <div>
                <span class="font-serif-display font-bold text-xl text-[#0f2255] block">BOOKSPACE</span>
                <span class="text-xs text-gray-500">Toko Buku Online &amp; Sastra Pilihan</span>
            </div>
            <div class="sm:text-right text-xs text-gray-500">
                <p>Tanggal Pesanan: <strong class="text-gray-900">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                <p>Metode: <strong class="text-gray-900">{{ $order->latestPayment?->methodLabel() ?? 'Manual' }}</strong></p>
            </div>
        </div>

        {{-- Shipping & Recipient Info --}}
        <div class="grid sm:grid-cols-2 gap-6 pb-6 border-b border-[#f0eee6] text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block mb-1">Penerima Paket</span>
                <p class="font-bold text-gray-900 text-sm">{{ $order->recipient_name }}</p>
                <p class="text-gray-600 mt-0.5">{{ $order->phone }}</p>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block mb-1">Alamat Pengiriman</span>
                <p class="text-gray-800 leading-relaxed">{{ $order->address }}</p>
                @if($order->note)
                    <p class="mt-2 text-gray-500 italic bg-[#fafaf8] p-2 rounded border border-[#e8e6df]">
                        &ldquo;Catatan: {{ $order->note }}&rdquo;
                    </p>
                @endif
            </div>
        </div>

        {{-- Book Items Ordered --}}
        <div>
            <h3 class="font-serif-display font-bold text-base text-[#0f2255] mb-4">Rincian Buku yang Dipesan</h3>
            <div class="divide-y divide-[#f0eee6]">
                @foreach($order->items as $item)
                    <div class="py-3.5 flex items-center justify-between text-xs sm:text-sm">
                        <div class="flex items-center gap-3.5">
                            @if($item->book)
                                <div class="w-10 h-14 rounded overflow-hidden shadow-sm bg-white shrink-0 border border-[#e8e6df]">
                                    <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                </div>
                            @endif
                            <div>
                                <p class="font-serif-display font-bold text-gray-900 text-xs sm:text-sm">{{ $item->book_title }}</p>
                                <p class="text-[11px] text-gray-400 font-mono">{{ $item->quantity }} eksemplar &times; Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <span class="font-mono font-bold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <div class="pt-4 border-t border-[#f0eee6] space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal Buku</span>
                <span class="font-mono font-semibold text-gray-900">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Biaya Pengiriman</span>
                <span class="text-emerald-700 font-semibold">Gratis</span>
            </div>
            <div class="pt-3 border-t border-[#f0eee6] flex justify-between items-baseline">
                <span class="font-serif-display font-bold text-sm text-gray-900">Total Pembayaran</span>
                <span class="font-serif-display font-bold text-2xl text-[#e05c1a]">
                    Rp{{ number_format($order->total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Bottom Actions --}}
        <div class="pt-6 border-t border-[#f0eee6] flex flex-wrap gap-3 justify-between items-center text-xs">
            <a href="{{ route('user.orders') }}" class="text-[#0f2255] hover:underline font-semibold">
                &larr; Lihat Seluruh Riwayat Pesanan
            </a>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="border border-[#dcd8cd] hover:border-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors bg-[#fafaf8]">
                    Cetak Lembar Invoice
                </button>
                <a href="{{ route('books.index') }}" class="btn-primary text-xs py-2 px-4 rounded-lg">
                    Lanjut Belanja Buku
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

