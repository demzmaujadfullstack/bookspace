@extends('layouts.admin')

@section('title', 'Manajemen Buku')
@section('page_title', 'Koleksi & Manajemen Buku')

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteAction: '', bookTitle: '' }">

    {{-- Top Action & Filter Bar --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            {{-- Search & Filter --}}
            <form method="GET" action="{{ route('admin.books.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul, penulis, atau ISBN..."
                    class="text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-orange-300"
                >

                <select name="category" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Cari
                </button>

                @if(request('search') || request('category'))
                    <a href="{{ route('admin.books.index') }}" class="text-xs text-orange-600 hover:underline">Reset</a>
                @endif
            </form>

            {{-- Create Button --}}
            <a href="{{ route('admin.books.create') }}" class="bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors shadow flex items-center justify-center gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Buku Baru
            </a>
        </div>
    </div>

    {{-- Books Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 w-14">Cover</th>
                        <th class="pb-3">Judul & ISBN</th>
                        <th class="pb-3">Kategori</th>
                        <th class="pb-3">Penulis</th>
                        <th class="pb-3">Harga</th>
                        <th class="pb-3 text-center">Stok</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3">
                            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded-md border border-gray-100">
                        </td>
                        <td class="py-3 max-w-[220px]">
                            <p class="font-bold text-gray-900 text-xs truncate">{{ $book->title }}</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">ISBN: {{ $book->isbn }}</p>
                            @if($book->is_featured)
                                <span class="inline-block mt-1 bg-amber-100 text-amber-800 text-[9px] font-bold px-1.5 py-0.5 rounded">Rekomendasi</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="bg-gray-100 text-gray-700 font-medium px-2 py-0.5 rounded text-[11px]">
                                {{ $book->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-600 truncate max-w-[140px]">{{ $book->author }}</td>
                        <td class="py-3">
                            @if($book->hasDiscount())
                                <p class="font-bold text-orange-600">Rp{{ number_format($book->finalPrice(), 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400 line-through">Rp{{ number_format($book->price, 0, ',', '.') }} (-{{ $book->discount_percent }}%)</p>
                            @else
                                <p class="font-bold text-gray-900">Rp{{ number_format($book->price, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @php
                                $stockColor = match(true) {
                                    $book->stock === 0 => 'bg-red-100 text-red-700',
                                    $book->stock <= \App\Models\Book::STOCK_LOW_THRESHOLD => 'bg-amber-100 text-amber-800',
                                    default => 'bg-green-100 text-green-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $stockColor }}">
                                {{ $book->stock }} eks
                            </span>
                        </td>
                        <td class="py-3 text-right space-x-2">
                            <a href="{{ route('admin.books.show', $book) }}" class="text-gray-500 hover:text-gray-700 font-semibold" title="Detail">Detail</a>
                            <a href="{{ route('admin.books.edit', $book) }}" class="text-blue-600 hover:text-blue-800 font-semibold" title="Edit">Edit</a>
                            <button
                                type="button"
                                @click="deleteAction = '{{ route('admin.books.destroy', $book) }}'; bookTitle = '{{ addslashes($book->title) }}'; deleteModal = true"
                                class="text-red-500 hover:text-red-700 font-semibold"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">Tidak ada buku ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $books->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="deleteModal = false">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Hapus Buku?</h3>
            <p class="text-xs text-gray-500 mb-1">Anda akan menghapus buku:</p>
            <p class="text-xs font-bold text-gray-800 mb-5" x-text="bookTitle"></p>
            <form :action="deleteAction" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="text-xs border border-gray-200 text-gray-700 px-4 py-2 rounded-xl">Batal</button>
                <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-xl">Hapus Sekarang</button>
            </form>
        </div>
    </div>

</div>
@endsection
