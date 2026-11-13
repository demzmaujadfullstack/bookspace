@extends('layouts.admin')

@section('title', 'Detail Buku: ' . $book->title)
@section('page_title', 'Detail Buku')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.books.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Koleksi Buku</a>
        <div class="flex gap-2">
            <a href="{{ route('books.show', $book->slug) }}" target="_blank" class="text-xs border border-gray-200 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-xl font-medium">
                Lihat di Toko ↗
            </a>
            <a href="{{ route('admin.books.edit', $book) }}" class="text-xs bg-[#0f2557] hover:bg-orange-500 text-white px-4 py-1.5 rounded-xl font-semibold transition-colors">
                Edit Buku
            </a>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm">
        <div class="grid md:grid-cols-4 gap-6">
            <div class="md:col-span-1">
                <div class="aspect-[2/3] bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                    <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
                </div>
            </div>
            <div class="md:col-span-3 space-y-4 text-xs">
                <div>
                    <span class="inline-block bg-orange-100 text-orange-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-1">{{ $book->category->name ?? 'Uncategorized' }}</span>
                    <h1 class="font-display font-bold text-2xl text-gray-900 leading-tight">{{ $book->title }}</h1>
                    <p class="text-gray-500 mt-0.5">Penulis: <span class="font-semibold text-gray-800">{{ $book->author }}</span></p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3 border-y border-gray-100">
                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">Harga Jual</span>
                        <span class="font-bold text-sm text-gray-900">Rp{{ number_format($book->finalPrice(), 0, ',', '.') }}</span>
                        @if($book->hasDiscount())
                            <span class="text-[10px] text-orange-600 block">(-{{ $book->discount_percent }}%)</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">Stok Saat Ini</span>
                        <span class="font-bold text-sm {{ $book->stock <= 5 ? 'text-red-600' : 'text-gray-900' }}">{{ $book->stock }} eksemplar</span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">Total Terjual</span>
                        <span class="font-bold text-sm text-gray-900">{{ $book->soldCount() }} eksemplar</span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[10px] uppercase font-semibold block">ISBN</span>
                        <span class="font-mono text-xs font-semibold text-gray-800">{{ $book->isbn }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-gray-400 text-[10px] uppercase font-semibold block mb-1">Sinopsis / Deskripsi</span>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $book->description ?: 'Belum ada deskripsi.' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock Movement History --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Riwayat Perubahan Stok Buku Ini</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Waktu</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3 text-center">Perubahan</th>
                        <th class="pb-3 text-center">Stok Akhir</th>
                        <th class="pb-3">Keterangan / Alasan</th>
                        <th class="pb-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($book->movements as $m)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-2.5 text-gray-400 font-mono">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : ($m->type === 'out' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($m->type) }}
                            </span>
                        </td>
                        <td class="py-2.5 text-center font-bold {{ $m->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                        </td>
                        <td class="py-2.5 text-center font-bold text-gray-900">{{ $m->stock_after }}</td>
                        <td class="py-2.5 text-gray-600">{{ $m->reason }}</td>
                        <td class="py-2.5 text-gray-500">{{ $m->user->name ?? 'Sistem' }}</td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada riwayat pergerakan stok buku ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
