@extends('layouts.bookspace')

@section('title', 'Invoice Pesanan #' . $order->code)

@section('content')
@php
    $isCancelled = $order->status === \App\Models\Order::DIBATALKAN;
    $isPaid = in_array($order->status, \App\Models\Order::STOCK_REDUCED_STATUSES);
    $freeShippingThreshold = (int) \App\Models\Setting::value('free_shipping_threshold', 200000) ?: 200000;
    $shippingFree = (int) $order->total >= $freeShippingThreshold;

    $badgeClasses = match ($order->status) {
        'pending' => 'bg-gray-100 text-gray-800',
        'menunggu_pembayaran' => 'bg-amber-50 text-amber-900',
        'menunggu_verifikasi' => 'bg-blue-50 text-blue-950',
        'dibayar' => 'bg-indigo-50 text-indigo-900',
        'diproses' => 'bg-purple-50 text-purple-900',
        'dikirim' => 'bg-cyan-50 text-cyan-950',
        'selesai' => 'bg-emerald-50 text-emerald-900',
        'dibatalkan' => 'bg-red-50 text-red-900',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<div class="shell py-6 sm:py-10 lg:py-12">

    {{-- Alur checkout --}}
    <x-checkout-steps :step="4" class="no-print" />

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 lg:gap-6">

        {{-- ── KOLOM KIRI: status & tindak lanjut ────────────────── --}}
        <div class="no-print lg:col-span-5 xl:col-span-4 space-y-4">
            <div class="card card-pad">
                <div class="w-11 h-11 rounded-full flex items-center justify-center mb-4 {{ $isCancelled ? 'bg-red-50 text-red-600' : ($isPaid ? 'bg-emerald-50 text-emerald-600' : 'bg-[#eef2ff] text-[#0f2255]') }}">
                    @if($isCancelled)
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <h1 class="page-title mb-1.5">{{ $isCancelled ? 'Pesanan Dibatalkan' : 'Pesanan Berhasil Dicatat' }}</h1>
                <p class="muted">
                    Nomor invoice <span class="font-mono font-semibold text-[#17181c]">{{ $order->code }}</span>
                    &middot; dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
                </p>

                <div class="mt-4">
                    <span class="pill {{ $badgeClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                        {{ $order->statusLabel() }}
                    </span>
                </div>

                <div class="mt-6 pt-5 border-t border-[#eeebe3]">
                    <x-order-stepper :order="$order" />
                </div>

                @if($order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN)
                    <a href="{{ route('checkout.payment', $order) }}" class="btn-orange w-full mt-5">
                        Unggah Bukti Transfer
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @endif

                <div class="mt-4 grid grid-cols-2 gap-2.5">
                    <a href="{{ route('user.orders.show', $order) }}" class="btn-ghost btn-sm">Lacak Pesanan</a>
                    <a href="{{ route('books.index') }}" class="btn-primary btn-sm">Lanjut Belanja</a>
                </div>
            </div>

            {{-- Langkah berikutnya --}}
            <div class="card card-pad">
                <h2 class="text-[13px] font-bold uppercase tracking-wide text-[#3f4451] mb-3">Yang Terjadi Selanjutnya</h2>
                <ol class="space-y-3">
                    @php
                        $nextSteps = match (true) {
                            $isCancelled => [
                                ['Stok buku sudah dikembalikan ke gudang.', 'bg-red-500'],
                                ['Ingin pesan lagi? Buat pesanan baru dari halaman katalog.', 'bg-red-500'],
                            ],
                            $order->status === \App\Models\Order::MENUNGGU_PEMBAYARAN => [
                                ['Transfer ke rekening resmi yang tercantum, lalu unggah bukti transfernya.', 'bg-[#0f2255]'],
                                ['Admin memeriksa pembayaran paling lambat 1&times;24 jam pada hari kerja.', 'bg-[#0f2255]'],
                                ['Setelah disetujui, buku disiapkan dan dikirim dengan nomor resi.', 'bg-[#0f2255]'],
                            ],
                            in_array($order->status, [\App\Models\Order::MENUNGGU_VERIFIKASI, \App\Models\Order::PENDING]) => [
                                ['Bukti transfer kamu sudah diterima dan sedang diperiksa admin.', 'bg-[#0f2255]'],
                                ['Status berubah jadi <span class="font-semibold">Dibayar</span> begitu pembayaran disetujui.', 'bg-[#0f2255]'],
                                ['Paket dikirim 1&ndash;2 hari kerja setelah status Dibayar.', 'bg-[#0f2255]'],
                            ],
                            $order->status === \App\Models\Order::DIPROSES => [
                                ['Buku sedang dikemas di gudang BOOKSPACE.', 'bg-[#0f2255]'],
                                ['Nomor resi muncul di halaman pesanan begitu paket diserahkan ke kurir.', 'bg-[#0f2255]'],
                            ],
                            $order->status === \App\Models\Order::DIKIRIM => [
                                ['Paket dalam pengiriman dengan resi <span class="font-mono font-semibold">'.e($order->tracking_number ?: '-').'</span>.', 'bg-[#0f2255]'],
                                ['Tekan <span class="font-semibold">Pesanan Sudah Diterima</span> di halaman pesanan saat paket sampai.', 'bg-[#0f2255]'],
                            ],
                            default => [
                                ['Pesanan selesai. Terima kasih sudah belanja di BOOKSPACE.', 'bg-emerald-500'],
                                ['Tulis ulasan untuk buku yang sudah kamu baca.', 'bg-[#0f2255]'],
                            ],
                        };
                    @endphp
                    @foreach($nextSteps as [$text, $dotClass])
                        <li class="flex gap-3">
                            <span class="mt-[7px] w-1.5 h-1.5 rounded-full {{ $dotClass }} shrink-0"></span>
                            <p class="text-[13px] leading-relaxed text-[#3f4451]">{!! $text !!}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Ringkasan pengiriman --}}
            <div class="card card-pad">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-[13px] font-bold uppercase tracking-wide text-[#3f4451]">Pengiriman</h2>
                    <a href="{{ route('user.profile.edit') }}#buku-alamat" class="text-[12px] font-semibold text-[#0f2255] hover:underline">Ubah alamat</a>
                </div>
                <dl class="space-y-2.5 text-[13px]">
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#8b8578]">Penerima</dt>
                        <dd class="text-right font-semibold text-[#17181c]">{{ $order->recipient_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#8b8578]">Kontak</dt>
                        <dd class="text-right font-medium text-[#17181c]">{{ $order->phone }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-[#8b8578]">Metode bayar</dt>
                        <dd class="text-right font-semibold text-[#17181c]">{{ strtoupper($order->latestPayment?->method ?? 'manual') }}</dd>
                    </div>
                </dl>
                <p class="mt-3 pt-3 border-t border-[#eeebe3] text-[13px] leading-relaxed text-[#3f4451]">{{ $order->address }}</p>
                @if($order->note)
                    <p class="mt-2 rounded-lg bg-[#f6f5f1] px-3 py-2 text-[12px] italic text-[#6b6f78]">Catatan: {{ $order->note }}</p>
                @endif
            </div>
        </div>

        {{-- ── KOLOM KANAN: dokumen invoice ──────────────────────── --}}
        <div class="lg:col-span-7 xl:col-span-8">
            <div id="invoice-doc" class="card">
                <div class="flex flex-wrap items-start justify-between gap-4 p-5 sm:p-7 border-b border-[#eeebe3]">
                    <div>
                        <span class="font-serif-display font-bold text-xl text-[#0f2255] block leading-none">BOOKSPACE</span>
                        <span class="muted mt-1 block">Toko Buku Online &amp; Sastra Pilihan</span>
                    </div>
                    <div class="text-right">
                        <p class="eyebrow mb-1">Invoice</p>
                        <p class="font-mono text-sm font-bold text-[#17181c]">#{{ $order->code }}</p>
                        <p class="text-[12px] text-[#8b8578] mt-0.5">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 sm:p-7 border-b border-[#eeebe3] bg-[#fbfaf7]">
                    <div>
                        <p class="eyebrow mb-1">Penerima</p>
                        <p class="text-sm font-semibold text-[#17181c]">{{ $order->recipient_name }}</p>
                        <p class="text-[12px] text-[#6b6f78] mt-0.5">{{ $order->phone }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="eyebrow mb-1">Alamat Pengiriman</p>
                        <p class="text-[13px] leading-relaxed text-[#3f4451]">{{ $order->address }}</p>
                    </div>
                </div>

                {{-- Barang --}}
                <div class="p-5 sm:p-7">
                    <h3 class="text-[13px] font-bold uppercase tracking-wide text-[#3f4451] mb-4">Rincian Buku</h3>

                    {{-- Desktop: tabel --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-[13px]">
                            <thead>
                                <tr class="text-left text-[11px] uppercase tracking-wide text-[#8b8578] border-b border-[#eeebe3]">
                                    <th class="pb-2.5 font-semibold">Judul</th>
                                    <th class="pb-2.5 font-semibold text-center w-20">Qty</th>
                                    <th class="pb-2.5 font-semibold text-right w-28">Harga</th>
                                    <th class="pb-2.5 font-semibold text-right w-32">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f1efe8]">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="py-3.5 pr-4">
                                            <div class="flex items-center gap-3">
                                                @if($item->book)
                                                    <div class="w-9 h-12 rounded border border-[#e6e3db] overflow-hidden bg-white shrink-0">
                                                        <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="font-serif-display font-bold text-[#17181c] leading-snug">{{ $item->book_title }}</p>
                                                    @if($item->discount_percent > 0)
                                                        <p class="text-[11px] text-[#e05c1a] mt-0.5">Diskon {{ $item->discount_percent }}%</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 text-center text-[#3f4451]">{{ $item->quantity }}</td>
                                        <td class="py-3.5 text-right font-mono text-[#3f4451]">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="py-3.5 text-right font-mono font-semibold text-[#17181c]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile: kartu --}}
                    <ul class="sm:hidden divide-y divide-[#f1efe8]">
                        @foreach($order->items as $item)
                            <li class="py-3.5 flex items-start gap-3">
                                @if($item->book)
                                    <div class="w-11 h-14 rounded border border-[#e6e3db] overflow-hidden bg-white shrink-0">
                                        <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="font-serif-display font-bold text-[13px] text-[#17181c] leading-snug">{{ $item->book_title }}</p>
                                    <p class="text-[11px] text-[#8b8578] mt-1">{{ $item->quantity }} &times; Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <p class="font-mono text-[13px] font-semibold text-[#17181c] shrink-0">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Total --}}
                    <div class="mt-5 pt-5 border-t border-[#eeebe3] w-full sm:ml-auto sm:w-[320px] space-y-2 text-[13px]">
                        <div class="flex justify-between text-[#6b6f78]">
                            <span>Subtotal buku ({{ $order->items->sum('quantity') }} eksemplar)</span>
                            <span class="font-mono text-[#17181c]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[#6b6f78] gap-4">
                            <span>Ongkos kirim</span>
                            <span @class(['font-semibold text-emerald-700' => $shippingFree, 'font-mono text-[#17181c] text-right' => ! $shippingFree])>
                                {{ $shippingFree ? 'Gratis' : 'Di bawah ambang Rp'.number_format($freeShippingThreshold, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-baseline pt-2.5 mt-1 border-t border-[#eeebe3]">
                            <span class="font-bold text-[#17181c]">Total pembayaran</span>
                            <span class="font-serif-display font-bold text-xl text-[#e05c1a]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 p-5 sm:p-6 border-t border-[#eeebe3] bg-[#fbfaf7] rounded-b-xl">
                    <p class="text-[12px] text-[#8b8578] max-w-md">
                        Invoice ini tersimpan di akun kamu. Simpan baik-baik atau cetak untuk arsip.
                    </p>
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="window.print()" class="btn-ghost btn-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-3h12v7H6v-7z"/></svg>
                            Cetak Invoice
                        </button>
                        <a href="{{ route('user.orders') }}" class="btn-outline btn-sm">Riwayat Pesanan</a>
                    </div>
                </div>
            </div>

            <p class="no-print mt-4 text-[12px] text-[#8b8578]">
                Ada pertanyaan soal pesanan ini?
                <a href="{{ route('help.index') }}" class="font-semibold text-[#0f2255] hover:underline">Buka live chat bantuan</a>.
            </p>
        </div>
    </div>
</div>
@endsection
