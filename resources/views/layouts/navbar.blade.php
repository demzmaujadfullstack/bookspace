{{-- ════════════════════════════════════════════════════════
     BOOKSPACE NAVBAR  —  Waterstones / B&N editorial style
     ════════════════════════════════════════════════════════ --}}
<header x-data="{ mobileOpen: false, searchOpen: false, categoryOpen: false }"
        class="sticky top-0 z-40 bg-white shadow-sm"
        @click.away="categoryOpen = false">

    {{-- ── Promotional top strip ──────────────────────────── --}}
    <div class="bg-[#0f2255] text-white text-[11px] font-medium tracking-wide py-1.5 hidden md:block">
        <div class="px-5 md:px-8 flex justify-between items-center">
            <span>📚 &nbsp;<strong>BOOKSPACE</strong> &nbsp;—&nbsp; Gratis ongkir untuk pembelian di atas <strong>Rp200.000</strong></span>
            <div class="flex items-center gap-5">
                <a href="{{ route('help.index') }}" class="hover:text-orange-300 transition-colors">Bantuan</a>
                @auth
                    <span class="text-white/50">|</span>
                    <span class="text-white/80">Halo, <strong class="text-white">{{ explode(' ', auth()->user()->name)[0] }}</strong></span>
                @endauth
            </div>
        </div>
    </div>

    {{-- ── Main header: Logo Kiri + Search Tengah + Actions Kanan ───────────── --}}
    <div class="border-b border-gray-100">
        <div class="px-5 md:px-8 py-3 flex items-center justify-between gap-4">

            {{-- Logo BOOKSPACE (Identitas Web di Kiri Atas) --}}
            <div class="flex items-center shrink-0 md:w-56">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 bg-[#0f2255] rounded-lg flex items-center justify-center group-hover:bg-[#1a3380] transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="leading-none">
                        <span class="font-serif-display font-bold text-xl text-[#0f2255] tracking-tight block">BOOKSPACE</span>
                        <span class="text-[9px] text-gray-400 tracking-widest uppercase font-medium">Toko Buku Online</span>
                    </div>
                </a>
            </div>

            {{-- Search Bar (desktop) Centered in the middle --}}
            <div class="flex-1 hidden md:block max-w-2xl relative mx-auto" x-data="{
                query: '{{ request('search') }}',
                results: [],
                loading: false,
                open: false,
                debounceTimer: null,
                search() {
                    clearTimeout(this.debounceTimer);
                    if (this.query.trim().length < 2) {
                        this.results = [];
                        this.open = false;
                        return;
                    }
                    this.loading = true;
                    this.open = true;
                    this.debounceTimer = setTimeout(() => {
                        fetch(`{{ route('books.search.suggest') }}?q=${encodeURIComponent(this.query)}`)
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.loading = false;
                            })
                            .catch(() => {
                                this.loading = false;
                            });
                    }, 220);
                }
            }" @click.away="open = false" @keydown.escape.window="open = false">
                <form action="{{ route('books.index') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="search"
                        x-model="query"
                        @input="search()"
                        @focus="if (query.trim().length >= 2) open = true"
                        placeholder="Cari judul buku, penulis, atau ISBN..."
                        class="w-full pl-5 pr-14 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-[#0f2255] transition-colors bg-gray-50 placeholder-gray-400"
                        autocomplete="off"
                    >
                    <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-[#0f2255] hover:bg-[#1a3380] text-white rounded-r-lg transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </button>
                </form>

                {{-- Live Search Dropdown Panel --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 max-h-[440px] flex flex-col"
                    style="display: none;"
                >
                    {{-- Loading State --}}
                    <div x-show="loading" class="p-4 text-center text-xs text-gray-500 flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-[#0f2255]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mencari buku...</span>
                    </div>

                    {{-- Results List --}}
                    <div x-show="!loading && results.length > 0" class="overflow-y-auto divide-y divide-gray-100">
                        <template x-for="book in results" :key="book.id">
                            <a :href="book.url" class="flex items-center gap-3.5 p-3 hover:bg-[#fafaf8] transition-colors group">
                                <img :src="book.cover_url" :alt="book.title" class="w-10 h-14 object-cover rounded shadow shrink-0 bg-gray-100 border border-gray-200">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span x-show="book.category" x-text="book.category" class="text-[9px] font-bold uppercase tracking-wider text-[#e05c1a] bg-[#f4f2ec] px-1.5 py-0.5 rounded"></span>
                                        <span x-show="book.has_discount" class="text-[9px] font-bold text-white bg-red-600 px-1.5 py-0.5 rounded" x-text="'-' + book.discount_percent + '%'"></span>
                                    </div>
                                    <h4 class="font-serif font-bold text-xs sm:text-sm text-[#0f2255] group-hover:text-[#e05c1a] truncate transition-colors mt-0.5" x-text="book.title"></h4>
                                    <p class="text-[11px] text-gray-500 italic truncate" x-text="book.author"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-bold text-xs sm:text-sm text-[#0f2255]" x-text="book.price_formatted"></p>
                                    <p x-show="book.has_discount" class="text-[10px] text-gray-400 line-through" x-text="book.original_price_formatted"></p>
                                </div>
                            </a>
                        </template>

                        {{-- Footer Link: See all results --}}
                        <a :href="'{{ route('books.index') }}?search=' + encodeURIComponent(query)" class="block p-2.5 text-center bg-gray-50 hover:bg-gray-100 text-xs font-semibold text-[#0f2255] border-t border-gray-100 transition-colors">
                            Lihat semua hasil untuk "<span x-text="query" class="font-bold"></span>" &rarr;
                        </a>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!loading && results.length === 0 && query.trim().length >= 2" class="p-6 text-center">
                        <p class="text-xs text-gray-500 font-serif">Tidak ditemukan buku untuk "<span x-text="query" class="font-bold text-gray-700"></span>".</p>
                        <a :href="'{{ route('books.index') }}?search=' + encodeURIComponent(query)" class="inline-block mt-2 text-xs text-[#e05c1a] font-semibold hover:underline">
                            Coba cari di seluruh katalog &rarr;
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-1 shrink-0 md:w-56 justify-end">

                {{-- Mobile search --}}
                <button @click="searchOpen = !searchOpen" class="md:hidden p-2.5 text-gray-600 hover:text-[#0f2255] hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </button>

                {{-- Wishlist (auth) --}}
                @auth
                    @php $wishlistCount = auth()->user()->wishlists()->count(); @endphp
                    <a href="{{ route('wishlist.index') }}" class="relative p-2.5 text-gray-600 hover:text-[#0f2255] hover:bg-gray-100 rounded-lg transition-colors hidden sm:flex items-center gap-1.5" title="Buku Favorit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#0f2255] hidden xl:inline">Favorit</span>
                        @if($wishlistCount > 0)
                            <span class="absolute -top-0.5 right-0 bg-[#e05c1a] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $wishlistCount > 9 ? '9+' : $wishlistCount }}</span>
                        @endif
                    </a>
                @endauth

                {{-- Cart --}}
                @auth
                    @php $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0; @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 text-gray-600 hover:text-[#0f2255] hover:bg-gray-100 rounded-lg transition-colors hidden sm:flex items-center gap-1.5" title="Keranjang">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#0f2255]">Keranjang</span>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 right-0 bg-[#e05c1a] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                        @endif
                    </a>
                @endauth

                {{-- Auth / Profile --}}
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm">
                            <div class="w-7 h-7 bg-[#0f2255] rounded-full flex items-center justify-center text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span class="font-medium text-gray-700 hidden sm:block">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-[#e05c1a] hover:bg-orange-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Dashboard Admin
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @endif
                            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Akun Saya
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Daftar Favorit
                            </a>
                            <a href="{{ route('user.orders') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('cart.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors sm:hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>
                                Keranjang
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-[#0f2255] px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors hidden sm:block">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm py-2 px-4 rounded-lg ml-1">Daftar</a>
                @endauth

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2.5 text-gray-600 hover:text-[#0f2255] hover:bg-gray-100 rounded-lg transition-colors ml-1">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile search bar --}}
        <div x-show="searchOpen" x-transition class="md:hidden px-4 pb-3">
            <form action="{{ route('books.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari buku..." class="w-full pl-4 pr-12 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-[#0f2255] bg-gray-50 placeholder-gray-400">
                    <button type="submit" class="absolute right-0 top-0 h-full px-3.5 bg-[#0f2255] text-white rounded-r-lg flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Category Nav (desktop) ─────────────────────────── --}}
    <nav class="hidden lg:block border-b border-gray-100 bg-white">
        <div class="px-5 md:px-8">
            <div class="flex items-center justify-center gap-2 sm:gap-6 h-11">
                <a href="{{ route('home') }}" class="flex items-center px-3 h-full text-sm font-medium text-gray-600 hover:text-[#0f2255] border-b-2 border-transparent hover:border-[#0f2255] transition-all {{ request()->routeIs('home') ? 'text-[#0f2255] border-[#0f2255]' : '' }}">
                    Beranda
                </a>

                {{-- Kategori mega-dropdown --}}
                @php $navCategories = \App\Models\Category::orderBy('name')->get(); @endphp
                <div x-data="{ open: false }" class="relative h-full flex items-center" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center gap-1 px-3 h-full text-sm font-medium text-gray-600 hover:text-[#0f2255] border-b-2 border-transparent hover:border-[#0f2255] transition-all">
                        Kategori
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute top-full left-0 mt-0 w-64 bg-white rounded-b-xl rounded-tr-xl shadow-xl border border-gray-100 py-2 z-50">
                        @foreach($navCategories as $cat)
                            <a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-[#f5f1ea] hover:text-[#0f2255] transition-colors group">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400 group-hover:text-[#e05c1a] transition-colors">{{ $cat->books()->count() }}</span>
                            </a>
                        @endforeach
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <a href="{{ route('books.index') }}" class="flex items-center gap-1 px-4 py-2.5 text-sm font-semibold text-[#e05c1a] hover:bg-orange-50 transition-colors">
                                Semua Kategori
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('books.index') }}" class="flex items-center px-3 h-full text-sm font-medium text-gray-600 hover:text-[#0f2255] border-b-2 border-transparent hover:border-[#0f2255] transition-all {{ request()->routeIs('books.index') && !request('promo') ? 'text-[#0f2255] border-[#0f2255]' : '' }}">
                    Semua Buku
                </a>
                <a href="{{ route('books.index', ['promo' => 1]) }}" class="flex items-center gap-1.5 px-3 h-full text-sm font-medium text-gray-600 hover:text-[#0f2255] border-b-2 border-transparent hover:border-[#e05c1a] transition-all {{ request('promo') ? 'text-[#e05c1a] border-[#e05c1a]' : '' }}">
                    Promo
                    <span class="bg-[#e05c1a] text-white text-[9px] px-1.5 py-0.5 rounded font-bold tracking-wide">DISKON</span>
                </a>
                <a href="{{ route('help.index') }}" class="flex items-center px-3 h-full text-sm font-medium text-gray-600 hover:text-[#0f2255] border-b-2 border-transparent hover:border-[#0f2255] transition-all">
                    Bantuan
                </a>
            </div>
        </div>
    </nav>

    {{-- ── Mobile Menu ─────────────────────────────────────── --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="lg:hidden border-t border-gray-100 bg-white shadow-lg">
        <div class="px-5 md:px-8 py-3 space-y-0.5">
            <a href="{{ route('home') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">Beranda</a>
            <a href="{{ route('books.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">Semua Buku</a>
            <a href="{{ route('books.index', ['promo' => 1]) }}" class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">
                Promo &amp; Diskon
                <span class="bg-[#e05c1a] text-white text-[9px] px-1.5 py-0.5 rounded font-bold">HOT</span>
            </a>
            <a href="{{ route('help.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">Bantuan</a>
            @auth
                <a href="{{ route('wishlist.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">Daftar Buku Favorit</a>
            @endauth

            @if($navCategories->isNotEmpty())
            <div class="border-t border-gray-100 mt-2 pt-2">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold px-3 mb-2">Kategori</p>
                @foreach($navCategories as $cat)
                    <a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-[#0f2255] hover:bg-gray-50 rounded-lg transition-colors">{{ $cat->name }}</a>
                @endforeach
            </div>
            @endif

            @guest
            <div class="border-t border-gray-100 mt-2 pt-2 flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 text-center bg-[#0f2255] text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-[#1a3380] transition-colors">Daftar</a>
            </div>
            @endguest
        </div>
    </div>
</header>


