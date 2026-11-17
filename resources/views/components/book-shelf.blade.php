@props(['books', 'cardWidth' => null])

@php
    $books = collect($books);
    $width = $cardWidth ?? 'w-[172px] sm:w-[200px] lg:w-[220px] xl:w-[240px]';
@endphp

@if($books->isNotEmpty())
    <div class="relative"
         x-data="{ atStart: true, atEnd: false,
                   sync() { const r = $refs.rail; this.atStart = r.scrollLeft <= 4; this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 4; },
                   go(d) { const r = $refs.rail; r.scrollBy({ left: d * r.clientWidth * 0.85, behavior: 'smooth' }); } }"
         x-init="$nextTick(() => sync())"
         @resize.window.throttle.200ms="sync()">

        <div x-ref="rail" @scroll.throttle.100ms="sync()"
             class="chip-rail flex gap-3 overflow-x-auto pb-2 sm:gap-4 xl:gap-5">
            @foreach($books as $book)
                <div class="{{ $width }} shrink-0">
                    @include('components.book-card', ['book' => $book])
                </div>
            @endforeach
        </div>

        <button type="button" @click="go(-1)" x-show="!atStart" x-cloak
                class="absolute -left-3 top-[30%] z-20 grid h-10 w-10 place-items-center rounded-full border border-[#dcd8cd] bg-white/95 text-[#0f2255] shadow-md transition hover:bg-[#0f2255] hover:text-white lg:-left-5"
                aria-label="Geser ke kiri">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <button type="button" @click="go(1)" x-show="!atEnd" x-cloak
                class="absolute -right-3 top-[30%] z-20 grid h-10 w-10 place-items-center rounded-full border border-[#dcd8cd] bg-white/95 text-[#0f2255] shadow-md transition hover:bg-[#0f2255] hover:text-white lg:-right-5"
                aria-label="Geser ke kanan">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
@endif
