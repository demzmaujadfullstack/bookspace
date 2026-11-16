<footer class="bg-[#0b1736] text-white/80 mt-20 border-t border-white/5 font-sans">
    @php $settings = \App\Models\Setting::values(); @endphp

    {{-- ── Curated Newsletter Strip ───────────────────────────── --}}
    <div class="bg-[#12224d] border-b border-white/10">
        <div class="px-5 md:px-8 py-10 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7">
                    <span class="inline-block text-[#e05c1a] font-serif-display italic text-sm mb-1.5">Warta Sastra &amp; Pembaca</span>
                    <h3 class="font-serif-display font-bold text-2xl md:text-3xl text-white tracking-tight leading-snug">
                        Bergabung dengan Lingkar Pembaca BOOKSPACE
                    </h3>
                    <p class="text-white/60 text-sm mt-2 max-w-xl leading-relaxed">
                        Dapatkan ulasan buku kurasi redaksi, informasi peluncuran judul terbaru, serta penawaran diskon buku istimewa setiap pekannya langsung di inbox Anda.
                    </p>
                </div>
                <div class="lg:col-span-5">
                    <form onsubmit="event.preventDefault(); alert('Terima kasih! Anda telah terdaftar dalam buletin BOOKSPACE.');" class="flex flex-col sm:flex-row gap-2.5">
                        <input
                            type="email"
                            required
                            placeholder="Alamat surel / email Anda..."
                            aria-label="Email untuk langganan"
                            class="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/40 text-sm focus:outline-none focus:ring-2 focus:ring-[#e05c1a] focus:bg-white/15 transition-all"
                        >
                        <button type="submit" class="bg-[#e05c1a] hover:bg-[#c54e12] text-white px-6 py-3 rounded-lg text-sm font-semibold tracking-wide transition-colors whitespace-nowrap shadow-md">
                            Langganan
                        </button>
                    </form>
                    <p class="text-[11px] text-white/40 mt-2">Bebas spam. Anda dapat berhenti berlangganan kapan pun.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Footer Grid ───────────────────────────────────── --}}
    <div class="px-5 md:px-8 pt-14 pb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

            {{-- Kolom 1 & 2: Identitas Toko --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-4 group">
                    <div class="w-9 h-9 bg-[#e05c1a] rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="font-serif-display font-bold text-2xl text-white tracking-tight">BOOKSPACE</span>
                </a>
                <p class="text-white/65 text-sm leading-relaxed mb-6 max-w-sm">
                    {{ $settings['footer_description'] ?? 'BOOKSPACE adalah toko buku daring independen yang menyajikan kurasi karya sastra, fiksi, wawasan pemikiran, sains, hingga pengembangan diri untuk memperkaya khazanah literasi pembaca Nusantara.' }}
                </p>

                {{-- Komitmen & Jaminan --}}
                <div class="space-y-2 text-xs text-white/50 border-t border-white/10 pt-4">
                    <div class="flex items-center gap-2">
                        <span class="text-[#e05c1a]">✓</span>
                        <span>100% Buku Original Langsung dari Penerbit</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[#e05c1a]">✓</span>
                        <span>Pengemasan Standar Kolektor (Karton + Bubble Wrap)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[#e05c1a]">✓</span>
                        <span>Layanan Pengiriman Cepat ke Seluruh Indonesia</span>
                    </div>
                </div>
            </div>

            {{-- Kolom 3: Katalog & Jelajah --}}
            <div>
                <h4 class="font-serif-display font-semibold text-white text-base mb-4 tracking-wide">Jelajah Katalog</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('books.index') }}" class="hover:text-white transition-colors">Semua Koleksi Buku</a></li>
                    <li><a href="{{ route('books.index', ['sort' => 'newest']) }}" class="hover:text-white transition-colors">Buku Rilis Terbaru</a></li>
                    <li><a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="hover:text-white transition-colors">Buku Terlaris (Bestsellers)</a></li>
                    <li><a href="{{ route('books.index', ['promo' => 1]) }}" class="hover:text-[#e05c1a] transition-colors flex items-center gap-1.5">
                        <span>Pilihan Diskon &amp; Promo</span>
                        <span class="bg-[#e05c1a]/30 text-[#e05c1a] text-[10px] font-bold px-1.5 py-0.2 rounded">Hemat</span>
                    </a></li>
                    @foreach(\App\Models\Category::take(4)->get() as $c)
                        <li><a href="{{ route('books.index', ['category' => $c->slug]) }}" class="hover:text-white transition-colors">{{ $c->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Kolom 4: Bantuan & Akun --}}
            <div>
                <h4 class="font-serif-display font-semibold text-white text-base mb-4 tracking-wide">Layanan Pelanggan</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">Pusat Bantuan &amp; FAQ</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">Hubungi Layanan Buku</a></li>
                    @auth
                        <li><a href="{{ route('user.dashboard') }}" class="hover:text-white transition-colors">Dashboard Akun</a></li>
                        <li><a href="{{ route('user.orders') }}" class="hover:text-white transition-colors">Lacak Pesanan Saya</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-white transition-colors">Keranjang Belanja</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk ke Akun</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Registrasi Pembaca Baru</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Kolom 5: Kontak & Jam Buka --}}
            <div>
                <h4 class="font-serif-display font-semibold text-white text-base mb-4 tracking-wide">Informasi Kontak</h4>
                <ul class="space-y-3 text-sm text-white/70">
                    @if($settings['site_address'] ?? null)
                        <li class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#e05c1a] shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $settings['site_address'] }}</span>
                        </li>
                    @endif
                    @if($settings['site_email'] ?? null)
                        <li class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#e05c1a] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $settings['site_email'] }}</span>
                        </li>
                    @endif
                    @if($settings['site_whatsapp'] ?? null)
                        <li class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span>{{ $settings['site_phone'] ?? $settings['site_whatsapp'] }}</span>
                        </li>
                    @endif
                </ul>

                @if($settings['site_whatsapp'] ?? null)
                    <a href="https://wa.me/{{ $settings['site_whatsapp'] }}" target="_blank" class="mt-4 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wide transition-colors">
                        Konsultasi Buku via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Sub Footer & Copyright ─────────────────────────────── --}}
    <div class="border-t border-white/10 bg-[#081129]">
        <div class="px-5 md:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-white/50">
            <p>&copy; {{ date('Y') }} BOOKSPACE. Seluruh Hak Cipta Dilindungi. Proyek Uji Kompetensi Keahlian (USK).</p>
            <div class="flex items-center gap-6">
                <span>Metode Pembayaran: Bank Mandiri, BCA, BRI, QRIS &amp; COD</span>
                <span class="text-white/20">|</span>
                <span>Standar Mutu Buku Gramedia &amp; Penerbit Resmi</span>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-white/60">
                <a href="{{ route('pages.about') }}" class="hover:text-white transition-colors font-medium">Tentang Kami</a>
                <a href="{{ route('pages.privacy') }}" class="hover:text-white transition-colors font-medium">Kebijakan Privasi</a>
                <a href="{{ route('pages.terms') }}" class="hover:text-white transition-colors font-medium">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>


