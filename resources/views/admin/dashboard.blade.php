@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan & Analitik Toko')

@section('content')
<div class="space-y-6">

    {{-- ── Quick Action Bar ──────────────────────────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 mr-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#e05c1a]"></span>
                Aksi Cepat:
            </span>
            <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-1.5 text-xs bg-[#0f2255] hover:bg-[#1a3570] text-white px-3.5 py-2 rounded-xl font-medium transition-colors shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Judul Buku
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}" class="inline-flex items-center gap-1.5 text-xs bg-[#e05c1a] hover:bg-[#c54e12] text-white px-3.5 py-2 rounded-xl font-medium transition-colors shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verifikasi Pembayaran
            </a>
            <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center gap-1.5 text-xs bg-[#f4f2ec] hover:bg-[#ebe7de] text-[#0f2255] border border-[#dcd8cd] px-3.5 py-2 rounded-xl font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#e05c1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Audit Stok Buku
            </a>
        </div>
        <div class="text-xs text-gray-400 font-mono">
            Pembaruan Real-Time &bull; {{ now()->translatedFormat('d M Y') }}
        </div>
    </div>

    {{-- ── 6 KPI STAT CARDS ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Total Buku --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Judul Buku</span>
                <span class="p-1.5 bg-[#f4f2ec] text-[#0f2255] rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-2xl text-[#0f2255]">{{ number_format($totalBooks) }}</p>
            <a href="{{ route('admin.books.index') }}" class="text-[10px] text-[#0f2255] font-semibold hover:underline mt-1 block">Kelola katalog &rarr;</a>
        </div>

        {{-- Total Kategori --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kategori Genre</span>
                <span class="p-1.5 bg-indigo-50 text-indigo-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-2xl text-gray-900">{{ number_format($totalCategories) }}</p>
            <a href="{{ route('admin.categories.index') }}" class="text-[10px] text-indigo-700 font-semibold hover:underline mt-1 block">Kelola genre &rarr;</a>
        </div>

        {{-- Total User --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</span>
                <span class="p-1.5 bg-cyan-50 text-cyan-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-2xl text-gray-900">{{ number_format($totalUsers) }}</p>
            <a href="{{ route('admin.users.index') }}" class="text-[10px] text-cyan-700 font-semibold hover:underline mt-1 block">Data pembaca &rarr;</a>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Pesanan</span>
                <span class="p-1.5 bg-purple-50 text-purple-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-2xl text-gray-900">{{ number_format($totalOrders) }}</p>
            <a href="{{ route('admin.orders.index') }}" class="text-[10px] text-purple-700 font-semibold hover:underline mt-1 block">Lihat order &rarr;</a>
        </div>

        {{-- Pendapatan Total --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Omzet</span>
                <span class="p-1.5 bg-emerald-50 text-emerald-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-lg text-emerald-700 truncate">Rp{{ number_format($revenue, 0, ',', '.') }}</p>
            <span class="text-[10px] text-gray-400 block mt-1">Bulan ini: Rp{{ number_format($monthRevenue, 0, ',', '.') }}</span>
        </div>

        {{-- Perlu Verifikasi --}}
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-[#e05c1a] uppercase tracking-wider">Perlu Tindakan</span>
                <span class="p-1.5 bg-orange-50 text-[#e05c1a] rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="font-serif-display font-bold text-2xl text-[#e05c1a]">{{ number_format($pendingOrders) }}</p>
            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_verifikasi']) }}" class="text-[10px] text-[#e05c1a] font-semibold hover:underline mt-1 block">Verifikasi sekarang &rarr;</a>
        </div>
    </div>

    {{-- ── SALES CHART & LOW STOCK ──────────────────────────────── --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Chart --}}
        <div class="lg:col-span-2 bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                <div>
                    <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Kurva Penjualan 30 Hari Terakhir</h2>
                    <p class="text-xs text-gray-500 font-serif italic">Performa omzet harian transaksi buku yang telah disetujui</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold bg-[#f4f2ec] text-[#0f2255] border border-[#dcd8cd] px-3 py-1 rounded-md">IDR (Rupiah)</span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="lg:col-span-1 bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-xs flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#f0eee6]">
                <h2 class="font-serif-display font-bold text-base text-[#0f2255] flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    Stok Menipis
                </h2>
                <a href="{{ route('admin.stock.index') }}" class="text-xs text-[#e05c1a] font-semibold hover:underline">Semua &rarr;</a>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2.5">
                @forelse($lowStockBooks as $b)
                <div class="flex items-center justify-between text-xs p-3 bg-[#fafaf8] border border-[#f0eee6] rounded-xl hover:border-[#0f2255]/30 transition-colors">
                    <div class="min-w-0 pr-2">
                        <p class="font-bold text-gray-900 truncate">{{ $b->title }}</p>
                        <p class="text-[11px] text-gray-400 font-serif italic">{{ $b->category->name ?? 'Kategori' }} &bull; {{ $b->author }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $b->stock === 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900' }} shrink-0">
                        {{ $b->stock }} eks
                    </span>
                </div>
                @empty
                    <div class="text-center py-12 text-gray-400 text-xs font-serif italic">
                        Semua stok buku dalam batas persediaan aman.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── RECENT ORDERS ────────────────────────────────────────── --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-[#f0eee6]">
            <div>
                <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Transaksi Pesanan Terkini</h2>
                <p class="text-xs text-gray-500 font-serif italic">Pantau pesanan pembaca dan konfirmasi status pembayaran</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#e05c1a] font-bold hover:underline">
                Kelola Semua Pesanan ({{ $totalOrders }}) &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#e8e6df] text-gray-400 uppercase tracking-wider text-[10px] font-bold">
                        <th class="pb-3">Nomor Faktur</th>
                        <th class="pb-3">Nama Pembeli</th>
                        <th class="pb-3">Metode Bayar</th>
                        <th class="pb-3">Total Tagihan</th>
                        <th class="pb-3">Status Saat Ini</th>
                        <th class="pb-3">Waktu Pemesanan</th>
                        <th class="pb-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0eee6]">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-[#fafaf8] transition-colors">
                        <td class="py-3.5 font-mono font-bold text-[#0f2255]">#{{ $order->code }}</td>
                        <td class="py-3.5">
                            <p class="font-bold text-gray-900">{{ $order->recipient_name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $order->phone }}</p>
                        </td>
                        <td class="py-3.5">
                            <span class="font-medium text-gray-700">{{ $order->latestPayment?->methodLabel() ?? 'Manual' }}</span>
                        </td>
                        <td class="py-3.5 font-bold font-serif-display text-gray-900">
                            Rp{{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5">
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
                        <td class="py-3.5 text-gray-400 font-mono">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3.5 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs bg-[#0f2255] hover:bg-[#1a3570] text-white font-medium px-3 py-1.5 rounded-lg transition-colors shadow-xs">
                                Detail &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 font-serif italic">Belum ada transaksi pesanan yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('salesChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    // Create subtle gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(15, 34, 85, 0.22)');
    gradient.addColorStop(1, 'rgba(15, 34, 85, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($days) !!},
            datasets: [{
                label: 'Penjualan (Rp)',
                data: {!! json_encode($sales) !!},
                borderColor: '#0f2255',
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointBackgroundColor: '#e05c1a',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Penjualan: Rp' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        maxTicksLimit: 12
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.04)' },
                    ticks: {
                        font: { size: 10 },
                        callback: val => 'Rp' + (val >= 1000 ? (val/1000).toLocaleString('id-ID') + 'k' : val.toLocaleString('id-ID'))
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
