@extends('layouts.admin')

@section('title', 'Kelola Pesanan #' . $order->code)
@section('page_title', 'Kelola Pesanan #' . $order->code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ verifyModal: false, rejectModal: false, imgModal: false }">

    {{-- Top Action Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Daftar Pesanan</a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="text-xs border border-gray-200 text-gray-700 hover:bg-gray-50 px-3.5 py-2 rounded-xl font-medium transition-colors">
                Cetak Invoice
            </button>
        </div>
    </div>

    {{-- Verification Alert Banner --}}
    @if($order->status === \App\Models\Order::MENUNGGU_VERIFIKASI)
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div>
                <p class="font-display font-bold text-base text-orange-900">Perlu Tindakan: Verifikasi Pembayaran</p>
                <p class="text-xs text-orange-700 mt-0.5">Pembeli telah mengunggah bukti pembayaran. Periksa kesesuaian nominal sebelum menyetujui.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="rejectModal = true" class="text-xs bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Tolak Pembayaran
                </button>
                <button type="button" @click="verifyModal = true" class="text-xs bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors shadow">
                    ✓ Terima & Kurangi Stok
                </button>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- ── LEFT: DETAILS & ITEMS ────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Order Items List --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Daftar Buku Dipesan</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="py-3.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                @if($item->book)
                                    <img src="{{ $item->book->coverUrl() }}" alt="{{ $item->book_title }}" class="w-10 h-14 object-cover rounded border border-gray-100">
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900">{{ $item->book_title }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Harga: Rp{{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }} eksemplar</p>
                                    @if($item->book)
                                        <p class="text-[10px] {{ $item->book->stock < $item->quantity ? 'text-red-500 font-bold' : 'text-gray-400' }}">Stok gudang saat ini: {{ $item->book->stock }} eks</p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 text-sm">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-gray-100 space-y-1.5 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotal Produk</span>
                        <span class="font-medium text-gray-900">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-medium text-green-600">Gratis (USK)</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-baseline">
                        <span class="font-bold text-gray-900 text-sm">Total Tagihan</span>
                        <span class="font-display font-bold text-xl text-orange-600">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Buyer & Shipping Info --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Informasi Pengiriman</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-semibold block mb-1">Nama Penerima</span>
                        <p class="font-bold text-gray-900 text-sm">{{ $order->recipient_name }}</p>
                        <p class="text-gray-500 mt-0.5">Telepon: {{ $order->phone }}</p>
                        <p class="text-gray-400 mt-1">Akun Terdaftar: {{ $order->user->name }} ({{ $order->user->email }})</p>
                    </div>
                    <div>
                        <span class="text-gray-400 uppercase text-[10px] font-semibold block mb-1">Alamat Lengkap</span>
                        <p class="text-gray-800 leading-relaxed">{{ $order->address }}</p>
                        @if($order->note)
                            <p class="text-gray-400 italic mt-2">Catatan: {{ $order->note }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. Stock Movements related to this order --}}
            @if($order->movements->isNotEmpty())
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h2 class="font-display font-bold text-base text-gray-900 mb-3 pb-2 border-b border-gray-100">Catatan Mutasi Stok Pesanan Ini</h2>
                <div class="space-y-2 text-xs">
                    @foreach($order->movements as $m)
                        <div class="p-2.5 bg-gray-50 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-bold text-gray-800">{{ $m->book->title ?? '-' }}</span>
                                <span class="text-gray-400 ml-2">({{ $m->reason }})</span>
                            </div>
                            <span class="font-bold {{ $m->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $m->quantity }} eks (Sisa: {{ $m->stock_after }})</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── RIGHT: STATUS & PAYMENT ──────────────────────────── --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Status Management Card --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display font-bold text-base text-gray-900 mb-3 pb-2 border-b border-gray-100">Status Pesanan</h3>

                <div class="mb-4">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Status Saat Ini</span>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                        {{ $order->statusLabel() }}
                    </span>
                    @if($order->paid_at)
                        <p class="text-[10px] text-gray-400 mt-1">Dikonfirmasi bayar: {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Ubah Status Pengiriman</label>
                        <select name="status" class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-300">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="menunggu_pembayaran" {{ $order->status === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="menunggu_verifikasi" {{ $order->status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="dibayar" {{ $order->status === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                            <option value="diproses" {{ $order->status === 'diproses' ? 'selected' : '' }}>Diproses (Packing)</option>
                            <option value="dikirim" {{ $order->status === 'dikirim' ? 'selected' : '' }}>Dikirim (Ekspedisi)</option>
                            <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $order->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs py-2 rounded-xl transition-colors shadow">
                        Perbarui Status
                    </button>
                </form>
            </div>

            {{-- Payment Proof Card --}}
            @php $payment = $order->latestPayment; @endphp
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-display font-bold text-base text-gray-900 mb-3 pb-2 border-b border-gray-100">Informasi Pembayaran</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">Metode</span>
                        <span class="font-semibold text-gray-900">{{ $payment?->methodLabel() ?? 'Manual' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">Status Pembayaran</span>
                        <span class="font-semibold text-gray-900">{{ $payment?->statusLabel() ?? '-' }}</span>
                    </div>

                    @if($payment?->proof_image)
                        <div>
                            <span class="text-gray-400 text-[10px] uppercase font-semibold block mb-2">Bukti Transfer</span>
                            <div class="border border-gray-200 rounded-xl overflow-hidden cursor-pointer" @click="imgModal = true">
                                <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti Transfer" class="w-full h-44 object-cover hover:scale-105 transition-transform">
                            </div>
                            <span class="text-[10px] text-gray-400 block text-center mt-1">Klik gambar untuk memperbesar</span>
                        </div>
                    @else
                        <p class="text-gray-400 italic text-[11px]">Belum ada bukti pembayaran diunggah.</p>
                    @endif

                    @if($payment?->verifier)
                        <div class="pt-2 border-t border-gray-100 text-[11px] text-gray-400">
                            Diverifikasi oleh: <strong class="text-gray-600">{{ $payment->verifier->name }}</strong>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- ── VERIFY MODAL ────────────────────────────────────────── --}}
    <div x-show="verifyModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="verifyModal = false">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Konfirmasi Pembayaran?</h3>
            <p class="text-xs text-gray-500 mb-4">
                Tindakan ini akan mengubah status menjadi <strong>"Dibayar"</strong> dan <strong class="text-orange-600">mengurangi stok buku secara otomatis</strong> menggunakan database transaction & row lock.
            </p>
            <form method="POST" action="{{ route('admin.orders.payment.verify', $order) }}" class="flex justify-center gap-3">
                @csrf
                @method('PATCH')
                <button type="button" @click="verifyModal = false" class="text-xs border border-gray-200 text-gray-700 px-4 py-2 rounded-xl">Batal</button>
                <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-xl">Ya, Konfirmasi</button>
            </form>
        </div>
    </div>

    {{-- ── REJECT MODAL ────────────────────────────────────────── --}}
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="rejectModal = false">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Tolak Pembayaran?</h3>
            <p class="text-xs text-gray-500 mb-3">Pesanan akan dibatalkan dan pembeli dapat melihat alasan penolakan.</p>
            <form method="POST" action="{{ route('admin.orders.payment.reject', $order) }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <input type="text" name="reason" placeholder="Alasan penolakan (misal: nominal tidak sesuai)" class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2 text-left">
                <div class="flex justify-center gap-2">
                    <button type="button" @click="rejectModal = false" class="text-xs border border-gray-200 text-gray-700 px-4 py-2 rounded-xl">Batal</button>
                    <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-xl">Tolak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── ZOOM PROOF IMAGE & ONE-CLICK VERIFICATION MODAL ────── --}}
    @if($payment?->proof_image)
    <div x-show="imgModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-transition.opacity @click="imgModal = false">
        <div class="relative max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]" @click.stop>
            <div class="p-4 border-b border-[#e8e6df] flex items-center justify-between bg-[#fafaf8]">
                <div>
                    <h3 class="font-serif-display font-bold text-base text-[#0f2255]">Pemeriksaan Bukti Transfer #{{ $order->code }}</h3>
                    <p class="text-xs text-gray-500 font-serif">Tagihan: <strong class="text-[#e05c1a]">Rp{{ number_format($order->total, 0, ',', '.') }}</strong> &bull; Pembeli: {{ $order->recipient_name }}</p>
                </div>
                <button @click="imgModal = false" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg text-sm">✕</button>
            </div>

            <div class="p-4 bg-gray-950 flex-1 overflow-auto flex items-center justify-center min-h-[280px]">
                <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti Transfer Penuh" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-lg">
            </div>

            <div class="p-4 bg-white border-t border-[#e8e6df] flex flex-wrap items-center justify-between gap-3">
                <span class="text-xs text-gray-500">Metode: <strong>{{ $payment->methodLabel() }}</strong></span>
                <div class="flex items-center gap-2">
                    <button type="button" @click="imgModal = false; rejectModal = true" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2 rounded-xl font-semibold transition-colors">
                        Tolak Bukti
                    </button>
                    <button type="button" @click="imgModal = false; verifyModal = true" class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-semibold transition-colors shadow-sm">
                        ✓ Setujui &amp; Kurangi Stok Gudang
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
