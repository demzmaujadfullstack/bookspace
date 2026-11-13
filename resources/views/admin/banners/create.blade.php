@extends('layouts.admin')

@section('title', 'Tambah Banner Baru')
@section('page_title', 'Tambah Banner Carousel')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.banners.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Manajemen Banner</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Promo Spesial Hari Buku Nasional" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Sub-judul / Keterangan</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Dapatkan diskon hingga 25% untuk buku-buku pilihan" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Teks Tombol (CTA)</label>
                        <input type="text" name="cta_text" value="{{ old('cta_text', 'Lihat Koleksi') }}" placeholder="Contoh: Belanja Sekarang" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Link Tujuan (URL)</label>
                        <input type="text" name="cta_link" value="{{ old('cta_link', '/books') }}" placeholder="Contoh: /books?promo=1" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor Urutan Tampilan <span class="text-red-500">*</span></label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" min="0" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="text-orange-500 rounded">
                        <label for="is_active" class="text-xs font-semibold text-gray-700 cursor-pointer">Aktifkan langsung di Carousel</label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Upload Gambar Background (Opsional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0f2557] file:text-white hover:file:bg-orange-500 border border-gray-200 rounded-xl p-2 cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 4MB). Jika kosong, gradient warna default BOOKSPACE akan digunakan.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.banners.index') }}" class="border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="bg-[#0f2557] hover:bg-orange-500 text-white px-6 py-2.5 rounded-xl text-xs font-semibold transition-colors shadow">
                    Simpan Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
