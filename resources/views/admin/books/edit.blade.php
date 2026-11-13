@extends('layouts.admin')

@section('title', 'Edit: ' . $book->title)
@section('page_title', 'Edit Data Buku')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.books.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Koleksi Buku</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" x-data="{ price: {{ old('price', $book->price) }}, disc: {{ old('discount_percent', $book->discount_percent) }} }">
            @csrf
            @method('PATCH')

            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Informasi Utama</h2>

            <div class="grid md:grid-cols-2 gap-4 mb-4">
                {{-- Judul --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Penulis --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    @error('author') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kategori Buku <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Penerbit --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                {{-- ISBN --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor ISBN <span class="text-red-500">*</span></label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                    @error('isbn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tahun Terbit --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Tahun Terbit</label>
                    <input type="number" name="year" value="{{ old('year', $book->year) }}" min="1900" max="{{ date('Y') + 1 }}" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                {{-- Rekomendasi Switch --}}
                <div class="flex items-center gap-3 pt-6">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $book->is_featured) ? 'checked' : '' }} class="text-orange-500 rounded">
                    <label for="is_featured" class="text-xs font-semibold text-gray-700 cursor-pointer">Tampilkan di Rekomendasi Pilihan</label>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Sinopsis / Deskripsi Buku</label>
                <textarea name="description" rows="4" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">{{ old('description', $book->description) }}</textarea>
            </div>

            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Harga, Diskon & Stok</h2>

            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Harga Pokok (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" x-model.number="price" min="0" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Diskon (%)</label>
                    <input type="number" name="discount_percent" x-model.number="disc" min="0" max="99" class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Stok Tersedia (Eksemplar) <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" min="0" required class="w-full text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>
            </div>

            <div class="mb-6 p-4 bg-orange-50/50 border border-orange-100 rounded-xl flex items-center justify-between text-xs">
                <span class="text-gray-600">Harga Jual Akhir Setelah Diskon:</span>
                <span class="font-display font-bold text-base text-orange-600" x-text="'Rp' + Math.round(price * (1 - (disc || 0)/100)).toLocaleString('id-ID')"></span>
            </div>

            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Sampul Buku (Cover)</h2>

            <div class="mb-8 flex items-start gap-4">
                <div class="w-20 h-28 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden shrink-0">
                    <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Ganti Gambar Sampul (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0f2557] file:text-white hover:file:bg-orange-500 border border-gray-200 rounded-xl p-2 cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti cover yang sudah ada.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.books.index') }}" class="border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="bg-[#0f2557] hover:bg-orange-500 text-white px-6 py-2.5 rounded-xl text-xs font-semibold transition-colors shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
