@props(['book'])

<div class="book-card group relative bg-white border border-[#e8e6df] hover:border-[#0f2255]/30 rounded-xl overflow-hidden flex flex-col h-full transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">

    {{-- ── Book Cover Section: edge-to-edge, no padding ───────── --}}
    <div class="relative overflow-hidden">
        {{-- Wishlist toggle — top-left overlay on the cover --}}
        @auth
            @php $isCardWishlisted = auth()->user()->wishlists()->where('book_id', $book->id)->exists(); @endphp
            <form method="POST" action="{{ route('wishlist.toggle', $book) }}" class="absolute top-2.5 left-2.5 z-20">
                @csrf
                <button
                    type="submit"
                    class="w-7 h-7 rounded-full bg-black/40 hover:bg-black/60 backdrop-blur-sm flex items-center justify-center transition-all duration-200 {{ $isCardWishlisted ? 'text-red-400' : 'text-white/80 hover:text-red-400 opacity-0 group-hover:opacity-100' }}"
                    title="{{ $isCardWishlisted ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="{{ $isCardWishlisted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </form>
        @endauth

        {{-- Top Right Badges --}}
        <div class="absolute top-2.5 right-2.5 z-10 flex flex-col items-end gap-1">
            @if($book->hasDiscount())
                <span class="bg-[#e05c1a] text-white text-[10px] font-bold px-2 py-0.5 rounded shadow tracking-wide">
                    -{{ $book->discount_percent }}%
                </span>
            @endif
            @if($book->stock === 0)
                <span class="bg-gray-800/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded shadow backdrop-blur-sm">
                    Habis
                </span>
            @elseif($book->stock <= \App\Models\Book::STOCK_LOW_THRESHOLD)
                <span class="bg-amber-600/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded shadow backdrop-blur-sm">
                    Sisa {{ $book->stock }}
                </span>
            @endif
        </div>

        {{-- Direct edge-to-edge cover image, aspect ratio 2:3 (book proportion) --}}
        <a href="{{ route('books.show', $book->slug) }}" class="block aspect-[2/3] w-full overflow-hidden bg-[#0f2255]">
            <img
                src="{{ $book->coverUrl() }}"
                alt="{{ $book->title }}"
                class="book-cover w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'"
                loading="lazy"
            >
            {{-- Spine shadow — realistic book depth --}}
            <div class="absolute inset-y-0 left-0 w-4 bg-gradient-to-r from-black/30 via-black/10 to-transparent pointer-events-none"></div>
        </a>
    </div>

    {{-- ── Book Details Section ─────────────────────────── --}}
    <div class="p-3.5 flex flex-col flex-1 justify-between bg-white">
        <div>
            {{-- Category / Genre --}}
            <div class="flex items-center justify-between text-[10px] text-[#e05c1a] font-semibold tracking-wider uppercase mb-1">
                <span>{{ $book->category->name ?? 'Koleksi' }}</span>
                @if($book->year)
                    <span class="text-gray-400 font-normal normal-case">{{ $book->year }}</span>
                @endif
            </div>

            {{-- Title --}}
            <h3 class="font-serif-display font-bold text-sm text-[#1c1c1c] group-hover:text-[#0f2255] transition-colors leading-snug line-clamp-2 mb-1" title="{{ $book->title }}">
                <a href="{{ route('books.show', $book->slug) }}">
                    {{ $book->title }}
                </a>
            </h3>

            {{-- Author --}}
            <p class="text-[11px] text-gray-500 truncate mb-2 italic">
                {{ $book->author }}
            </p>

            {{-- Star Rating --}}
            @php $rating = $book->averageRating(); @endphp
            <div class="flex items-center gap-1 mb-2.5 text-xs">
                @if($rating > 0)
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-3 w-3 {{ $i <= round($rating) ? 'fill-current' : 'text-gray-200 fill-transparent stroke-current' }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700">{{ number_format($rating, 1) }}</span>
                    <span class="text-[10px] text-gray-400">({{ $book->reviews_count ?? $book->reviews()->count() }})</span>
                @else
                    <span class="text-[11px] text-gray-300">Belum ada ulasan</span>
                @endif
            </div>
        </div>

        {{-- Pricing & CTA Bar --}}
        <div class="pt-2.5 border-t border-[#f0eee6] mt-auto">
            <div class="mb-2.5">
                @if($book->hasDiscount())
                    <div class="flex items-baseline gap-1.5 flex-wrap">
                        <span class="font-serif-display font-bold text-sm text-[#e05c1a]">
                            Rp{{ number_format($book->finalPrice(), 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-gray-400 line-through">
                            Rp{{ number_format($book->price, 0, ',', '.') }}
                        </span>
                    </div>
                @else
                    <span class="font-serif-display font-bold text-sm text-[#0f2255]">
                        Rp{{ number_format($book->price, 0, ',', '.') }}
                    </span>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-5 gap-1.5">
                <a href="{{ route('books.show', $book->slug) }}" class="col-span-4 text-center py-2 px-2 rounded-lg text-xs font-semibold bg-[#f7f5f0] border border-[#dcd8cd] text-[#0f2255] hover:bg-[#0f2255] hover:text-white hover:border-[#0f2255] transition-all">
                    Lihat Rincian
                </a>

                @if($book->stock > 0)
                    @auth
                        <form method="POST" action="{{ route('books.cart.add', $book) }}" class="col-span-1">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full h-full py-2 flex items-center justify-center bg-[#0f2255] hover:bg-[#e05c1a] text-white rounded-lg transition-colors shadow-sm" title="Tambah ke Keranjang">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="col-span-1 py-2 flex items-center justify-center bg-[#0f2255] hover:bg-[#e05c1a] text-white rounded-lg transition-colors shadow-sm" title="Masuk untuk membeli">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </a>
                    @endauth
                @else
                    <button disabled class="col-span-1 py-2 flex items-center justify-center bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed" title="Stok Habis">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
