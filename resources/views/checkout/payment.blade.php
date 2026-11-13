@extends('layouts.bookspace')

@section('title', 'Pembayaran Invoice #' . $order->code)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">

    {{-- ── Checkout Steps Indicator ───────────────────────────── --}}
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between text-xs font-semibold">
            <div class="flex items-center gap-2 text-emerald-700">
                <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">✓</span>
                <span>Keranjang</span>
            </div>
            <div class="flex-1 h-0.5 bg-emerald-500 mx-4"></div>
            <div class="flex items-center gap-2 text-emerald-700">
                <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">✓</span>
                <span>Pengiriman</span>
            </div>
            <div class="flex-1 h-0.5 bg-emerald-500 mx-4"></div>
            <div class="flex items-center gap-2 text-[#0f2255]">
                <span class="w-7 h-7 rounded-full bg-[#0f2255] text-white flex items-center justify-center font-bold">3</span>
                <span>Pembayaran</span>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-8 text-center max-w-xl mx-auto">
        <span class="inline-block bg-[#f4f2ec] text-[#0f2255] text-[11px] font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-2">
            Instruksi Pembayaran Buku
        </span>
        <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255]">
            Pesanan #{{ $order->code }}
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Silakan selesaikan pembayaran sesuai nominal tepat di bawah ini dan unggah buktinya agar kami dapat memproses pesanan Anda.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

        {{-- ── LEFT: INSTRUCTION CARD (7 cols) ────────────────── --}}
        <div class="md:col-span-7 space-y-6">
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-8 shadow-sm">
                {{-- Bill Header --}}
                <div class="flex items-baseline justify-between pb-4 border-b border-[#f0eee6] mb-6">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold block">Total Tagihan Buku</span>
                        <span class="text-xs text-gray-500 font-sans">Termasuk PPN &amp; Subsidi Ongkir</span>
                    </div>
                    <span class="font-serif-display font-bold text-3xl text-[#e05c1a]">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>

                @if($payment && $payment->method === 'qris')
                    {{-- QRIS Section --}}
                    <div class="text-center py-2">
                        <div class="inline-block p-4 bg-white border-2 border-dashed border-[#dcd8cd] rounded-2xl shadow-sm mb-4">
                            @if(isset($settings['qris_image']) && $settings['qris_image'] && file_exists(public_path('storage/' . $settings['qris_image'])))
                                <img src="{{ asset('storage/' . $settings['qris_image']) }}" alt="QRIS BOOKSPACE" class="w-60 h-60 object-contain mx-auto">
                            @else
                                <div class="w-56 h-56 bg-[#fafaf8] flex flex-col items-center justify-center border border-[#e8e6df] rounded-xl p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-[#0f2255] mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span class="font-serif-display font-bold text-xs text-[#0f2255]">QRIS RESMI BOOKSPACE</span>
                                    <span class="text-[10px] text-gray-400 mt-1 font-mono">NMID: ID102003004050</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-serif-display font-bold text-base text-gray-900 mb-1">Pindai Kode QRIS di Atas</h3>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto mb-2">Buka aplikasi mobile banking (BCA, Mandiri, BRI, BNI) atau dompet digital (GoPay, OVO, ShopeePay, DANA) Anda lalu lakukan pembayaran.</p>
                    </div>
                @else
                    {{-- Bank Transfer Section --}}
                    <div class="space-y-4">
                        <div class="bg-[#fafaf8] rounded-xl p-5 border border-[#e8e6df]">
                            <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold block mb-1">Rekening Transfer Resmi</span>
                            <p class="font-serif-display font-bold text-xl text-gray-900">{{ $settings['bank_name'] ?? 'BCA' }}</p>

                            <div class="mt-3 pt-3 border-t border-[#e8e6df] flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-gray-400 block">Nomor Rekening</span>
                                    <span class="font-mono font-bold text-xl text-[#0f2255]" id="acc-num">{{ $settings['bank_account_number'] ?? '1234567890' }}</span>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('acc-num').innerText); alert('Nomor rekening berhasil disalin!');" class="text-xs bg-white border border-[#dcd8cd] text-gray-700 hover:text-[#0f2255] hover:border-[#0f2255] px-3.5 py-1.5 rounded-lg font-semibold transition-colors shadow-sm">
                                    Salin Nomor
                                </button>
                            </div>

                            <div class="mt-3">
                                <span class="text-[10px] text-gray-400 block">Atas Nama Penerima</span>
                                <span class="text-xs font-semibold text-gray-800">{{ $settings['bank_account_name'] ?? 'BOOKSPACE INDONESIA' }}</span>
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 space-y-1.5 bg-[#f4f2ec] p-4 rounded-xl">
                            <p><strong>Langkah Pembayaran:</strong></p>
                            <p>1. Transfer tepat sejumlah <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong> via ATM / m-Banking.</p>
                            <p>2. Ambil tangkapan layar (screenshot) bukti transfer yang memuat nomor referensi.</p>
                            <p>3. Unggah bukti pembayaran melalui formulir di samping.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Ordered Books Preview --}}
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 shadow-sm">
                <h4 class="font-serif-display font-bold text-sm text-[#0f2255] mb-3">Item dalam Pesanan Ini</h4>
                <div class="divide-y divide-[#f0eee6]">
                    @foreach($order->items as $item)
                        <div class="py-2.5 flex justify-between items-center text-xs">
                            <span class="truncate max-w-[260px] text-gray-800 font-medium">
                                {{ $item->book_title }} &times; {{ $item->quantity }}
                            </span>
                            <span class="font-mono font-semibold text-gray-900">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── RIGHT: UPLOAD FORM CARD (5 cols) ───────────────── --}}
        <div class="md:col-span-5 space-y-6">
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-7 shadow-sm">
                <h3 class="font-serif-display font-bold text-lg text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                    Unggah Bukti Transfer
                </h3>

                @if($payment && $payment->proof_image)
                    <div class="mb-5 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900">
                        <p class="font-semibold mb-1">Bukti Sudah Tersimpan</p>
                        <p>Status: <strong class="text-blue-700">{{ $payment->statusLabel() }}</strong>. Anda tetap dapat mengunggah ulang bila terdapat revisi bukti.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.payment.upload', $order) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                            Pilih Foto / Screenshot Bukti <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="file"
                            name="proof_image"
                            accept="image/*"
                            required
                            class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0f2255] file:text-white hover:file:bg-[#1a3380] border border-[#dcd8cd] rounded-xl p-2 cursor-pointer bg-[#fafaf8]"
                        >
                        @error('proof_image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400 mt-1.5">Format: JPG, PNG, atau WEBP (Maksimal 3MB)</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Catatan Pengirim (Opsional)
                        </label>
                        <input
                            type="text"
                            name="note"
                            value="{{ old('note') }}"
                            placeholder="Contoh: Rekening atas nama Bapak Rahmat"
                            class="w-full text-xs border border-[#dcd8cd] rounded-xl px-3 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255]"
                        >
                    </div>

                    <button type="submit" class="w-full bg-[#e05c1a] hover:bg-[#c54e12] text-white font-semibold py-3.5 px-4 rounded-xl transition-all shadow-md text-sm tracking-wide block text-center">
                        Kirim Bukti Pembayaran
                    </button>
                </form>

                @if($order->canCancel())
                    <form method="POST" action="{{ route('checkout.cancel', $order) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" class="mt-5 pt-4 border-t border-[#f0eee6] text-center">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold transition-colors">
                            Batalkan Pesanan Ini
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-[#fafaf8] border border-[#e8e6df] rounded-xl p-4 text-xs text-gray-600 space-y-1">
                <p class="font-bold text-[#0f2255]">Aturan Stok USK:</p>
                <p class="text-gray-500 leading-relaxed">
                    Stok buku di gudang BOOKSPACE dilindungi dan baru akan berkurang secara resmi setelah bukti transfer Anda disetujui oleh admin.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
