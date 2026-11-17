@props(['book'])

@php
    $rating = $book->averageRating();
    $reviewCount = $book->reviews_count ?? $book->reviews()->count();
@endphp

<article class="book-card group relative flex h-full flex-col overflow-hidden rounded-lg border border-[#e6e3db] bg-white transition-all duration-200 hover:-translate-y-0.5 hover:border-[#0f2255]/30 hover:shadow-md">

    {{-- ── Sampul ─────────────────────────────────────────────── --}}
    <div class="relative bg-[#0f2255]">
        <a href="{{ route('books.show', $book->slug) }}" class="block aspect-[2/3] w-full overflow-hidden" tabindex="-1" aria-hidden="true">
            <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}"
                class="book-cover h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
                onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'" loading="lazy">
        </a>

        <div class="pointer-events-none absolute inset-y-0 left-0 w-3 bg-gradient-to-r from-black/35 to-transparent"></div>

        <div class="absolute right-2 top-2 z-10 flex flex-col items-end gap-1">
            @if($book->hasDiscount())
                <span class="rounded bg-[#e05c1a] px-1.5 py-0.5 text-[10px] font-bold tracking-wide text-white shadow-sm">-{{ $book->discount_percent }}%</span>
            @endif
            @if($book->stock === 0)
                <span class="rounded bg-[#3f4451]/90 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm">Habis</span>
            @elseif($book->stock <= \App\Models\Book::STOCK_LOW_THRESHOLD)
                <span class="rounded bg-[#8a3d0c]/90 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm">Sisa {{ $book->stock }}</span>
            @endif
        </div>

        @auth
            <form method="POST" action="{{ route('wishlist.toggle', $book) }}" class="absolute left-2 top-2 z-20">
                @csrf
                <button type="submit"
                    class="grid h-7 w-7 place-items-center rounded-full bg-black/35 text-white backdrop-blur-sm transition-colors hover:bg-black/55 hover:text-[#ff6b4a]
                        {{ auth()->user()->hasWishlisted($book->id) ? 'text-[#ff6b4a]' : 'opacity-0 group-hover:opacity-100' }}"
                    title="{{ auth()->user()->hasWishlisted($book->id) ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="{{ auth()->user()->hasWishlisted($book->id) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </form>
        @endauth
    </div>

    {{-- ── Informasi & aksi ────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col p-3">
        <p class="truncate text-[10px] font-semibold uppercase tracking-wider text-[#e05c1a]">
            {{ $book->category->name ?? 'Koleksi' }}
            @if($book->year)
                <span class="ml-1 font-normal normal-case tracking-normal text-[#9a9484]">{{ $book->year }}</span>
            @endif
        </p>

        <h3 class="mt-1 font-serif-display text-[13px] font-bold leading-snug text-[#17181c]">
            <a href="{{ route('books.show', $book->slug) }}" class="line-clamp-2 transition-colors group-hover:text-[#0f2255]" title="{{ $book->title }}">
                {{ $book->title }}
            </a>
        </h3>

        <p class="mt-0.5 truncate text-[11px] italic text-[#6b6f78]">{{ $book->author }}</p>

        <div class="mt-1.5 flex items-center gap-1 text-[11px]">
            @if($rating > 0)
                <span class="flex items-center gap-px" aria-hidden="true">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="h-3 w-3 {{ $i <= round($rating) ? 'fill-[#e0a80c] stroke-none' : 'fill-none stroke-[#dcd8cd]' }}" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l1.519-4.674z"/>
                        </svg>
                    @endfor
                </span>
                <span class="font-semibold text-[#3f4451]">{{ number_format($rating, 1) }}</span>
                <span class="text-[#9a9484]">({{ $reviewCount }})</span>
            @else
                <span class="text-[#9a9484]">Belum ada ulasan</span>
            @endif
        </div>

        <div class="mt-auto pt-2.5">
            <div class="flex flex-wrap items-baseline gap-x-1.5">
                @if($book->hasDiscount())
                    <span class="font-serif-display text-[14px] font-bold text-[#e05c1a]">Rp{{ number_format($book->finalPrice(), 0, ',', '.') }}</span>
                    <span class="text-[11px] text-[#9a9484] line-through">Rp{{ number_format($book->price, 0, ',', '.') }}</span>
                @else
                    <span class="font-serif-display text-[14px] font-bold text-[#0f2255]">Rp{{ number_format($book->price, 0, ',', '.') }}</span>
                @endif
            </div>

            @if($book->stock > 0)
                @auth
                    <form method="POST" action="{{ route('books.cart.add', $book) }}" class="mt-2">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                            class="flex h-8 w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#0f2255] text-[11px] font-semibold text-white transition-colors hover:bg-[#e05c1a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="mt-2 flex h-8 w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#0f2255] text-[11px] font-semibold text-white transition-colors hover:bg-[#e05c1a]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Masuk untuk Membeli</span>
                    </a>
                @endauth
            @else
                <button type="button" disabled
                    class="mt-2 flex h-8 w-full cursor-not-allowed items-center justify-center rounded-lg border border-dashed border-[#dcd8cd] bg-[#fafaf8] text-[12px] font-semibold text-[#9a9484]">
                    Stok Habis
                </button>
            @endif
        </div>
    </div>
</article>
