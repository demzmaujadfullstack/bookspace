{{-- Pencarian langsung dengan saran judul buku — dipakai di bar desktop dan mobile. --}}
@php
    $inputId = 'site-search-' . Str::random(6);
@endphp

<div class="relative w-full" x-data="{
        query: '{{ request('search') }}',
        results: [],
        loading: false,
        open: false,
        timer: null,
        suggest() {
            clearTimeout(this.timer);
            if (this.query.trim().length < 2) {
                this.results = [];
                this.open = false;
                this.loading = false;
                return;
            }
            this.loading = true;
            this.open = true;
            this.timer = setTimeout(() => {
                fetch(`{{ route('books.search.suggest') }}?q=${encodeURIComponent(this.query)}`)
                    .then(res => res.ok ? res.json() : [])
                    .then(data => { this.results = data; this.loading = false; })
                    .catch(() => { this.results = []; this.loading = false; });
            }, 220);
        }
    }"
    @click.away="open = false"
    @keydown.escape.window="open = false"
>
    <form action="{{ route('books.index') }}" method="GET" role="search">
        <label for="{{ $inputId }}" class="sr-only">Cari buku</label>
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#9a9484]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input
                id="{{ $inputId }}"
                type="text"
                name="search"
                x-model="query"
                @input="suggest()"
                @focus="if (query.trim().length >= 2) open = true"
                placeholder="Cari judul, penulis, atau ISBN..."
                class="w-full rounded-lg border border-[#dcd8cd] bg-white py-2.5 pl-11 pr-[4.25rem] text-sm text-[#1c1c1c] placeholder-[#9a9484] transition focus:border-[#0f2255] focus:outline-none focus:ring-2 focus:ring-[#0f2255]/10"
                autocomplete="off"
            >
            <button type="submit" class="absolute right-1 top-1 bottom-1 w-16 rounded-md bg-[#0f2255] text-[13px] font-semibold text-white transition hover:bg-[#1a3380]">
                Cari
            </button>
        </div>
    </form>

    {{-- Panel saran --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-xl border border-[#e6e3db] bg-white shadow-2xl"
        style="display: none"
    >
        <div x-show="loading" class="flex items-center justify-center gap-2.5 p-6 text-[13px] text-[#6b6f78]">
            <svg class="h-4 w-4 animate-spin text-[#0f2255]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            Mencari...
        </div>

        <div x-show="!loading && results.length > 0" class="max-h-[24rem] overflow-y-auto">
            <template x-for="book in results" :key="book.id">
                <a :href="book.url" class="flex items-center gap-3 border-b border-[#f0eee6] p-3 last:border-0 hover:bg-[#fafaf8]">
                    <img :src="book.cover_url" :alt="book.title" onerror="this.src='{{ asset('images/cover-placeholder.svg') }}'" class="h-14 w-10 shrink-0 border border-[#e6e3db] bg-white object-cover">
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-1.5">
                            <span x-show="book.category" x-text="book.category" class="eyebrow text-[#8b8578]"></span>
                            <span x-show="book.has_discount" x-text="'-' + book.discount_percent + '%'" class="rounded bg-[#b42318] px-1.5 py-0.5 text-[10px] font-bold text-white"></span>
                        </span>
                        <span x-text="book.title" class="mt-0.5 block truncate font-serif-display text-[13px] font-semibold text-[#0f2255]"></span>
                        <span x-text="book.author" class="block truncate text-[12px] text-[#6b6f78]"></span>
                    </span>
                    <span class="shrink-0 text-right">
                        <span x-text="book.price_formatted" class="block text-[13px] font-bold text-[#0f2255]"></span>
                        <span x-show="book.has_discount" x-text="book.original_price_formatted" class="block text-[11px] text-[#9a9484] line-through"></span>
                    </span>
                </a>
            </template>
            <a :href="'{{ route('books.index') }}?search=' + encodeURIComponent(query)" class="block bg-[#fafaf8] px-4 py-2.5 text-center text-[12px] font-semibold text-[#0f2255] hover:bg-[#f4f2ec]">
                Lihat semua hasil untuk "<span x-text="query" class="font-bold"></span>"
            </a>
        </div>

        <div x-show="!loading && results.length === 0 && query.trim().length >= 2" class="p-6 text-center">
            <p class="text-[13px] text-[#6b6f78]">Belum ada buku yang cocok dengan "<span x-text="query" class="font-semibold text-[#1c1c1c]"></span>".</p>
        </div>
    </div>
</div>
