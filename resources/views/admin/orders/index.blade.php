@extends('layouts.admin')

@section('title', 'Manajemen Transaksi Pesanan')
@section('page_title', 'Manajemen Transaksi & Pembayaran')

@section('content')
<div class="space-y-6">

    {{-- Filter by Status Tabs --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-3 shadow-xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max text-xs">
            <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ !request('status') ? 'bg-[#0f2255] text-white shadow-xs' : 'text-gray-600 hover:bg-[#f4f2ec]' }}">
                Semua Pesanan ({{ $statusCounts['all'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'menunggu_verifikasi' ? 'bg-[#e05c1a] text-white shadow-xs' : 'text-[#e05c1a] bg-orange-50 hover:bg-orange-100' }}">
                Perlu Verifikasi ({{ $statusCounts['menunggu_verifikasi'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'menunggu_pembayaran' ? 'bg-amber-600 text-white shadow-xs' : 'text-amber-800 bg-amber-50 hover:bg-amber-100' }}">
                Belum Bayar ({{ $statusCounts['menunggu_pembayaran'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'dibayar']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'dibayar' ? 'bg-[#0f2255] text-white shadow-xs' : 'text-gray-600 hover:bg-[#f4f2ec]' }}">
                Dibayar ({{ $statusCounts['dibayar'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'diproses']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'diproses' ? 'bg-[#0f2255] text-white shadow-xs' : 'text-gray-600 hover:bg-[#f4f2ec]' }}">
                Diproses ({{ $statusCounts['diproses'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'dikirim']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'dikirim' ? 'bg-[#0f2255] text-white shadow-xs' : 'text-gray-600 hover:bg-[#f4f2ec]' }}">
                Dikirim ({{ $statusCounts['dikirim'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'selesai']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'selesai' ? 'bg-emerald-700 text-white shadow-xs' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }}">
                Selesai ({{ $statusCounts['selesai'] }})
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'dibatalkan']) }}" class="px-3.5 py-2 rounded-xl font-semibold transition-colors {{ request('status') === 'dibatalkan' ? 'bg-red-700 text-white shadow-xs' : 'text-red-800 bg-red-50 hover:bg-red-100' }}">
                Dibatalkan ({{ $statusCounts['dibatalkan'] }})
            </a>
        </div>
    </div>

    {{-- Search & Table Card --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-4 pb-4 border-b border-[#f0eee6]">
            <div>
                <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Daftar Transaksi Pesanan</h2>
                <p class="text-xs text-gray-500 font-serif italic">Total {{ $orders->total() }} pesanan ditemukan dalam sistem</p>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice / pembeli / HP..." class="text-xs border border-[#dcd8cd] rounded-xl px-3.5 py-2 focus:outline-none focus:border-[#0f2255] bg-gray-50">
                <button type="submit" class="bg-[#0f2255] hover:bg-[#1a3570] text-white text-xs px-3.5 py-2 rounded-xl font-semibold transition-colors">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#e8e6df] text-gray-400 uppercase tracking-wider text-[10px] font-bold">
                        <th class="pb-3">Invoice &amp; Tanggal</th>
                        <th class="pb-3">Pembeli</th>
                        <th class="pb-3">Buku Dipesan</th>
                        <th class="pb-3">Total Tagihan</th>
                        <th class="pb-3">Metode &amp; Bukti</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0eee6]">
                    @forelse($orders as $order)
                    <tr class="hover:bg-[#fafaf8] transition-colors">
                        <td class="py-3.5">
                            <p class="font-mono font-bold text-[#0f2255] text-sm">#{{ $order->code }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900">{{ $order->recipient_name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $order->phone }}</p>
                        </td>
                        <td class="py-3.5 max-w-[200px]">
                            <p class="truncate font-medium text-gray-800">{{ $order->items->first()?->book_title }}</p>
                            @if($order->items->count() > 1)
                                <span class="text-[10px] text-[#e05c1a] font-semibold">+{{ $order->items->count() - 1 }} buku lainnya</span>
                            @endif
                        </td>
                        <td class="py-3.5 font-serif-display font-bold text-gray-900 text-sm">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5">
                            <span class="block text-gray-700 font-medium">{{ $order->latestPayment?->methodLabel() ?? 'Manual' }}</span>
                            @if($order->latestPayment?->proof_image)
                                <a href="{{ route('payment.proof', $order->latestPayment) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-[#e05c1a] font-semibold hover:underline mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Lihat Bukti
                                </a>
                            @endif
                        </td>
                        <td class="py-3.5 text-center">
                            @php
                                $badgeClasses = match($order->status) {
                                    'pending' => 'bg-gray-100 text-gray-700',
                                    'menunggu_pembayaran' => 'bg-amber-100 text-amber-900',
                                    'menunggu_verifikasi' => 'bg-blue-100 text-blue-900 animate-pulse',
                                    'dibayar' => 'bg-indigo-100 text-indigo-900',
                                    'diproses' => 'bg-purple-100 text-purple-900',
                                    'dikirim' => 'bg-cyan-100 text-cyan-900',
                                    'selesai' => 'bg-emerald-100 text-emerald-900',
                                    'dibatalkan' => 'bg-red-100 text-red-900',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $badgeClasses }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs bg-[#0f2255] hover:bg-[#1a3570] text-white font-medium px-3.5 py-1.5 rounded-lg transition-colors shadow-xs">
                                Kelola Pesanan
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400 font-serif italic">Tidak ada transaksi ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>

</div>
@endsection
