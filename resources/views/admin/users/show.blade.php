@extends('layouts.admin')

@section('title', 'Detail Pengguna: ' . $user->name)
@section('page_title', 'Detail Akun Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Manajemen User</a>

    {{-- User Profile Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-4 pb-5 border-b border-gray-100 mb-5">
            <div class="w-16 h-16 rounded-full bg-orange-500 text-white flex items-center justify-center font-display font-bold text-2xl shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="font-display font-bold text-xl text-gray-900">{{ $user->name }}</h1>
                <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }} • {{ $user->phone ?: 'Tidak ada nomor telepon' }}</p>
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-gray-400 text-[10px] uppercase font-semibold block">Total Transaksi</span>
                <span class="font-bold text-base text-gray-900">{{ $user->orders->count() }} pesanan</span>
            </div>
            <div>
                <span class="text-gray-400 text-[10px] uppercase font-semibold block">Total Belanja</span>
                <span class="font-bold text-base text-orange-600">Rp{{ number_format($user->orders->whereIn('status', \App\Models\Order::STOCK_REDUCED_STATUSES)->sum('total'), 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-[10px] uppercase font-semibold block">Terdaftar Pada</span>
                <span class="font-medium text-gray-800">{{ $user->created_at->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- User's Orders --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h2 class="font-display font-bold text-base text-gray-900 mb-4 pb-3 border-b border-gray-100">Riwayat Pesanan Pengguna Ini</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Invoice</th>
                        <th class="pb-3">Tanggal</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($user->orders as $order)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3 font-mono font-bold text-gray-900">#{{ $order->code }}</td>
                        <td class="py-3 text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 font-bold text-gray-900">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-orange-600 hover:underline font-semibold">
                                Lihat Pesanan →
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">Pengguna belum memiliki transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
