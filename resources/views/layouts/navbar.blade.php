@php
    $navCategories = \App\Models\Category::navList();
    $navCartCount = auth()->check() ? (int) (auth()->user()->cart?->items()->sum('quantity') ?? 0) : 0;
    $navWishlistCount = auth()->check() ? count(auth()->user()->wishlistedBookIds()) : 0;
    $navFreeShip = (int) \App\Models\Setting::value('free_shipping_threshold', 200000);
@endphp

<header x-data="{ drawer: false }" class="sticky top-0 z-40 bg-white no-print"
        @keydown.escape.window="drawer = false"
        x-init="$watch('drawer', v => document.body.style.overflow = v ? 'hidden' : '')">

    {{-- ── Strip utilitas ───────────────────────────────────── --}}
    <div class="bg-[#0f2255] text-white">
        <div class="shell flex h-9 items-center justify-between gap-4 text-[12px]">
            <p class="truncate">
                Gratis ongkir di atas Rp{{ number_format($navFreeShip, 0, ',', '.') }}
                <span class="hidden text-white/55 lg:inline">· Buku asli langsung dari penerbit resmi</span>
            </p>
            <div class="flex shrink-0 items-center gap-4">
                <a href="{{ route('help.index') }}" class="text-white/80 transition-colors hover:text-white">Bantuan</a>
                @auth
                    <a href="{{ route('user.orders') }}" class="hidden text-white/80 transition-colors hover:text-white md:inline">Lacak Pesanan</a>
                @else
                    <a href="{{ route('login') }}" class="text-white/80 transition-colors hover:text-white">Masuk</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ── Bar utama ────────────────────────────────────────── --}}
    <div class="border-b border-[#e6e3db]">
        <div class="shell flex items-center gap-3 py-3 md:gap-6">

            <button type="button" @click="drawer = true" aria-label="Buka menu navigasi"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-[#0f2255] transition-colors hover:bg-[#f4f2ec] lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/>
                </svg>
            </button>

            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-[#0f2255] text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </span>
                <span class="leading-none">
                    <span class="block font-serif-display text-[19px] font-bold tracking-tight text-[#0f2255]">BOOKSPACE</span>
                    <span class="mt-1 hidden text-[10px] uppercase tracking-[0.18em] text-[#8b8578] sm:block">Toko Buku Online</span>
                </span>
            </a>

            <div class="mx-auto hidden w-full max-w-2xl md:block">
                @include('components.site-search')
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-1 md:ml-0">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="relative hidden h-10 items-center gap-2 rounded-lg px-3 text-[13px] font-semibold text-[#3f4451] transition-colors hover:bg-[#f4f2ec] hover:text-[#0f2255] sm:flex" title="Buku favorit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="hidden xl:inline">Favorit</span>
                        @if($navWishlistCount > 0)
                            <span class="grid h-5 min-w-[20px] place-items-center rounded-full bg-[#e05c1a] px-1 text-[11px] font-bold text-white">{{ $navWishlistCount > 99 ? '99+' : $navWishlistCount }}</span>
                        @endif
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="relative flex h-10 items-center gap-2 rounded-lg px-3 text-[13px] font-semibold text-[#3f4451] transition-colors hover:bg-[#f4f2ec] hover:text-[#0f2255]" title="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    @if($navCartCount > 0)
                        <span class="grid h-5 min-w-[20px] place-items-center rounded-full bg-[#e05c1a] px-1 text-[11px] font-bold text-white">{{ $navCartCount > 99 ? '99+' : $navCartCount }}</span>
                    @endif
                </a>

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="flex h-10 items-center gap-2 rounded-lg px-2 text-[13px] font-semibold text-[#3f4451] transition-colors hover:bg-[#f4f2ec] hover:text-[#0f2255]">
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-[#0f2255] text-[12px] font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden max-w-[7rem] truncate lg:inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#9a9484]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 top-full z-50 mt-2 w-60 overflow-hidden rounded-xl border border-[#e6e3db] bg-white py-1 shadow-2xl">
                            <div class="border-b border-[#f0eee6] px-4 py-3">
                                <p class="truncate text-[13px] font-bold text-[#0f2255]">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[12px] text-[#6b6f78]">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="border-b border-[#f0eee6] px-4 py-2.5 text-[13px] font-semibold text-[#e05c1a] hover:bg-[#faf5ee]">Dasbor Admin</a>
                            @endif
                            <a href="{{ route('user.dashboard') }}" class="block px-4 py-2.5 text-[13px] text-[#3f4451] hover:bg-[#fafaf8]">Ringkasan Akun</a>
                            <a href="{{ route('user.orders') }}" class="block px-4 py-2.5 text-[13px] text-[#3f4451] hover:bg-[#fafaf8]">Pesanan Saya</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2.5 text-[13px] text-[#3f4451] hover:bg-[#fafaf8]">Buku Favorit</a>
                            <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2.5 text-[13px] text-[#3f4451] hover:bg-[#fafaf8]">Profil &amp; Alamat</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-[#f0eee6]">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 text-left text-[13px] font-semibold text-[#b42318] hover:bg-[#fdf2f2]">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden h-10 items-center rounded-lg px-3 text-[13px] font-semibold text-[#3f4451] transition-colors hover:bg-[#f4f2ec] hover:text-[#0f2255] sm:flex">Masuk</a>
                    <a href="{{ route('register') }}" class="hidden h-10 items-center rounded-lg bg-[#0f2255] px-4 text-[13px] font-semibold text-white transition-colors hover:bg-[#1a3380] sm:flex">Daftar</a>
                @endauth
            </div>
        </div>

        {{-- Pencarian mobile: selalu terlihat, tanpa toggle --}}
        <div class="shell pb-3 md:hidden">
            @include('components.site-search')
        </div>
    </div>

    {{-- ── Navigasi kategori (desktop) ──────────────────────── --}}
    <nav class="hidden border-b border-[#e6e3db] bg-white lg:block">
        <div class="shell flex items-center">
            @php
                $onCatalog = request()->routeIs('books.index');
                $navLinks = [
                    ['label' => 'Beranda', 'url' => route('home'), 'on' => request()->routeIs('home')],
                    ['label' => 'Semua Buku', 'url' => route('books.index'), 'on' => $onCatalog && !request('promo') && !request('sort') && !request('category')],
                    ['label' => 'Rilis Terbaru', 'url' => route('books.index', ['sort' => 'newest']), 'on' => $onCatalog && request('sort') === 'newest'],
                    ['label' => 'Terlaris', 'url' => route('books.index', ['sort' => 'bestseller']), 'on' => $onCatalog && request('sort') === 'bestseller'],
                ];
            @endphp
            @foreach($navLinks as $link)
                <a href="{{ $link['url'] }}" class="border-b-2 px-3.5 py-2.5 text-[13px] font-semibold transition-colors {{ $link['on'] ? 'border-[#0f2255] text-[#0f2255]' : 'border-transparent text-[#3f4451] hover:text-[#0f2255]' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @if($navCategories->isNotEmpty())
                <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open" class="flex items-center gap-1.5 border-b-2 border-transparent px-3.5 py-2.5 text-[13px] font-semibold text-[#3f4451] transition-colors hover:text-[#0f2255]">
                        Kategori
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute left-0 top-full z-50 w-[760px] overflow-hidden rounded-b-xl border border-[#e6e3db] bg-white shadow-2xl" style="display: none">
                        <div class="grid grid-cols-4 divide-x divide-[#f0eee6]">
                            <div class="col-span-3 p-2">
                                <p class="eyebrow px-3.5 pb-1 pt-2.5">Rak Katalog</p>
                                <div class="grid grid-cols-2 gap-x-2">
                                    @foreach($navCategories as $cat)
                                        <a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between gap-3 rounded-lg px-3.5 py-2 text-[13px] text-[#3f4451] transition-colors hover:bg-[#fafaf8] hover:text-[#0f2255]">
                                            <span class="truncate">{{ $cat->name }}</span>
                                            <span class="shrink-0 text-[11px] text-[#9a9484]">{{ $cat->books_count }} judul</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="bg-[#fafaf8] p-4">
                                <p class="eyebrow mb-3">Kurasi Redaksi</p>
                                <ul class="space-y-2.5 text-[13px] font-semibold text-[#0f2255]">
                                    <li><a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="hover:text-[#e05c1a]">Paling banyak terjual</a></li>
                                    <li><a href="{{ route('books.index', ['promo' => 1]) }}" class="hover:text-[#e05c1a]">Buku sedang diskon</a></li>
                                    <li><a href="{{ route('books.index', ['sort' => 'price_asc']) }}" class="hover:text-[#e05c1a]">Harga paling terjangkau</a></li>
                                    <li><a href="{{ route('books.index') }}" class="hover:text-[#e05c1a]">Katalog lengkap</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('books.index', ['promo' => 1]) }}" class="flex items-center gap-2 border-b-2 border-transparent px-3.5 py-2.5 text-[13px] font-semibold text-[#e05c1a] transition-colors hover:text-[#c54e12]">
                Promo
                <span class="rounded bg-[#e05c1a] px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Hemat</span>
            </a>

            <div class="ml-auto flex items-center">
                @auth
                    <a href="{{ route('user.orders') }}" class="border-b-2 px-3.5 py-2.5 text-[13px] font-semibold {{ request()->routeIs('user.orders*') ? 'border-[#0f2255] text-[#0f2255]' : 'border-transparent text-[#3f4451] hover:text-[#0f2255]' }}">Pesanan Saya</a>
                @endauth
                <a href="{{ route('help.index') }}" class="border-b-2 px-3.5 py-2.5 text-[13px] font-semibold {{ request()->routeIs('help.*') ? 'border-[#0f2255] text-[#0f2255]' : 'border-transparent text-[#3f4451] hover:text-[#0f2255]' }}">Bantuan</a>
            </div>
        </div>
    </nav>

    {{-- ── Drawer navigasi (mobile) ─────────────────────────── --}}
    <div x-show="drawer" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div x-show="drawer" x-transition.opacity class="absolute inset-0 bg-[#0b1736]/50" @click="drawer = false"></div>
        <div x-show="drawer"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="absolute inset-y-0 left-0 flex w-[86%] max-w-[360px] flex-col bg-white shadow-2xl">

            <div class="flex h-14 shrink-0 items-center justify-between border-b border-[#e6e3db] px-4">
                <span class="font-serif-display text-[17px] font-bold text-[#0f2255]">BOOKSPACE</span>
                <button type="button" @click="drawer = false" aria-label="Tutup menu" class="grid h-9 w-9 place-items-center rounded-lg text-[#3f4451] hover:bg-[#f4f2ec]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto pb-6">
                @auth
                    <div class="flex items-center gap-3 border-b border-[#f0eee6] bg-[#fafaf8] px-4 py-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[15px] font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="block truncate text-[13px] font-bold text-[#0f2255]">{{ auth()->user()->name }}</span>
                            <a href="{{ route('user.profile.edit') }}" class="block truncate text-[12px] text-[#6b6f78] hover:text-[#0f2255] hover:underline">Kelola profil &amp; alamat</a>
                        </span>
                    </div>
                @else
                    <div class="border-b border-[#f0eee6] bg-[#fafaf8] px-4 py-4">
                        <p class="mb-2.5 text-[13px] font-semibold text-[#0f2255]">Masuk untuk checkout lebih cepat</p>
                        <div class="flex gap-2">
                            <a href="{{ route('login') }}" class="btn-ghost btn-sm flex-1">Masuk</a>
                            <a href="{{ route('register') }}" class="flex-1 rounded-lg bg-[#0f2255] px-3 py-1.5 text-center text-[13px] font-semibold text-white">Daftar</a>
                        </div>
                    </div>
                @endauth

                <nav class="px-2 py-2 text-[14px]">
                    <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Beranda</a>
                    <a href="{{ route('books.index') }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Semua Buku</a>
                    <a href="{{ route('books.index', ['sort' => 'newest']) }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Rilis Terbaru</a>
                    <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Terlaris</a>
                    <a href="{{ route('books.index', ['promo' => 1]) }}" class="block rounded-lg px-3 py-2.5 font-semibold text-[#e05c1a] hover:bg-[#faf5ee]">Promo &amp; Diskon</a>
                    <a href="{{ route('help.index') }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Bantuan</a>
                </nav>

                @if($navCategories->isNotEmpty())
                    <div class="border-t border-[#f0eee6] px-4 py-4">
                        <p class="eyebrow mb-3">Kategori</p>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach($navCategories as $cat)
                                <a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between gap-2 rounded-lg border border-[#e6e3db] px-2.5 py-2 text-[12px] font-medium text-[#3f4451] hover:border-[#0f2255] hover:text-[#0f2255]">
                                    <span class="truncate">{{ $cat->name }}</span>
                                    <span class="text-[11px] text-[#9a9484]">{{ $cat->books_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @auth
                    <nav class="border-t border-[#f0eee6] px-2 py-2 text-[14px]">
                        <a href="{{ route('user.orders') }}" class="block rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">Pesanan Saya</a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">
                            Buku Favorit
                            @if($navWishlistCount > 0)<span class="pill bg-[#f4f2ec] text-[#0f2255]">{{ $navWishlistCount }}</span>@endif
                        </a>
                        <a href="{{ route('cart.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium text-[#3f4451] hover:bg-[#fafaf8] hover:text-[#0f2255]">
                            Keranjang
                            @if($navCartCount > 0)<span class="pill bg-[#f4f2ec] text-[#0f2255]">{{ $navCartCount }}</span>@endif
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-3 py-2.5 text-left font-medium text-[#b42318] hover:bg-[#fdf2f2]">Keluar</button>
                        </form>
                    </nav>
                @endauth
            </div>
        </div>
    </div>
</header>
