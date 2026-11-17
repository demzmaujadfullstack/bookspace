<footer class="mt-16 bg-[#0b1736] text-white/75 no-print">
    @php $settings = \App\Models\Setting::values(); @endphp

    {{-- ── Buletin ──────────────────────────────────────────── --}}
    <div class="border-b border-white/10 bg-[#12224d]">
        <div class="shell py-8 md:py-10">
            <div class="grid items-center gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="eyebrow text-[#f0a473]">Warta Buku Mingguan</p>
                    <h3 class="mt-1.5 font-serif-display text-xl font-bold leading-snug text-white sm:text-2xl">
                        Kurasi judul baru dan promo dari redaksi BOOKSPACE, langsung ke email Anda
                    </h3>
                </div>
                <div class="lg:col-span-5">
                    <form onsubmit="event.preventDefault(); alert('Terima kasih! Email Anda telah terdaftar di buletin BOOKSPACE.');" class="flex flex-col gap-2 sm:flex-row">
                        <label for="newsletter-email" class="sr-only">Email untuk langganan buletin</label>
                        <input id="newsletter-email" type="email" required placeholder="nama@email.com"
                               class="w-full flex-1 rounded-lg border border-white/20 bg-white/10 px-4 py-2.5 text-sm text-white placeholder-white/45 focus:border-[#e05c1a] focus:outline-none focus:ring-2 focus:ring-[#e05c1a]/30">
                        <button type="submit" class="shrink-0 rounded-lg bg-[#e05c1a] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#c54e12]">
                            Langganan
                        </button>
                    </form>
                    <p class="mt-2 text-[12px] text-white/45">Berhenti berlangganan kapan saja. Kami tidak membagikan email Anda.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Grid utama ───────────────────────────────────────── --}}
    <div class="shell py-12">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12">

            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-[#e05c1a] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    <span class="font-serif-display text-xl font-bold tracking-tight text-white">BOOKSPACE</span>
                </a>
                <p class="mt-4 max-w-sm text-[13px] leading-relaxed text-white/60">
                    {{ $settings['footer_description'] ?? 'Toko buku daring dengan kurasi sastra, fiksi, sains, hingga pengembangan diri untuk pembaca di seluruh Indonesia.' }}
                </p>

                <ul class="mt-6 space-y-2.5 border-t border-white/10 pt-5 text-[13px] text-white/65">
                    @foreach([
                        'Buku 100% asli, langsung dari penerbit resmi',
                        'Pengemasan kardus tebal dan pelindung sudut',
                        'Verifikasi pembayaran diproses di hari kerja yang sama',
                    ] as $promise)
                        <li class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-[#e05c1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $promise }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="mb-3.5 text-[13px] font-bold uppercase tracking-[0.12em] text-white">Katalog</h4>
                <ul class="space-y-2.5 text-[13px]">
                    <li><a href="{{ route('books.index') }}" class="hover:text-white">Semua Buku</a></li>
                    <li><a href="{{ route('books.index', ['sort' => 'newest']) }}" class="hover:text-white">Rilis Terbaru</a></li>
                    <li><a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="hover:text-white">Paling Terlaris</a></li>
                    <li><a href="{{ route('books.index', ['promo' => 1]) }}" class="hover:text-white">Promo &amp; Diskon</a></li>
                    @foreach(\App\Models\Category::navList()->take(4) as $cat)
                        <li><a href="{{ route('books.index', ['category' => $cat->slug]) }}" class="hover:text-white">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h4 class="mb-3.5 text-[13px] font-bold uppercase tracking-[0.12em] text-white">Layanan Pelanggan</h4>
                <ul class="space-y-2.5 text-[13px]">
                    <li><a href="{{ route('help.index') }}" class="hover:text-white">Pusat Bantuan &amp; FAQ</a></li>
                    @auth
                        <li><a href="{{ route('user.orders') }}" class="hover:text-white">Lacak Pesanan Saya</a></li>
                        <li><a href="{{ route('user.dashboard') }}" class="hover:text-white">Ringkasan Akun</a></li>
                        <li><a href="{{ route('wishlist.index') }}" class="hover:text-white">Buku Favorit</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Daftar Akun Baru</a></li>
                    @endauth
                    <li><a href="{{ route('pages.about') }}" class="hover:text-white">Tentang Kami</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="hover:text-white">Syarat &amp; Ketentuan</a></li>
                    <li><a href="{{ route('pages.privacy') }}" class="hover:text-white">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h4 class="mb-3.5 text-[13px] font-bold uppercase tracking-[0.12em] text-white">Hubungi Kami</h4>
                <ul class="space-y-3 text-[13px] text-white/65">
                    @if($settings['site_address'] ?? null)
                        <li>{{ $settings['site_address'] }}</li>
                    @endif
                    @if($settings['site_email'] ?? null)
                        <li><a href="mailto:{{ $settings['site_email'] }}" class="hover:text-white">{{ $settings['site_email'] }}</a></li>
                    @endif
                    @if($settings['site_phone'] ?? $settings['site_whatsapp'] ?? null)
                        <li>{{ $settings['site_phone'] ?? $settings['site_whatsapp'] }}</li>
                    @endif
                    <li>Jam operasional: Senin&ndash;Sabtu, 09.00&ndash;18.00 WIB</li>
                </ul>
                @if($settings['site_whatsapp'] ?? null)
                    <a href="https://wa.me/{{ $settings['site_whatsapp'] }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex h-10 items-center gap-2 rounded-lg border border-white/20 px-4 text-[13px] font-semibold text-white transition-colors hover:border-white/40 hover:bg-white/5">
                        Chat via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Sub-footer ───────────────────────────────────────── --}}
    <div class="border-t border-white/10 bg-[#081129]">
        <div class="shell flex flex-col items-center justify-between gap-3 py-5 text-[12px] text-white/50 sm:flex-row">
            <p>&copy; {{ date('Y') }} BOOKSPACE. Hak cipta dilindungi. Proyek uji kompetensi sekolah.</p>
            <p class="text-center sm:text-right">Pembayaran: transfer bank, QRIS &amp; COD &middot; Pengiriman: J&amp;T, JNE, SiCepat, Anteraja</p>
        </div>
    </div>
</footer>
