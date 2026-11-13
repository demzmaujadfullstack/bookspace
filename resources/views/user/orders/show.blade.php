@extends('layouts.bookspace')

@section('title', 'Detail Pesanan #' . $order->code)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    {{-- Top nav & Title --}}
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#e8e6df]">
        <div>
            <a href="{{ route('user.orders') }}" class="text-xs text-gray-500 hover:text-[#0f2255] transition-colors mb-2 inline-flex items-center gap-1">
                &larr; Kembali ke Riwayat Pesanan
            </a>
            <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255]">
                Faktur Pesanan #{{ $order->code }}
            </h1>
            <p class="text-xs text-gray-500 mt-1 font-mono">
                Dipesan pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-3">
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
            <span class="px-3.5 py-1.5 rounded-full font-semibold text-xs border {{ $badgeClasses }}">
                {{ $order->statusLabel() }}
            </span>

            <button type="button" onclick="window.print()" class="text-xs border border-[#dcd8cd] text-gray-700 hover:border-[#0f2255] hover:text-[#0f2255] bg-white px-3.5 py-1.5 rounded-lg transition-colors font-medium shadow-sm">
                Cetak Lembar Faktur
            </button>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Status Notification Strip --}}
        @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="text-xs text-amber-900">
                    <p class="font-bold text-sm mb-1">Pesanan Menunggu Pembayaran</p>
                    <p>Silakan lakukan transfer sesuai total tagihan lalu unggah bukti pembayaran agar pesanan diproses.</p>
                </div>
                <a href="{{ route('checkout.payment', $order) }}" class="btn-orange text-xs py-2.5 px-5 rounded-lg shadow-sm shrink-0">
                    Unggah Bukti Sekarang &rarr;
                </a>
            </div>
        @elseif($order->status === \App\Models\Order::MENUNGGU_VERIFIKASI)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-xs text-blue-900">
                <p class="font-bold text-sm mb-1">Bukti Pembayaran Sedang Diverifikasi</p>
                <p>Tim admin BOOKSPACE sedang mencocokkan mutasi pembayaran Anda. Begitu disetujui, stok buku akan otomatis terpotong dan paket segera disiapkan.</p>
            </div>
        @endif

        {{-- Shipping & Recipient Details --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 md:p-8 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                Informasi Penerima &amp; Alamat Tujuan
            </h2>
            <div class="grid sm:grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold block mb-1">Nama Penerima</span>
                    <p class="font-bold text-gray-900 text-sm">{{ $order->recipient_name }}</p>
                    <p class="text-gray-600 mt-1">Kontak: {{ $order->phone }}</p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold block mb-1">Alamat Tujuan Pengiriman</span>
                    <p class="text-gray-800 leading-relaxed">{{ $order->address }}</p>
                    @if($order->note)
                        <p class="text-gray-500 italic mt-2 bg-[#fafaf8] p-2 rounded border border-[#e8e6df]">
                            &ldquo;{{ $order->note }}&rdquo;
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Book Items List --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 md:p-8 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                Daftar Judul Buku yang Dipesan
            </h2>
            <div class="divide-y divide-[#f0eee6]">
                @foreach($order->items as $item)
                    <div class="py-4 flex items-center justify-between text-xs sm:text-sm">
                        <div class="flex items-center gap-4">
                            @if($item->book)
                                <a href="{{ route('books.show', $item->book->slug) }}" class="w-12 h-16 rounded overflow-hidden shadow-sm bg-white shrink-0 border border-[#e8e6df] block">
                                    <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                </a>
                            @endif
                            <div>
                                <a href="{{ $item->book ? route('books.show', $item->book->slug) : '#' }}" class="font-serif-display font-bold text-gray-900 hover:text-[#0f2255] transition-colors text-xs sm:text-sm">
                                    {{ $item->book_title }}
                                </a>
                                <p class="text-gray-400 text-xs mt-0.5 font-mono">
                                    {{ $item->quantity }} eksemplar &times; Rp{{ number_format($item->price, 0, ',', '.') }}
                                </p>
                                @if($item->discount_percent > 0)
                                    <span class="text-[10px] bg-orange-100 text-[#e05c1a] px-2 py-0.5 rounded font-bold mt-1 inline-block">
                                        Diskon {{ $item->discount_percent }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="font-mono font-bold text-sm text-gray-900">
                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Summary Breakdown --}}
            <div class="pt-5 border-t border-[#f0eee6] space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal Buku</span>
                    <span class="font-mono font-semibold text-gray-900">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Ongkos Kirim Buku</span>
                    <span class="text-emerald-700 font-semibold">Gratis</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Metode Pembayaran</span>
                    <span class="font-semibold text-gray-900">{{ $order->latestPayment?->methodLabel() ?? 'Manual' }}</span>
                </div>
                <div class="pt-3 border-t border-[#f0eee6] flex justify-between items-baseline">
                    <span class="font-serif-display font-bold text-sm text-gray-900">Total Akhir</span>
                    <span class="font-serif-display font-bold text-2xl text-[#e05c1a]">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Verification & Payment Proof --}}
        @if($order->latestPayment)
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                    Catatan Pembayaran &amp; Verifikasi
                </h2>
                <div class="grid sm:grid-cols-2 gap-6 text-xs">
                    <div>
                        <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold block mb-1">Status Verifikasi</span>
                        <p class="font-bold text-gray-900 text-sm">{{ $order->latestPayment->statusLabel() }}</p>
                        @if($order->latestPayment->verified_at)
                            <p class="text-gray-400 text-[11px] mt-1 font-mono">Diverifikasi oleh admin pada: {{ $order->latestPayment->verified_at->translatedFormat('d M Y, H:i') }} WIB</p>
                        @endif
                    </div>
                    @if($order->latestPayment->proof_image)
                        <div>
                            <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold block mb-1">Bukti Transfer yang Diunggah</span>
                            <a href="{{ route('payment.proof', $order->latestPayment) }}" target="_blank" class="inline-block border border-[#dcd8cd] rounded-xl p-1 hover:border-[#0f2255] transition-colors shadow-sm">
                                <img src="{{ route('payment.proof', $order->latestPayment) }}" alt="Bukti Transfer" class="w-24 h-24 object-cover rounded-lg">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Cancel action if eligible --}}
        @if($order->canCancel())
            <div class="text-right">
                <form method="POST" action="{{ route('checkout.cancel', $order) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan #{{ $order->code }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold underline">
                        Batalkan Pesanan Ini
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

