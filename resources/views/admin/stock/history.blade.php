@extends('layouts.admin')

@section('title', 'Riwayat Stok: ' . $book->title)
@section('page_title', 'Riwayat Perubahan Stok')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.stock.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Manajemen Stok</a>
        <span class="text-xs font-bold text-gray-700 bg-white border border-gray-100 px-3 py-1.5 rounded-xl shadow-sm">
            Stok Saat Ini: <span class="text-orange-600">{{ $book->stock }} eksemplar</span>
        </span>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-4 pb-4 border-b border-gray-100 mb-6">
            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="w-12 h-16 object-cover rounded-lg border border-gray-100" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'">
            <div>
                <h1 class="font-display font-bold text-lg text-gray-900">{{ $book->title }}</h1>
                <p class="text-xs text-gray-400 font-mono mt-0.5">ISBN: {{ $book->isbn }} • {{ $book->category->name ?? '-' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Waktu</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3 text-center">Perubahan</th>
                        <th class="pb-3 text-center">Stok Akhir</th>
                        <th class="pb-3">Alasan / Terkait</th>
                        <th class="pb-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $m)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3 text-gray-400 font-mono">{{ $m->created_at->format('d M Y, H:i') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : ($m->type === 'out' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($m->type) }}
                            </span>
                        </td>
                        <td class="py-3 text-center font-bold text-sm {{ $m->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                        </td>
                        <td class="py-3 text-center font-bold text-gray-900 text-sm">{{ $m->stock_after }}</td>
                        <td class="py-3 text-gray-700">
                            {{ $m->reason }}
                            @if($m->order)
                                <a href="{{ route('admin.orders.show', $m->order) }}" class="text-blue-600 hover:underline block text-[10px]">
                                    (Pesanan #{{ $m->order->code }})
                                </a>
                            @endif
                        </td>
                        <td class="py-3 text-gray-500">{{ $m->user->name ?? 'Sistem' }}</td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada riwayat pergerakan stok untuk buku ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $movements->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
