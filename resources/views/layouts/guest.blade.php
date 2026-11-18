<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::siteName() }} — Autentikasi Pembaca</title>
    <x-brand-favicon />

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
            <x-brand-logo tone="guest" />
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

