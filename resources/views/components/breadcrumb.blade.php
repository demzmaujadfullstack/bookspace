@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5 text-[12px] text-[#6b6f78]']) }}>
    @foreach($items as $label => $url)
        @if($url)
            <a href="{{ $url }}" class="transition-colors hover:text-[#0f2255]">{{ $label }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#c9c4b6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        @else
            <span class="font-semibold text-[#0f2255]">{{ $label }}</span>
        @endif
    @endforeach
</nav>
