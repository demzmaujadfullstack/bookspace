@props(['user' => null])

@php
    $accountUser = $user ?? auth()->user();
    $accountOrders = \App\Models\Order::where('user_id', $accountUser->id)->count();
    $accountWishlist = count($accountUser->wishlistedBookIds());
    $accountCart = (int) ($accountUser->cart?->items()->sum('quantity') ?? 0);

    $links = [
        ['route' => 'user.dashboard', 'label' => 'Ringkasan Akun', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'match' => 'user.dashboard', 'count' => null],
        ['route' => 'user.orders', 'label' => 'Pesanan Saya', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'match' => 'user.orders*', 'count' => $accountOrders],
        ['route' => 'wishlist.index', 'label' => 'Daftar Favorit', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'match' => 'wishlist.*', 'count' => $accountWishlist],
        ['route' => 'cart.index', 'label' => 'Keranjang', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'cart.*', 'count' => $accountCart],
        ['route' => 'user.profile.edit', 'label' => 'Profil & Alamat', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'match' => 'user.profile*', 'count' => null],
        ['route' => 'help.index', 'label' => 'Pusat Bantuan', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'match' => 'help.*', 'count' => null],
    ];

    if ($accountUser->isAdmin()) {
        $buyerOnly = ['user.dashboard', 'user.orders', 'wishlist.index', 'cart.index'];
        $links = array_values(array_filter($links, fn ($link) => ! in_array($link['route'], $buyerOnly, true)));
        array_unshift($links, [
            'route' => 'admin.dashboard',
            'label' => 'Dasbor Admin',
            'icon' => 'M10.325 9.432c.372-.372.977-.372 1.35 0l3 3a.957.957 0 010 1.35l-3 3a.957.957 0 01-1.35 0l-3-3a.957.957 0 010-1.35l3-3zM3 4h18M3 20h18',
            'match' => 'admin.*',
            'count' => null,
        ]);
    }
@endphp

<aside {{ $attributes }}>
    <div class="card sticky top-[108px] overflow-hidden">
        <div class="flex items-center gap-3 border-b border-[#e6e3db] px-5 py-4">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#0f2255] font-serif-display text-[16px] font-bold text-white">
                {{ strtoupper(substr($accountUser->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-[14px] font-bold text-[#17181c]">{{ $accountUser->name }}</p>
                <p class="truncate text-[12px] text-[#6b6f78]">{{ $accountUser->email }}</p>
            </div>
        </div>

        <nav class="p-2">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-semibold transition-colors
                        {{ request()->routeIs($link['match']) ? 'bg-[#0f2255] text-white' : 'text-[#3f4451] hover:bg-[#f4f2ec]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                    <span class="flex-1">{{ $link['label'] }}</span>
                    @if($link['count'])
                        <span class="pill {{ request()->routeIs($link['match']) ? 'bg-white/15 text-white' : 'bg-[#f4f2ec] text-[#6b6f78]' }}">{{ $link['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e6e3db] p-2">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-semibold text-[#b42318] transition-colors hover:bg-[#fdecea]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>
</aside>
