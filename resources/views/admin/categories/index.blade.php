@extends('layouts.admin')

@section('title', 'Kategori Buku')
@section('page_title', 'Manajemen Kategori Buku')

@section('content')
<div class="grid lg:grid-cols-3 gap-6" x-data="{ editModal: false, activeCat: { id: '', name: '', description: '' }, deleteModal: false, deleteAction: '' }">

    {{-- ── LEFT: ADD FORM ───────────────────────────────────────── --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm sticky top-24">
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Tambah Kategori Baru</h2>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Sains Populer" required class="w-full text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Keterangan singkat mengenai kategori buku..." class="w-full text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs py-2.5 rounded-xl transition-colors shadow">
                    + Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    {{-- ── RIGHT: CATEGORIES TABLE ─────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h2 class="font-display font-bold text-base text-gray-900">Daftar Kategori</h2>
                    <p class="text-xs text-gray-400">Total {{ $categories->total() }} kategori terdaftar</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                            <th class="pb-3">Nama & Slug</th>
                            <th class="pb-3">Deskripsi</th>
                            <th class="pb-3 text-center">Jumlah Buku</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3">
                                <p class="font-bold text-gray-900 text-sm">{{ $cat->name }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">/{{ $cat->slug }}</p>
                            </td>
                            <td class="py-3 text-gray-500 max-w-[200px] truncate">
                                {{ $cat->description ?: '-' }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded-md text-[11px]">
                                    {{ $cat->books_count }} buku
                                </span>
                            </td>
                            <td class="py-3 text-right space-x-2">
                                <button
                                    type="button"
                                    @click="activeCat = { id: {{ $cat->id }}, name: '{{ addslashes($cat->name) }}', description: '{{ addslashes($cat->description ?? '') }}' }; editModal = true"
                                    class="text-blue-600 hover:text-blue-800 font-semibold"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    @click="deleteAction = '{{ route('admin.categories.destroy', $cat) }}'; deleteModal = true"
                                    class="text-red-500 hover:text-red-700 font-semibold"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada kategori buku.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    {{-- ── EDIT MODAL ──────────────────────────────────────────── --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl" @click.away="editModal = false">
            <h3 class="font-display font-bold text-base text-gray-900 mb-4 pb-2 border-b border-gray-100">Edit Kategori</h3>
            <form :action="'/admin/categories/' + activeCat.id" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" x-model="activeCat.name" required class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" x-model="activeCat.description" rows="3" class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-orange-300"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="editModal = false" class="text-xs text-gray-500 hover:bg-gray-100 px-3 py-2 rounded-xl">Batal</button>
                    <button type="submit" class="text-xs bg-[#0f2557] hover:bg-orange-500 text-white font-semibold px-4 py-2 rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── DELETE CONFIRMATION MODAL ──────────────────────────── --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="deleteModal = false">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Hapus Kategori?</h3>
            <p class="text-xs text-gray-500 mb-5">Kategori hanya dapat dihapus jika tidak memiliki buku yang terhubung.</p>
            <form :action="deleteAction" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="text-xs border border-gray-200 text-gray-700 px-4 py-2 rounded-xl">Batal</button>
                <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-xl">Ya, Hapus</button>
            </form>
        </div>
    </div>

</div>
@endsection
