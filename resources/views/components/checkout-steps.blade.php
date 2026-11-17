@props(['step' => 1])

@php
    $steps = [1 => 'Keranjang', 2 => 'Pengiriman', 3 => 'Pembayaran', 4 => 'Selesai'];
@endphp

<div {{ $attributes->merge(['class' => 'mb-6 border-b border-[#e6e3db] pb-5']) }}>
<ol class="mx-auto flex w-full max-w-2xl items-center" aria-label="Langkah pemesanan">
    @foreach($steps as $index => $label)
        @php
            $state = $index < $step ? 'done' : ($index === $step ? 'current' : 'todo');
        @endphp

        <li class="flex shrink-0 items-center gap-1.5 sm:gap-2" aria-current="{{ $state === 'current' ? 'step' : 'false' }}">
            @if($state === 'done')
                <span class="grid h-6 w-6 place-items-center rounded-full bg-[#0f2255] text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
            @elseif($state === 'current')
                <span class="grid h-6 w-6 place-items-center rounded-full bg-[#e05c1a] text-[11px] font-bold text-white">{{ $index }}</span>
            @else
                <span class="grid h-6 w-6 place-items-center rounded-full border border-[#dcd8cd] bg-white text-[11px] font-bold text-[#9a9484]">{{ $index }}</span>
            @endif

            <span class="whitespace-nowrap text-[11px] sm:text-[12px] {{ $state === 'todo' ? 'text-[#9a9484]' : 'font-semibold text-[#0f2255]' }}">
                {{ $label }}
            </span>
        </li>

        @unless($loop->last)
            <li class="mx-1.5 h-px flex-1 bg-[#e0dccf] sm:mx-3" aria-hidden="true"></li>
        @endunless
    @endforeach
</ol>
</div>
