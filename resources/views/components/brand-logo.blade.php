@props(['tone' => 'navbar', 'url' => null])

@php
    $tones = [
        'navbar' => [
            'wrap' => 'flex shrink-0 items-center gap-2.5',
            'mark' => 'grid h-9 w-9 place-items-center rounded-lg bg-[#0f2255] text-white',
            'icon' => 'h-5 w-5',
            'image' => 'h-9 max-w-[132px] sm:max-w-[190px]',
            'title' => 'block font-serif-display text-[19px] font-bold tracking-tight text-[#0f2255]',
            'subtitle' => 'truncate text-[10px] uppercase tracking-[0.18em] text-[#8b8578]',
            'sub' => \App\Models\Setting::value('site_tagline', 'Toko Buku Online'),
            'url' => route('home'),
        ],
        'footer' => [
            'wrap' => 'inline-flex items-center gap-2.5',
            'mark' => 'grid h-9 w-9 place-items-center rounded-lg bg-[#e05c1a] text-white',
            'icon' => 'h-5 w-5',
            'image' => 'h-9 max-w-[190px]',
            'title' => 'font-serif-display text-xl font-bold tracking-tight text-white',
            'sub' => null,
            'url' => route('home'),
        ],
        'admin' => [
            'wrap' => 'flex min-w-0 items-center gap-2.5',
            'mark' => 'rounded-lg bg-[#e05c1a] p-1.5 shadow-sm',
            'icon' => 'h-5 w-5 text-white',
            'image' => 'h-9 max-w-[150px] rounded-lg',
            'title' => 'block truncate font-serif-display text-lg font-bold tracking-tight text-white',
            'subtitle' => 'block text-[9px] font-bold uppercase tracking-widest text-[#e05c1a]',
            'sub' => 'Admin Control',
            'url' => route('admin.dashboard'),
            'stack' => true,
        ],
        'guest' => [
            'wrap' => 'inline-flex items-center gap-2.5',
            'mark' => 'grid h-8 w-8 place-items-center rounded-lg bg-[#0f2255] text-white',
            'icon' => 'h-4 w-4',
            'image' => 'h-8 max-w-[170px]',
            'title' => 'font-serif-display text-xl font-bold tracking-tight text-[#0f2255]',
            'sub' => null,
            'url' => route('home'),
        ],
    ];

    $t = $tones[$tone] ?? $tones['navbar'];
    $brandName = \App\Models\Setting::siteName();
    $logoUrl = \App\Models\Setting::logoUrl();
    $stacked = (bool) ($logoUrl && ($t['stack'] ?? false));
    $showWordmark = $logoUrl ? (\App\Models\Setting::showsWordmark() && ! $stacked) : true;
    $subClass = ($t['subtitle'] ?? '').' mt-1'.($tone === 'navbar' ? ' hidden '.($logoUrl ? 'lg:block' : 'sm:block') : '');
@endphp

<a href="{{ $url ?? $t['url'] }}" {{ $attributes->merge(['class' => $stacked ? 'flex min-w-0 flex-col items-start gap-1.5' : $t['wrap']]) }}>
    @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="Logo {{ $brandName }}" class="{{ $stacked ? 'h-8 max-w-[168px]' : $t['image'] }} w-auto shrink-0 object-contain{{ $tone === 'admin' ? ' rounded-lg' : '' }}">
    @else
        <span class="{{ $t['mark'] }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $t['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </span>
    @endif

    @if($showWordmark)
        <span class="min-w-0 leading-none{{ $logoUrl && ! $stacked ? ' hidden sm:block' : '' }}">
            <span class="{{ $t['title'] }}{{ $t['sub'] ? ' truncate' : '' }}">{{ $brandName }}</span>
            @if($t['sub'])
                <span class="{{ $subClass }}">{{ $t['sub'] }}</span>
            @endif
        </span>
    @elseif($stacked && $t['sub'])
        <span class="{{ $t['subtitle'] }}">{{ $t['sub'] }}</span>
    @endif
</a>
