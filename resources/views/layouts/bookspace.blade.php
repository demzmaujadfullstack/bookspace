<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $brandName = \App\Models\Setting::siteName(); @endphp
    <title>@yield('title', 'Beranda') — {{ $brandName }}</title>
    <meta name="description" content="@yield('meta_description', 'BOOKSPACE — Toko buku online terpercaya dengan ribuan koleksi sastra, fiksi, nonfiksi, dan akademik.')">
    <x-brand-favicon />

    <!-- Google Fonts: Lora (Editorial Serif) & Inter (Modern Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#fafaf8] text-[#1c1c1c] font-sans antialiased selection:bg-[#0f2255] selection:text-white flex flex-col min-h-screen">

    {{-- NAVBAR --}}
    @include('layouts.navbar')

    {{-- FLASH MESSAGES & MAIN CONTENT --}}
    <main class="flex-grow">
        @if (session('success'))
            <div id="toast-success" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-[#0f2255] text-white px-5 py-3.5 rounded-xl shadow-2xl border border-white/10 transition-all duration-300">
                <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="text-sm font-medium pr-2">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-xs">✕</button>
            </div>
        @endif
        @if (session('error'))
            <div id="toast-error" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-[#8b1c1c] text-white px-5 py-3.5 rounded-xl shadow-2xl border border-white/10 transition-all duration-300">
                <span class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
                <span class="text-sm font-medium pr-2">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-white/60 hover:text-white text-xs">✕</button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('layouts.footer')

    {{-- Auto-dismiss toasts --}}
    <script>
        setTimeout(() => {
            ['toast-success', 'toast-error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(10px)';
                    setTimeout(() => el.remove(), 300);
                }
            });
        }, 4500);
    </script>

    @stack('scripts')
</body>
</html>
