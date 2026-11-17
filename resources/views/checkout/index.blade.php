@extends('layouts.bookspace')

@section('title', 'Alamat Pengiriman & Pembayaran')

@section('content')
@php
    $freeShipThreshold = (int) \App\Models\Setting::value('free_shipping_threshold', 200000) ?: 200000;
    $shippingFree = $subtotal >= $freeShipThreshold;
    $totalQty = (int) $cart->items->sum('quantity');

    $savedAddresses = $addresses->mapWithKeys(fn ($a) => [
        (string) $a->id => [
            'recipient_name' => $a->recipient_name,
            'phone' => $a->phone,
            'address' => $a->address,
            'city' => $a->city,
            'postal_code' => (string) $a->postal_code,
        ],
    ]);
@endphp

<div class="shell py-6 pb-28 md:py-8 lg:pb-14">

    <x-checkout-steps :step="2" />

    <form method="POST" action="{{ route('checkout.store') }}"
          x-data="{ method: '{{ old('payment_method', 'transfer') }}', saved: {{ \Illuminate\Support\Js::from($savedAddresses) }}, picked: '' }">
        @csrf

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

            {{-- ── Kolom isian ────────────────────────────────── --}}
            <div class="space-y-6 lg:col-span-8">

                {{-- 1. Alamat --}}
                <section class="card overflow-hidden">
                    <header class="flex items-center gap-3 border-b border-[#e6e3db] px-5 py-4">
                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-[#0f2255] text-[12px] font-bold text-white">1</span>
                        <div>
                            <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Tujuan Pengiriman</h2>
                            <p class="muted mt-0.5">Pastikan alamat lengkap agar paket tidak gagal kirim.</p>
                        </div>
                    </header>

                    <div class="space-y-5 p-5">
                        @if($addresses->isNotEmpty())
                            <div>
                                <label for="saved-address" class="field-label">Pakai alamat tersimpan</label>
                                <select id="saved-address" x-model="picked"
                                        @change="if (saved[picked]) { $refs.recipient.value = saved[picked].recipient_name; $refs.phone.value = saved[picked].phone; $refs.address.value = saved[picked].address; $refs.city.value = saved[picked].city; $refs.postal.value = saved[picked].postal_code }"
                                        class="field">
                                    <option value="">— Tulis alamat baru —</option>
                                    @foreach($addresses as $savedAddress)
                                        <option value="{{ $savedAddress->id }}">
                                            {{ ($savedAddress->label ?: 'Alamat').' · ' }}{{ $savedAddress->recipient_name }}, {{ $savedAddress->fullAddress() }}@if($savedAddress->is_default) (utama)@endif
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-[12px] text-[#9a9484]">
                                    Kelola alamat di <a href="{{ route('user.profile.edit') }}" class="font-semibold text-[#0f2255] hover:underline">Profil &rarr; Buku Alamat</a>.
                                </p>
                            </div>
                        @endif

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="recipient_name" class="field-label">Nama penerima <span class="text-[#b42318]">*</span></label>
                                <input type="text" id="recipient_name" name="recipient_name" x-ref="recipient" required
                                       value="{{ old('recipient_name', $defaultAddress?->recipient_name ?? $user->name) }}" class="field">
                                @error('recipient_name') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="field-label">Nomor WhatsApp <span class="text-[#b42318]">*</span></label>
                                <input type="tel" id="phone" name="phone" x-ref="phone" required placeholder="081234567890"
                                       value="{{ old('phone', $defaultAddress?->phone ?? $user->phone) }}" class="field">
                                @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="address" class="field-label">Alamat lengkap <span class="text-[#b42318]">*</span></label>
                            <textarea id="address" name="address" x-ref="address" rows="3" required
                                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos"
                                      class="field">{{ old('address', $defaultAddress?->address) }}</textarea>
                            @error('address') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="city" class="field-label">Kota / Kabupaten <span class="font-normal text-[#9a9484]">(wajib bila alamat disimpan)</span></label>
                                <input type="text" id="city" name="city" x-ref="city" placeholder="Kota Administrasi Jakarta Pusat"
                                       value="{{ old('city', $defaultAddress?->city) }}" class="field">
                                @error('city') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="field-label">Kode pos <span class="font-normal text-[#9a9484]">(opsional)</span></label>
                                <input type="text" id="postal_code" name="postal_code" x-ref="postal" placeholder="10310"
                                       value="{{ old('postal_code', $defaultAddress?->postal_code) }}" class="field">
                                @error('postal_code') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="rounded-lg border border-[#e6e3db] bg-[#fafaf8] p-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" name="save_address" value="1" @checked(old('save_address', $addresses->isEmpty()))
                                       class="mt-0.5 rounded border-[#dcd8cd] text-[#0f2255] focus:ring-[#0f2255]">
                                <span class="text-[13px] text-[#3f4451]">
                                    <span class="font-semibold text-[#17181c]">Simpan ke buku alamat saya</span>
                                    <span class="mt-0.5 block text-[12px] text-[#6b6f78]">Berikutnya cukup pilih dari daftar tanpa menulis ulang.</span>
                                </span>
                            </label>
                            <div class="mt-3">
                                <input type="text" name="address_label" maxlength="60" value="{{ old('address_label') }}"
                                       placeholder="Label alamat: Rumah, Kantor, Pondok (opsional)" class="field !py-2 !text-[13px]">
                                @error('address_label') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="note" class="field-label">Catatan pengemasan / pengiriman (opsional)</label>
                            <input type="text" id="note" name="note" value="{{ old('note') }}"
                                   placeholder="Contoh: bungkus ekstra rapi, titip pos satpam bila kosong" class="field">
                            @error('note') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- 2. Metode pembayaran --}}
                <section class="card overflow-hidden">
                    <header class="flex items-center gap-3 border-b border-[#e6e3db] px-5 py-4">
                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-[#0f2255] text-[12px] font-bold text-white">2</span>
                        <div>
                            <h2 class="font-serif-display text-[17px] font-bold text-[#0f2255]">Metode Pembayaran</h2>
                            <p class="muted mt-0.5">Pilih salah satu cara bayar resmi di bawah ini.</p>
                        </div>
                    </header>

                    <div class="space-y-3 p-5">
                        @php
                            $methods = [
                                'transfer' => [
                                    'Transfer Bank Manual',
                                    'BCA, Mandiri, BNI, BRI. Unggah bukti transfer setelah pesanan dibuat.',
                                    'Verifikasi admin',
                                ],
                                'qris' => [
                                    'QRIS',
                                    'Pindai dengan GoPay, OVO, DANA, ShopeePay, Livin\', BRImo, atau BCA mobile.',
                                    'Scan cepat',
                                ],
                                'cod' => [
                                    'Bayar di Tempat (COD)',
                                    'Bayar tunai ke kurir saat buku tiba di alamat Anda.',
                                    'Tunai kurir',
                                ],
                            ];
                        @endphp

                        @foreach($methods as $value => [$name, $desc, $tag])
                            <label class="block cursor-pointer rounded-xl border p-4 transition-all"
                                   :class="method === '{{ $value }}' ? 'border-[#0f2255] bg-[#f7f9fd] ring-1 ring-[#0f2255]' : 'border-[#e6e3db] hover:border-[#c9c4b6]'">
                                <span class="flex items-start gap-3">
                                    <input type="radio" name="payment_method" value="{{ $value }}" x-model="method" class="mt-1 text-[#0f2255] focus:ring-0">
                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="font-semibold text-[14px] text-[#17181c]">{{ $name }}</span>
                                            <span class="pill bg-[#f4f2ec] text-[#0f2255]">{{ $tag }}</span>
                                        </span>
                                        <span class="mt-1 block text-[12px] leading-relaxed text-[#6b6f78]">{{ $desc }}</span>
                                    </span>
                                </span>

                                @if($value === 'transfer')
                                    <div x-show="method === 'transfer'" x-transition class="mt-3 border-t border-[#e6e3db] pt-3 text-[12px] text-[#3f4451]">
                                        <p>Rekening tujuan: <strong class="text-[#17181c]">{{ $settings['bank_name'] ?? 'BCA' }} — {{ $settings['bank_account_number'] ?? '1234567890' }}</strong> a.n. <strong class="text-[#17181c]">{{ $settings['bank_account_name'] ?? 'BOOKSPACE' }}</strong></p>
                                        <p class="mt-1 text-[#c54e12]">Setelah checkout Anda diarahkan ke halaman invoice untuk mengunggah bukti transfer.</p>
                                    </div>
                                @elseif($value === 'qris')
                                    <div x-show="method === 'qris'" x-transition class="mt-3 border-t border-[#e6e3db] pt-3 text-[12px] text-[#c54e12]">
                                        Kode QRIS toko tampil di langkah berikutnya. Unggah tangkapan layar pembayarannya.
                                    </div>
                                @else
                                    <div x-show="method === 'cod'" x-transition class="mt-3 border-t border-[#e6e3db] pt-3 text-[12px] text-[#3f4451]">
                                        Pastikan nomor WhatsApp aktif; admin menghubungi Anda untuk mengonfirmasi kelayakan kirim.
                                    </div>
                                @endif
                            </label>
                        @endforeach

                        @error('payment_method') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </section>
            </div>

            {{-- ── Ringkasan pesanan ──────────────────────────── --}}
            <div class="lg:col-span-4">
                <aside class="card sticky top-[108px] overflow-hidden">
                    <h2 class="border-b border-[#e6e3db] px-5 py-4 font-serif-display text-[17px] font-bold text-[#0f2255]">Ringkasan Pesanan</h2>

                    <ul class="max-h-64 space-y-3 overflow-y-auto px-5 py-4">
                        @foreach($cart->items as $item)
                            <li class="flex items-center gap-3">
                                <img src="{{ $item->book->coverUrl() }}" alt="" class="h-14 w-10 shrink-0 rounded border border-[#e6e3db] object-cover"
                                     onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-semibold text-[#17181c]">{{ $item->book->title }}</p>
                                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">{{ $item->quantity }} &times; Rp{{ number_format($item->book->finalPrice(), 0, ',', '.') }}</p>
                                </div>
                                <span class="shrink-0 text-[13px] font-bold text-[#0f2255]">Rp{{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <dl class="space-y-2.5 border-t border-[#e6e3db] px-5 py-4 text-[13px]">
                        <div class="flex justify-between">
                            <dt class="text-[#6b6f78]">Subtotal ({{ $totalQty }} eks)</dt>
                            <dd class="font-semibold text-[#17181c]">Rp{{ number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-[#6b6f78]">Ongkos kirim</dt>
                            @if($shippingFree)
                                <dd class="font-semibold text-emerald-700">Gratis</dd>
                            @else
                                <dd class="text-[#6b6f78]">Dihitung admin (gratis di atas Rp{{ number_format($freeShipThreshold, 0, ',', '.') }})</dd>
                            @endif
                        </div>
                    </dl>

                    <div class="flex items-baseline justify-between border-t border-[#e6e3db] px-5 py-4">
                        <span class="text-[13px] font-semibold text-[#17181c]">Total bayar</span>
                        <span class="font-serif-display text-[22px] font-bold text-[#0f2255]">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="space-y-2.5 px-5 pb-5">
                        <button type="submit" class="btn-primary w-full !py-3">Konfirmasi &amp; Lanjut Pembayaran</button>
                        <a href="{{ route('cart.index') }}" class="block text-center text-[12px] font-semibold text-[#6b6f78] transition-colors hover:text-[#0f2255]">&larr; Periksa kembali keranjang</a>
                    </div>
                </aside>
            </div>
        </div>

        {{-- Tombol beli tempel di layar kecil --}}
        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-[#e6e3db] bg-white/95 px-4 py-3 backdrop-blur no-print lg:hidden">
            <div class="mx-auto flex max-w-[1400px] items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-[12px] font-semibold text-[#6b6f78]">Total {{ $totalQty }} eksemplar</p>
                    <p class="font-serif-display text-[18px] font-bold text-[#0f2255]">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                </div>
                <button type="submit" class="btn-primary shrink-0 !px-5 !py-2.5 text-[13px]">Konfirmasi Pesanan</button>
            </div>
        </div>
    </form>
</div>
@endsection
