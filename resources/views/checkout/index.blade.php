@extends('layouts.bookspace')

@section('title', 'Alamat Pengiriman & Pembayaran')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12">

    {{-- ── Checkout Steps Indicator ───────────────────────────── --}}
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between text-xs font-semibold">
            <div class="flex items-center gap-2 text-emerald-700">
                <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">✓</span>
                <span>Keranjang</span>
            </div>
            <div class="flex-1 h-0.5 bg-emerald-500 mx-4"></div>
            <div class="flex items-center gap-2 text-[#0f2255]">
                <span class="w-7 h-7 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold">2</span>
                <span>Alamat &amp; Pembayaran</span>
            </div>
            <div class="flex-1 h-0.5 bg-[#e8e6df] mx-4"></div>
            <div class="flex items-center gap-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">3</span>
                <span>Konfirmasi &amp; Bukti</span>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255]">
            Checkout &amp; Pengiriman Buku
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Lengkapi data penerima dan pilih metode pembayaran resmi untuk meneruskan pesanan Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('checkout.store') }}" x-data="{ method: 'transfer' }">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ── LEFT: FORM INPUTS (8 cols) ─────────────────── --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- 1. Informasi Penerima & Alamat --}}
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 md:p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[#f0eee6]">
                        <span class="w-7 h-7 rounded-lg bg-[#f4f2ec] text-[#0f2255] font-serif-display font-bold text-sm flex items-center justify-center">1</span>
                        <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Tujuan Pengiriman Paket</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Penerima <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="recipient_name"
                                value="{{ old('recipient_name', $user->name) }}"
                                required
                                class="w-full text-sm border border-[#dcd8cd] rounded-xl px-4 py-3 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                            >
                            @error('recipient_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nomor WhatsApp / Telepon <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="Contoh: 081234567890"
                                required
                                class="w-full text-sm border border-[#dcd8cd] rounded-xl px-4 py-3 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                            >
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Alamat Lengkap Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="address"
                            rows="3"
                            placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos"
                            required
                            class="w-full text-sm border border-[#dcd8cd] rounded-xl px-4 py-3 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        >{{ old('address') }}</textarea>
                        @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Catatan Pengemasan / Pengiriman (Opsional)
                        </label>
                        <input
                            type="text"
                            name="note"
                            value="{{ old('note') }}"
                            placeholder="Contoh: Tolong bungkus ekstra rapi / Titip di pos satpam jika tidak ada orang"
                            class="w-full text-sm border border-[#dcd8cd] rounded-xl px-4 py-3 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        >
                    </div>
                </div>

                {{-- 2. Pilihan Pembayaran --}}
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 md:p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[#f0eee6]">
                        <span class="w-7 h-7 rounded-lg bg-[#f4f2ec] text-[#0f2255] font-serif-display font-bold text-sm flex items-center justify-center">2</span>
                        <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Metode Pembayaran Resmi</h2>
                    </div>

                    <div class="space-y-3.5">
                        {{-- Transfer Bank Manual --}}
                        <label class="block border rounded-xl p-4 sm:p-5 cursor-pointer transition-all" :class="method === 'transfer' ? 'border-[#0f2255] bg-[#f8f9fc] ring-1 ring-[#0f2255]' : 'border-[#e8e6df] hover:border-gray-300'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3.5">
                                    <input type="radio" name="payment_method" value="transfer" x-model="method" class="text-[#0f2255] focus:ring-0">
                                    <div>
                                        <p class="font-serif-display font-bold text-gray-900 text-sm sm:text-base">Transfer Bank Manual</p>
                                        <p class="text-xs text-gray-500">BCA, Mandiri, BNI, BRI (Konfirmasi via unggah bukti transfer)</p>
                                    </div>
                                </div>
                                <span class="text-[11px] bg-white border border-[#dcd8cd] font-semibold text-[#0f2255] px-2.5 py-1 rounded">
                                    Verifikasi Admin
                                </span>
                            </div>
                            <div x-show="method === 'transfer'" x-transition class="mt-4 pt-3 border-t border-[#e8e6df] text-xs text-gray-600 space-y-1">
                                <p>Rekening Tujuan Resmi: <strong class="text-gray-900">{{ $settings['bank_name'] ?? 'BCA' }} — {{ $settings['bank_account_number'] ?? '1234567890' }}</strong> a.n. <strong class="text-gray-900">{{ $settings['bank_account_name'] ?? 'BOOKSPACE' }}</strong></p>
                                <p class="text-[#e05c1a]">Setelah menyelesaikan checkout, Anda akan langsung diarahkan ke halaman invoice untuk mengunggah bukti pembayaran.</p>
                            </div>
                        </label>

                        {{-- QRIS --}}
                        <label class="block border rounded-xl p-4 sm:p-5 cursor-pointer transition-all" :class="method === 'qris' ? 'border-[#0f2255] bg-[#f8f9fc] ring-1 ring-[#0f2255]' : 'border-[#e8e6df] hover:border-gray-300'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3.5">
                                    <input type="radio" name="payment_method" value="qris" x-model="method" class="text-[#0f2255] focus:ring-0">
                                    <div>
                                        <p class="font-serif-display font-bold text-gray-900 text-sm sm:text-base">QRIS (Semua E-Wallet &amp; Mobile Banking)</p>
                                        <p class="text-xs text-gray-500">GoPay, OVO, Dana, ShopeePay, BCA Mobile, Livin', BRImo</p>
                                    </div>
                                </div>
                                <span class="text-[11px] bg-blue-50 border border-blue-200 font-semibold text-blue-700 px-2.5 py-1 rounded">
                                    Scan QR Cepat
                                </span>
                            </div>
                            <div x-show="method === 'qris'" x-transition class="mt-4 pt-3 border-t border-[#e8e6df] text-xs text-gray-600">
                                <p class="text-[#e05c1a]">Kode QRIS resmi toko akan tampil di langkah selanjutnya untuk Anda pindai dan unggah tangkapan layarnya.</p>
                            </div>
                        </label>

                        {{-- COD --}}
                        <label class="block border rounded-xl p-4 sm:p-5 cursor-pointer transition-all" :class="method === 'cod' ? 'border-[#0f2255] bg-[#f8f9fc] ring-1 ring-[#0f2255]' : 'border-[#e8e6df] hover:border-gray-300'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3.5">
                                    <input type="radio" name="payment_method" value="cod" x-model="method" class="text-[#0f2255] focus:ring-0">
                                    <div>
                                        <p class="font-serif-display font-bold text-gray-900 text-sm sm:text-base">Bayar di Tempat (Cash on Delivery / COD)</p>
                                        <p class="text-xs text-gray-500">Bayar tunai kepada kurir saat buku tiba di alamat Anda</p>
                                    </div>
                                </div>
                                <span class="text-[11px] bg-emerald-50 border border-emerald-200 font-semibold text-emerald-700 px-2.5 py-1 rounded">
                                    Tunai Kurir
                                </span>
                            </div>
                            <div x-show="method === 'cod'" x-transition class="mt-4 pt-3 border-t border-[#e8e6df] text-xs text-gray-600">
                                <p class="text-gray-700">Pastikan nomor telepon WhatsApp aktif untuk konfirmasi kelayakan kirim dari admin BOOKSPACE.</p>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: ORDER SUMMARY (4 cols) ──────────────── --}}
            <div class="lg:col-span-4">
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sticky top-28 shadow-sm">
                    <h3 class="font-serif-display font-bold text-lg text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                        Ringkasan Pesanan
                    </h3>

                    {{-- Items mini list --}}
                    <div class="max-h-56 overflow-y-auto space-y-3 mb-4 pr-1">
                        @foreach($cart->items as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-14 bg-[#f4f2ec] rounded overflow-hidden shrink-0 border border-[#e8e6df]">
                                <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-serif-display font-bold text-gray-800 truncate">{{ $item->book->title }}</p>
                                <p class="text-[11px] text-gray-400 font-mono">{{ $item->quantity }} x Rp{{ number_format($item->book->finalPrice(), 0, ',', '.') }}</p>
                            </div>
                            <span class="text-xs font-bold text-gray-900 shrink-0 font-mono">Rp{{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Cost details --}}
                    <div class="space-y-2.5 pt-4 border-t border-[#f0eee6] text-xs">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Buku ({{ $cart->items->sum('quantity') }} eks)</span>
                            <span class="font-semibold text-gray-900 font-mono">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Ongkos Kirim Standar</span>
                            <span class="text-emerald-700 font-bold">Gratis (Subsidi Toko)</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-[#f0eee6] flex items-baseline justify-between mb-6">
                        <span class="font-serif-display font-bold text-sm text-gray-900">Total Akhir</span>
                        <span class="font-serif-display font-bold text-2xl text-[#e05c1a]">
                            Rp{{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <button type="submit" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold py-3.5 px-4 rounded-xl transition-all shadow-md text-sm tracking-wide block text-center">
                        Konfirmasi &amp; Lanjut Pembayaran
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{{ route('cart.index') }}" class="text-xs text-gray-400 hover:text-[#0f2255] transition-colors">
                            &larr; Periksa Kembali Keranjang
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

