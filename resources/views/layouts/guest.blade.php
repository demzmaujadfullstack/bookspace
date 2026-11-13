<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BOOKSPACE') }} — Autentikasi Pembaca</title>

    <!-- Google Fonts: Lora & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-[#1c1c1c] bg-[#fafaf8] min-h-full flex flex-col justify-between">

    {{-- ── Minimal Top Header ──────────────────────────────── --}}
    <header class="py-6 px-6 sm:px-10 border-b border-[#e8e6df] bg-white">
        <div class="flex items-center justify-between px-5 md:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-8 h-8 bg-[#0f2255] rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="font-serif-display font-bold text-xl text-[#0f2255] tracking-tight">BOOKSPACE</span>
            </a>
            <a href="{{ route('home') }}" class="text-xs font-semibold text-gray-500 hover:text-[#0f2255] transition-colors">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </header>

    {{-- ── Main Center Content ─────────────────────────────── --}}
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6">
        <div class="w-full max-w-md">
            {{-- Literary Card Container --}}
            <div class="bg-white border border-[#e8e6df] shadow-sm rounded-2xl p-7 sm:p-9">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- ── Minimal Footer ─────────────────────────────────── --}}
    <footer class="py-6 text-center text-xs text-gray-400 border-t border-[#e8e6df] bg-white">
        &copy; {{ date('Y') }} BOOKSPACE Indonesia. Kurasi Buku &amp; Sastra Pilihan.
    </footer>

</body>
</html>

