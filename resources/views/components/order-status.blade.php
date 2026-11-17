@props(['order'])

@php
    $classes = match ($order->status) {
        \App\Models\Order::PENDING => 'bg-[#f4f2ec] text-[#3f4451]',
        \App\Models\Order::MENUNGGU_PEMBAYARAN => 'bg-[#fdf1e6] text-[#c54e12]',
        \App\Models\Order::MENUNGGU_VERIFIKASI => 'bg-[#eef3fb] text-[#1d4ed8]',
        \App\Models\Order::DIBAYAR => 'bg-[#eef0fb] text-[#3941a6]',
        \App\Models\Order::DIPROSES => 'bg-[#f2ecfa] text-[#6b3fa0]',
        \App\Models\Order::DIKIRIM => 'bg-[#e6f4f8] text-[#0d6b82]',
        \App\Models\Order::SELESAI => 'bg-[#e9f5ee] text-[#166534]',
        \App\Models\Order::DIBATALKAN => 'bg-[#fdecea] text-[#b42318]',
        default => 'bg-[#f4f2ec] text-[#3f4451]',
    };
@endphp

<span {{ $attributes->merge(['class' => 'pill '.$classes]) }}>{{ $order->statusLabel() }}</span>
