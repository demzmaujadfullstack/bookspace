@extends('layouts.admin')

@section('title', 'Pengaturan Website')
@section('page_title', 'Pengaturan Toko & Website')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <x-form-errors class="mb-6" />

            {{-- 1. Identitas Toko --}}
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Identitas Toko</h2>
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Toko Online <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'BOOKSPACE') }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Tagline Slogan</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? 'Toko Buku Online Terpercaya') }}" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Email Toko <span class="text-red-500">*</span></label>
                    <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? 'info@bookspace.id') }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor WhatsApp Toko</label>
                    <input type="text" name="site_whatsapp" value="{{ old('site_whatsapp', $settings['site_whatsapp'] ?? '6281234567890') }}" placeholder="Format: 6281234567890 (tanpa +)" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Alamat Fisik Toko</label>
                    <textarea name="site_address" rows="2" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                </div>
            </div>

            {{-- 2. Branding --}}
            @php
                $logoPreview = \App\Models\Setting::mediaUrl('logo_image');
                $faviconPreview = \App\Models\Setting::mediaUrl('favicon_image');
            @endphp
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Logo &amp; Favicon Toko</h2>
            <div class="mb-6 space-y-4">
                <p class="text-[11px] text-gray-500 -mb-1">Identitas visual toko bisa diganti dari sini tanpa menyentuh kode. Logo tampil di navbar, footer, dan sidebar admin, sedangkan favicon tampil di tab browser.</p>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/40">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Logo Toko</label>
                        <div class="mb-3 flex h-20 items-center justify-center rounded-xl border border-gray-100 bg-white p-3">
                            @if($logoPreview)
                                <img src="{{ $logoPreview }}" alt="Logo saat ini" class="max-h-full max-w-full object-contain">
                            @else
                                <span class="text-[11px] text-gray-400">Belum ada logo &mdash; memakai tanda BOOKSPACE bawaan.</span>
                            @endif
                        </div>
                        <input type="file" name="logo_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0f2557] file:text-white hover:file:bg-orange-500 border border-gray-200 rounded-xl p-2 cursor-pointer bg-white">
                        <label class="mt-2 flex items-center gap-2 text-[11px] text-gray-600">
                            <input type="checkbox" name="remove_logo_image" value="1" class="rounded border-gray-300 text-[#e05c1a] focus:ring-orange-300">
                            Hapus logo dan kembali ke tampilan bawaan
                        </label>
                        <p class="text-[11px] text-gray-400 mt-1">Format PNG, JPG, atau WEBP. Maksimal 2 MB, disarankan tinggi 72&ndash;160 px.</p>
                    </div>

                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/40">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Favicon (Ikon Tab)</label>
                        <div class="mb-3 flex h-20 items-center gap-3 rounded-xl border border-gray-100 bg-white p-3">
                            @if($faviconPreview)
                                <img src="{{ $faviconPreview }}" alt="Favicon saat ini" class="h-10 w-10 shrink-0 rounded object-contain">
                                <span class="text-[11px] text-gray-500">Pratinjau pada ukuran kecil sering terlihat berbeda dari aslinya.</span>
                            @else
                                <span class="text-[11px] text-gray-400">Belum ada favicon &mdash; memakai logo toko atau favicon.ico bawaan.</span>
                            @endif
                        </div>
                        <input type="file" name="favicon_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0f2557] file:text-white hover:file:bg-orange-500 border border-gray-200 rounded-xl p-2 cursor-pointer bg-white">
                        <label class="mt-2 flex items-center gap-2 text-[11px] text-gray-600">
                            <input type="checkbox" name="remove_favicon_image" value="1" class="rounded border-gray-300 text-[#e05c1a] focus:ring-orange-300">
                            Hapus favicon
                        </label>
                        <p class="text-[11px] text-gray-400 mt-1">Format PNG atau WEBP. Maksimal 1 MB, disarankan persegi 64&times;64 px.</p>
                    </div>
                </div>

                <label class="flex items-start gap-2 text-[12px] text-gray-700">
                    <input type="checkbox" name="logo_show_text" value="1"
                           {{ old('logo_show_text', ($settings['logo_show_text'] ?? '1') === '1') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-[#e05c1a] focus:ring-orange-300">
                    <span>Tampilkan nama toko di samping logo. Centang bila logo Anda hanya berisi ikon; hilangkan centang bila logo sudah memuat tulisan nama toko.</span>
                </label>
            </div>

            {{-- 3. Rekening Pembayaran --}}
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Informasi Rekening Bank (Transfer Manual)</h2>
            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? 'BCA') }}" placeholder="Contoh: BCA / Mandiri / BNI" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '1234567890') }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Atas Nama Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $settings['bank_account_name'] ?? 'BOOKSPACE Indonesia') }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
            </div>

            {{-- 4. QRIS Image --}}
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Gambar QRIS Toko</h2>
            <div class="mb-6 flex items-start gap-4">
                @if(isset($settings['qris_image']) && $settings['qris_image'] && file_exists(public_path('storage/' . $settings['qris_image'])))
                    <div class="w-28 h-28 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden p-2 shrink-0">
                        <img src="{{ asset('storage/' . $settings['qris_image']) }}" alt="QRIS" class="w-full h-full object-contain">
                    </div>
                @endif
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Upload Gambar Kode QRIS</label>
                    <input type="file" name="qris_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0f2557] file:text-white hover:file:bg-orange-500 border border-gray-200 rounded-xl p-2 cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG (Maksimal 3MB). Gambar ini akan ditampilkan kepada pembeli yang memilih pembayaran QRIS.</p>
                </div>
            </div>

            {{-- 5. Carousel & Footer --}}
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Konfigurasi Slider & Footer</h2>
            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Durasi Carousel Otomatis (milidetik) <span class="text-red-500">*</span></label>
                    <input type="number" name="carousel_duration" value="{{ old('carousel_duration', $settings['carousel_duration'] ?? 5000) }}" min="1000" max="30000" step="500" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    <p class="text-[11px] text-gray-400 mt-1">1000 ms = 1 detik. Default: 5000 (5 detik per slide).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Ambang Gratis Ongkir (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? 200000) }}" min="0" max="10000000" step="1000" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    <p class="text-[11px] text-gray-400 mt-1">Belanja di atas nominal ini mendapat gratis ongkir. Angka ini dipakai di keranjang dan banner atas.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Deskripsi Footer Toko</label>
                    <textarea name="footer_description" rows="3" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
