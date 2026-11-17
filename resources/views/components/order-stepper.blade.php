@props(['order'])

@php
    $status = $order->status;

    $steps = [
        ['label' => 'Pesanan dibuat', 'step' => 1],
        ['label' => 'Pembayaran diunggah', 'step' => 2],
        ['label' => 'Verifikasi admin', 'step' => 3],
        ['label' => 'Dikirim', 'step' => 4],
        ['label' => 'Selesai', 'step' => 5],
    ];

    $reached = match (true) {
        $status === \App\Models\Order::SELESAI => 5,
        $status === \App\Models\Order::DIKIRIM => 4,
        $status === \App\Models\Order::DIPROSES => 3,
        $status === \App\Models\Order::DIBAYAR => 3,
        $status === \App\Models\Order::MENUNGGU_VERIFIKASI => 2,
        $status === \App\Models\Order::MENUNGGU_PEMBAYARAN => 1,
        $status === \App\Models\Order::PENDING => 1,
        default => 0,
    };

    $cancelled = $status === \App\Models\Order::DIBATALKAN;
@endphp

@if($cancelled)
    <div class="flex items-center gap-2.5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-[13px] font-semibold text-red-800">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-9 9 9 9 0 019-9zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        Pesanan ini dibatalkan.
    </div>
@else
    {{-- Desktop: stepper horizontal --}}
    <ol class="hidden sm:flex items-stretch gap-1" aria-label="Status pesanan">
        @foreach($steps as $index => $step)
            @php
                $number = $step['step'];
                $done = $reached >= $number;
                $current = $reached === $number && ! $done ? false : $reached === $number;
            @endphp
            <li class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-5 h-5 shrink-0 rounded-full flex items-center justify-center text-[10px] font-bold
                        {{ $done ? 'bg-[#0f2255] text-white' : 'bg-white text-[#9a978d] border border-[#dcd8cd]' }}">
                        @if($done)
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $number }}
                        @endif
                    </span>
                    <span class="h-px flex-1 {{ $index === count($steps) - 1 ? 'hidden' : ($done ? 'bg-[#0f2255]' : 'bg-[#e6e3db]') }}"></span>
                </div>
                <p class="text-[11px] leading-tight {{ $done ? 'font-semibold text-[#0f2255]' : 'text-[#8b8578]' }}">{{ $step['label'] }}</p>
            </li>
        @endforeach
    </ol>

    {{-- Mobile: ringkas --}}
    <div class="sm:hidden">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[13px] font-semibold text-[#0f2255]">{{ $steps[max($reached - 1, 0)]['label'] }}</p>
            <p class="text-[11px] text-[#8b8578]">{{ $reached }}/{{ count($steps) }}</p>
        </div>
        <div class="h-1.5 rounded-full bg-[#e6e3db] overflow-hidden">
            <div class="h-full rounded-full bg-[#0f2255] transition-all" style="width: {{ ($reached / count($steps)) * 100 }}%"></div>
        </div>
    </div>
@endif
