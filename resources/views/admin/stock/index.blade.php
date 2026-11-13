@extends('layouts.admin')

@section('title', 'Manajemen Stok Buku')
@section('page_title', 'Manajemen & Monitoring Stok')

@section('content')
<div class="space-y-6" x-data="{ adjustModal: false, activeBook: { id: '', title: '', currentStock: 0 } }">

    {{-- Stock KPI Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.stock.index') }}" class="bg-white border rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-colors {{ !request('status') ? 'border-[#0f2557] ring-1 ring-[#0f2557]' : 'border-gray-100' }}">
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Semua Koleksi</span>
            <span class="font-display font-bold text-2xl text-gray-900">{{ $stats['total_books'] }}</span>
        </a>

        <a href="{{ route('admin.stock.index', ['status' => 'tersedia']) }}" class="bg-white border rounded-2xl p-4 shadow-sm hover:border-green-300 transition-colors {{ request('status') === 'tersedia' ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-100' }}">
            <span class="text-[10px] font-semibold text-green-600 uppercase tracking-wider block mb-1">Stok Aman / Tersedia</span>
            <span class="font-display font-bold text-2xl text-green-600">{{ $stats['in_stock'] }}</span>
        </a>

        <a href="{{ route('admin.stock.index', ['status' => 'menipis']) }}" class="bg-white border rounded-2xl p-4 shadow-sm hover:border-amber-300 transition-colors {{ request('status') === 'menipis' ? 'border-amber-500 ring-1 ring-amber-500' : 'border-gray-100' }}">
            <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider block mb-1">Stok Menipis (≤5)</span>
            <span class="font-display font-bold text-2xl text-amber-600">{{ $stats['low_stock'] }}</span>
        </a>

        <a href="{{ route('admin.stock.index', ['status' => 'habis']) }}" class="bg-white border rounded-2xl p-4 shadow-sm hover:border-red-300 transition-colors {{ request('status') === 'habis' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-100' }}">
            <span class="text-[10px] font-semibold text-red-600 uppercase tracking-wider block mb-1">Stok Habis (0)</span>
            <span class="font-display font-bold text-2xl text-red-600">{{ $stats['out_of_stock'] }}</span>
        </a>
    </div>

    {{-- Filter & Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-4 pb-3 border-b border-gray-100">
            <h2 class="font-display font-bold text-base text-gray-900">Daftar Stok Inventaris</h2>
            <form method="GET" action="{{ route('admin.stock.index') }}" class="flex items-center gap-2">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / ISBN..." class="text-xs border border-gray-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-orange-300">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-xl font-medium">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 w-12">Cover</th>
                        <th class="pb-3">Judul Buku & ISBN</th>
                        <th class="pb-3">Kategori</th>
                        <th class="pb-3 text-center">Stok Saat Ini</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-center">Terjual</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3">
                            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-9 h-12 object-cover rounded border border-gray-100" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                        </td>
                        <td class="py-3 max-w-[240px]">
                            <p class="font-bold text-gray-900 text-xs truncate">{{ $book->title }}</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">ISBN: {{ $book->isbn }}</p>
                        </td>
                        <td class="py-3 text-gray-600">{{ $book->category->name ?? '-' }}</td>
                        <td class="py-3 text-center font-bold text-sm text-gray-900">{{ $book->stock }}</td>
                        <td class="py-3 text-center">
                            @php
                                $badge = match($book->stockStatus()) {
                                    'Habis' => 'bg-red-100 text-red-700',
                                    'Stok menipis' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-green-100 text-green-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $badge }}">
                                {{ $book->stockStatus() }}
                            </span>
                        </td>
                        <td class="py-3 text-center text-gray-600 font-medium">{{ $book->soldCount() }} eks</td>
                        <td class="py-3 text-right space-x-2">
                            <button
                                type="button"
                                @click="activeBook = { id: {{ $book->id }}, title: '{{ addslashes($book->title) }}', currentStock: {{ $book->stock }} }; adjustModal = true"
                                class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-medium px-3 py-1.5 rounded-lg transition-colors"
                            >
                                Sesuaikan Stok
                            </button>
                            <a href="{{ route('admin.stock.history', $book) }}" class="text-xs text-gray-500 hover:text-gray-800 underline">
                                Riwayat
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">Tidak ada buku dalam kategori ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $books->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Adjust Stock Modal --}}
    <div x-show="adjustModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl" @click.away="adjustModal = false">
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Penyesuaian Stok Buku</h3>
            <p class="text-xs text-gray-500 mb-4" x-text="activeBook.title"></p>

            <form :action="'/admin/stok/' + activeBook.id" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-600 flex justify-between">
                    <span>Stok Saat Ini:</span>
                    <span class="font-bold text-gray-900" x-text="activeBook.currentStock + ' eksemplar'"></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Jumlah Stok Baru <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" min="0" required class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Alasan Perubahan <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" placeholder="Contoh: Penerimaan pasokan baru dari penerbit / Koreksi fisik" required class="w-full text-xs border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-orange-300">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="adjustModal = false" class="text-xs text-gray-500 hover:bg-gray-100 px-4 py-2 rounded-xl">Batal</button>
                    <button type="submit" class="text-xs bg-[#0f2557] hover:bg-orange-500 text-white font-semibold px-4 py-2 rounded-xl shadow">Simpan Stok</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
