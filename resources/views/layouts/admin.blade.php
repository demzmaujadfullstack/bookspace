<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — {{ \App\Models\Setting::siteName() }} Admin</title>
    <x-brand-favicon />

    <!-- Google Fonts: Lora + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f6f5f1] text-gray-800 antialiased font-sans">
<div class="min-h-screen flex" x-data="{ sidebarOpen: false }">

    {{-- ── SIDEBAR OVERLAY FOR MOBILE ────────────────────────────── --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    {{-- ── SIDEBAR ──────────────────────────────────────────────── --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 left-0 z-50 h-screen w-64 bg-[#09142e] text-white flex flex-col transition-transform duration-200 ease-in-out shrink-0 border-r border-white/5"
    >
        {{-- Brand / Logo --}}
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <x-brand-logo tone="admin" />
            <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white p-1">✕</button>
        </div>

        {{-- Navigation Menus --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1 text-xs">
            <p class="text-white/30 uppercase tracking-widest text-[9px] font-bold px-3 py-1.5">Ringkasan</p>

            {{-- 1. Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard Utama
            </a>

            <p class="text-white/30 uppercase tracking-widest text-[9px] font-bold px-3 py-1.5 mt-3">Katalog &amp; Koleksi</p>

            {{-- 2. Manajemen Buku --}}
            <a href="{{ route('admin.books.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.books.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Katalog Buku
            </a>

            {{-- 3. Kategori Buku --}}
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Genre &amp; Kategori
            </a>

            {{-- 4. Manajemen Stok --}}
            <a href="{{ route('admin.stock.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.stock.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Manajemen Stok
            </a>

            <p class="text-white/30 uppercase tracking-widest text-[9px] font-bold px-3 py-1.5 mt-3">Transaksi Penjualan</p>

            {{-- 5. Transaksi --}}
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Pesanan &amp; Verifikasi</span>
                </div>
                @php
                    $pendingCount = \App\Models\Order::whereIn('status', [\App\Models\Order::MENUNGGU_VERIFIKASI, \App\Models\Order::MENUNGGU_PEMBAYARAN])->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="bg-[#e05c1a] text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $pendingCount }}</span>
                @endif
            </a>

            {{-- 6. Laporan Penjualan --}}
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan &amp; Analitik
            </a>

            <p class="text-white/30 uppercase tracking-widest text-[9px] font-bold px-3 py-1.5 mt-3">Etalase &amp; Pengguna</p>

            {{-- 7. Carousel Banner --}}
            <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.banners.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Banner Etalase
            </a>

            {{-- 8. Live Chat Admin --}}
            <a href="{{ route('admin.chat.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.chat.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>Layanan Pelanggan</span>
                </div>
                @php
                    $openChats = \App\Models\Conversation::where('status', 'open')->count();
                    $unreadMessages = \App\Models\Message::unreadForAdmins()->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="flex items-center gap-1">
                        <span class="bg-[#e05c1a] text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $unreadMessages }}</span>
                    </span>
                @elseif($openChats > 0)
                    <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $openChats }}</span>
                @endif
            </a>

            {{-- 9. Manajemen User --}}
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Kelola Pengguna
            </a>

            {{-- 10. Pengaturan Website --}}
            <a href="{{ route('admin.settings.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-[#0f2255] text-white border-l-4 border-[#e05c1a] shadow-sm' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Sistem
            </a>
        </nav>

        {{-- Footer user info & logout --}}
        <div class="p-4 border-t border-white/10 text-xs">
            <div class="flex items-center justify-between mb-3">
                <div class="min-w-0">
                    <p class="font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-white/50">Administrator USK</p>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="text-white/60 hover:text-white p-1 rounded" title="Buka Halaman Toko">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-white/10 hover:bg-red-600/80 text-white/90 hover:text-white py-2 rounded-xl transition-colors font-medium flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar Admin
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT WRAPPER ────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-[#e8e6df] sticky top-0 z-30 px-4 lg:px-8 py-3.5 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-serif-display font-bold text-lg md:text-xl text-[#0f2255]">@yield('page_title', 'Dashboard')</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="text-xs bg-[#f4f2ec] hover:bg-[#ebe7de] text-[#0f2255] border border-[#dcd8cd] px-3.5 py-1.5 rounded-lg font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat Toko Publik
                </a>
            </div>
        </header>

        {{-- Toast alerts --}}
        <main class="flex-1 p-4 lg:p-8">
            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>
