@props(['items'])

<nav aria-label="breadcrumb" {{ $attributes->merge(['class' => 'text-xs text-gray-500 mb-6']) }}>
    <ol class="flex flex-wrap items-center gap-1.5">
        @foreach($items as $item)
            @if(empty($item['url']))
                <li aria-current="page" class="font-semibold text-[#0f2255] max-w-[280px] truncate">{{ $item['label'] }}</li>
            @else
                <li class="flex items-center gap-1.5">
                    <a href="{{ $item['url'] }}" class="hover:text-[#e05c1a] transition-colors">{{ $item['label'] }}</a>
                    <span class="text-gray-300" aria-hidden="true">/</span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
