@extends('layouts.bookspace')

@section('title', 'Dashboard Akun Pembaca')

@section('content')
<div class="px-5 md:px-8 py-8 md:py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ── Sidebar Menu (4 cols) ──────────────────────────── --}}
        <div class="lg:col-span-4">
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm sticky top-28">
                <div class="flex items-center gap-3.5 pb-5 border-b border-[#f0eee6] mb-5">
                    <div class="w-12 h-12 bg-[#0f2255] rounded-full flex items-center justify-center text-white font-bold text-lg font-serif-display">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-serif-display font-bold text-base text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                <nav class="space-y-1 text-xs sm:text-sm font-medium">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-[#0f2255] text-white' : 'text-gray-600 hover:bg-[#fafaf8]' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Pembaca</span>
                    </a>
                    <a href="{{ route('user.orders') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-gray-600 hover:bg-[#fafaf8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Riwayat Pesanan Buku</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-gray-600 hover:bg-[#fafaf8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span>Daftar Buku Favorit</span>
                    </a>
                    <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-gray-600 hover:bg-[#fafaf8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Pengaturan Profil</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-gray-600 hover:bg-[#fafaf8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Keranjang Belanja</span>
                    </a>
                    <a href="{{ route('help.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-gray-600 hover:bg-[#fafaf8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Pusat Bantuan &amp; Chat</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- ── Main Content (8 cols) ──────────────────────────── --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="bg-[#0b1736] rounded-2xl p-6 sm:p-8 text-white shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
                <div class="relative z-10">
                    <span class="text-xs uppercase tracking-widest text-[#e05c1a] font-bold block mb-1">Ruang Pembaca</span>
                    <h1 class="font-serif-display font-bold text-2xl sm:text-3xl mb-1.5">Salam Literasi, {{ $user->name }}!</h1>
                    <p class="text-xs sm:text-sm text-white/70">Pantau proses pengiriman pesanan dan jelajahi kurasi buku terbaru kami.</p>
                </div>
                <a href="{{ route('books.index') }}" class="relative z-10 btn-orange text-xs font-semibold px-5 py-3 rounded-lg shadow-md shrink-0">
                    Jelajahi Katalog Buku &rarr;
                </a>
            </div>

            {{-- Stats Tiles --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Pesanan</p>
                    <p class="font-serif-display font-bold text-3xl text-[#0f2255]">{{ $stats['total_orders'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Seluruh transaksi Anda</p>
                </div>
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wider text-[#e05c1a] font-bold mb-1">Perlu Pembayaran / Verifikasi</p>
                    <p class="font-serif-display font-bold text-3xl text-[#e05c1a]">{{ $stats['pending_orders'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Menunggu tindak lanjut</p>
                </div>
                <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 shadow-sm">
                    <p class="text-[11px] uppercase tracking-wider text-emerald-600 font-bold mb-1">Pesanan Selesai</p>
                    <p class="font-serif-display font-bold text-3xl text-emerald-700">{{ $stats['completed_orders'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Buku telah diterima</p>
                </div>
            </div>

            {{-- Recent Orders Table --}}
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-[#f0eee6]">
                    <div>
                        <h2 class="font-serif-display font-bold text-lg text-[#0f2255]">Pesanan Buku Terbaru</h2>
                        <p class="text-xs text-gray-500">Daftar transaksi terkini di akun Anda</p>
                    </div>
                    <a href="{{ route('user.orders') }}" class="text-xs text-[#0f2255] hover:text-[#e05c1a] font-semibold transition-colors">
                        Lihat Seluruh Pesanan &rarr;
                    </a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="text-center py-10 text-gray-400 text-xs sm:text-sm">
                        <p>Anda belum memiliki riwayat pembelian buku.</p>
                        <a href="{{ route('books.index') }}" class="inline-block mt-3 text-xs text-[#e05c1a] hover:underline font-bold">
                            Temukan Buku Pertama Anda Sekarang &rarr;
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-[#f0eee6]">
                        @foreach($recentOrders as $order)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-gray-900 text-sm">#{{ $order->code }}</span>
                                    <span class="text-gray-400 text-[11px]">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-gray-500 mt-1">
                                    {{ $order->items->count() }} judul buku &bull; Total: <strong class="text-gray-900 font-mono">Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full font-semibold text-[11px] border bg-[#fafaf8] text-gray-800">
                                    {{ $order->statusLabel() }}
                                </span>
                                <a href="{{ route('user.orders.show', $order) }}" class="btn-primary text-xs py-1.5 px-3.5 rounded-lg shadow-sm">
                                    Rincian
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

