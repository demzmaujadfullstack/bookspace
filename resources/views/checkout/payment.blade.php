@extends('layouts.bookspace')

@section('title', 'Pembayaran Invoice #' . $order->code)

@section('content')
@php
    $isQris = $payment && $payment->method === 'qris';
    $qrisFile = isset($settings['qris_image']) && $settings['qris_image'] && file_exists(public_path('storage/'.$settings['qris_image']));
@endphp

<div class="shell py-6 md:py-8">

    <x-checkout-steps :step="3" />

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="eyebrow">Instruksi pembayaran</span>
            <h1 class="page-title mt-1">Pesanan #{{ $order->code }}</h1>
            <p class="muted mt-1.5">Bayar sesuai nominal berikut, lalu unggah buktinya agar admin dapat memverifikasi pesanan Anda.</p>
        </div>
        <div class="card !p-4 text-right">
            <p class="eyebrow">Total tagihan</p>
            <p class="font-serif-display text-[26px] font-bold leading-tight text-[#e05c1a]">Rp{{ number_format($order->total, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

        {{-- Instruksi --}}
        <div class="space-y-6 lg:col-span-7">
            <section class="card overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">
                        {{ $isQris ? 'Bayar dengan QRIS' : 'Transfer ke Rekening Toko' }}
                    </h2>
                </header>

                @if($isQris)
                    <div class="p-5 text-center">
                        <div class="inline-block rounded-xl border-2 border-dashed border-[#dcd8cd] bg-white p-4">
                            @if($qrisFile)
                                <img src="{{ asset('storage/'.$settings['qris_image']) }}" alt="Kode QRIS BOOKSPACE" class="mx-auto h-60 w-60 object-contain">
                            @else
                                <div class="flex h-60 w-60 flex-col items-center justify-center rounded-lg border border-[#e6e3db] bg-[#fafaf8] p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-[#0f2255]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    <span class="mt-3 text-[12px] font-bold uppercase tracking-wider text-[#0f2255]">Kode QRIS toko</span>
                                    <span class="mt-1 text-[11px] text-[#9a9484]">Belum dipasang di pengaturan admin</span>
                                </div>
                            @endif
                        </div>
                        <p class="muted mx-auto mt-4 max-w-sm">Buka aplikasi mobile banking atau dompet digital Anda, pilih menu <strong class="text-[#3f4451]">Scan QR</strong>, lalu bayar sampai nominal di atas.</p>
                    </div>
                @else
                    <div class="space-y-4 p-5">
                        <div class="rounded-xl border border-[#e6e3db] bg-[#fafaf8] p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="eyebrow">Bank tujuan</p>
                                    <p class="font-serif-display text-lg font-bold text-[#17181c]">{{ $settings['bank_name'] ?? 'BCA' }}</p>
                                </div>
                                <span class="pill bg-white text-[#0f2255]">a.n. {{ $settings['bank_account_name'] ?? 'BOOKSPACE' }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#e6e3db] pt-3">
                                <div>
                                    <p class="eyebrow">Nomor rekening</p>
                                    <p class="font-mono text-[19px] font-bold text-[#0f2255]" id="acc-num">{{ $settings['bank_account_number'] ?? '1234567890' }}</p>
                                </div>
                                <button type="button" class="btn-ghost btn-sm"
                                        onclick="navigator.clipboard.writeText(document.getElementById('acc-num').innerText).then(() => alert('Nomor rekening disalin.'))">
                                    Salin nomor
                                </button>
                            </div>
                        </div>

                        <ol class="space-y-2.5 text-[13px] text-[#3f4451]">
                            <li class="flex gap-3"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[11px] font-bold text-white">1</span><span>Transfer <strong class="text-[#17181c]">tepat Rp{{ number_format($order->total, 0, ',', '.') }}</strong> lewat ATM, m-banking, atau internet banking.</span></li>
                            <li class="flex gap-3"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[11px] font-bold text-white">2</span><span>Ambil tangkapan layar bukti transfer yang menampilkan nomor referensi transaksi.</span></li>
                            <li class="flex gap-3"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[11px] font-bold text-white">3</span><span>Unggah bukti tersebut pada formulir di samping. Admin meninjau maksimal 1&times;24 jam.</span></li>
                        </ol>
                    </div>
                @endif
            </section>

            <section class="card overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Rincian Pesanan</h2>
                </header>
                <ul class="divide-y divide-[#f0eee6] px-5">
                    @foreach($order->items as $item)
                        <li class="flex items-center justify-between gap-4 py-3 text-[13px]">
                            <span class="min-w-0 truncate text-[#17181c]">{{ $item->book_title }} <span class="text-[#6b6f78]">&times; {{ $item->quantity }}</span></span>
                            <span class="shrink-0 font-semibold text-[#0f2255]">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="flex items-center justify-between border-t border-[#e6e3db] bg-[#fafaf8] px-5 py-3.5 text-[13px]">
                    <span class="font-semibold text-[#17181c]">Total ({{ $order->items->sum('quantity') }} eksemplar)</span>
                    <span class="font-serif-display text-[17px] font-bold text-[#0f2255]">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </section>
        </div>

        {{-- Unggah bukti --}}
        <div class="lg:col-span-5">
            <div class="card sticky top-[108px] overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Unggah Bukti Pembayaran</h2>
                </header>

                <form method="POST" action="{{ route('checkout.payment.upload', $order) }}" enctype="multipart/form-data" class="space-y-5 p-5">
                    @csrf

                    @if($payment && $payment->proof_image)
                        <div class="rounded-lg border border-[#cfe0f5] bg-[#f2f7fd] px-4 py-3 text-[12px] text-[#1d4ed8]">
                            <p class="font-bold">Bukti sudah tersimpan</p>
                            <p class="mt-0.5">Status verifikasi: <strong>{{ $payment->statusLabel() }}</strong>. Unggah ulang bila perlu memperbaiki bukti.</p>
                        </div>
                    @endif

                    <div>
                        <label for="proof_image" class="field-label">Foto atau screenshot bukti <span class="text-[#b42318]">*</span></label>
                        <input type="file" id="proof_image" name="proof_image" accept="image/*" required
                               class="w-full rounded-lg border border-[#dcd8cd] bg-[#fafaf8] p-2 text-[13px] text-[#6b6f78] file:mr-3 file:rounded-md file:border-0 file:bg-[#0f2255] file:px-4 file:py-2 file:text-[12px] file:font-semibold file:text-white hover:file:bg-[#1a3380]">
                        @error('proof_image') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="mt-1.5 text-[12px] text-[#9a9484]">Format JPG, PNG, atau WEBP. Maksimal 3 MB.</p>
                    </div>

                    <div>
                        <label for="payment-note" class="field-label">Catatan pengirim (opsional)</label>
                        <input type="text" id="payment-note" name="note" value="{{ old('note') }}" maxlength="255"
                               placeholder="Contoh: transfer dari rekening BCA pribadi" class="field">
                        @error('note') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-orange w-full !py-3">Kirim Bukti Pembayaran</button>
                </form>

                @if($order->canCancel())
                    <form method="POST" action="{{ route('checkout.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')"
                          class="border-t border-[#e6e3db] px-5 py-4 text-center">
                        @csrf
                        <button type="submit" class="text-[12px] font-semibold text-[#b42318] transition-colors hover:text-[#8b1c1c]">Batalkan pesanan ini</button>
                    </form>
                @endif
            </div>

            <p class="muted mt-4 rounded-lg border border-[#e6e3db] bg-white px-4 py-3">
                Stok di gudang baru dipotong setelah bukti transfer Anda disetujui admin. Pesanan yang belum terverifikasi lebih dari 24 jam dibatalkan otomatis.
            </p>
        </div>
    </div>
</div>
@endsection
