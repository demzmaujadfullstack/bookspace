@extends('layouts.admin')

@section('title', 'Laporan & Analytics')
@section('page_title', 'Laporan Penjualan & Analytics')

@section('content')
<div class="space-y-6">

    {{-- Filter Date Range & Export Bar --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Mulai Dari</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="text-xs border border-gray-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Sampai Dengan</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="text-xs border border-gray-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="pt-4">
                    <button type="submit" class="bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-colors shadow">
                        Filter Periode
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2 md:pt-0">
                <a href="{{ route('admin.reports.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-colors shadow flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
                <button type="button" onclick="window.print()" class="border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                    Cetak
                </button>
            </div>
        </form>
    </div>

    {{-- Period KPI Summary --}}
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Total Pendapatan (Sukses)</span>
            <p class="font-display font-bold text-3xl text-orange-600">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Periode {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</p>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Total Transaksi Selesai</span>
            <p class="font-display font-bold text-3xl text-gray-900">{{ number_format($totalOrders) }} pesanan</p>
            <p class="text-[11px] text-gray-400 mt-1">Pesanan yang pembayarannya telah terverifikasi</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Best Sellers Ranking --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">10 Buku Terlaris</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                            <th class="pb-3 w-8">#</th>
                            <th class="pb-3">Judul Buku</th>
                            <th class="pb-3 text-center">Terjual</th>
                            <th class="pb-3 text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bestSellers as $i => $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 font-bold {{ $i < 3 ? 'text-orange-600' : 'text-gray-400' }}">{{ $i + 1 }}</td>
                            <td class="py-3 font-semibold text-gray-800">{{ $item->book_title }}</td>
                            <td class="py-3 text-center font-bold text-gray-900">{{ $item->total_sold }} eks</td>
                            <td class="py-3 text-right font-mono font-bold text-gray-900">Rp{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada data penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Daily Breakdown --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Rincian Penjualan Harian</h2>

            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3 text-center">Jumlah Transaksi</th>
                            <th class="pb-3 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailySales as $day)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2.5 font-medium text-gray-800">{{ \Carbon\Carbon::parse($day->date)->format('d F Y') }}</td>
                            <td class="py-2.5 text-center font-bold text-gray-700">{{ $day->count }}</td>
                            <td class="py-2.5 text-right font-mono font-bold text-green-600">Rp{{ number_format($day->revenue, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-400">Tidak ada transaksi harian pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
