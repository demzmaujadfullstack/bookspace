@extends('layouts.bookspace')

@section('title', 'Pusat Bantuan')

@section('content')
<div class="shell py-6 md:py-8">

    <div class="mb-6">
        <x-breadcrumb :items="['Beranda' => route('home'), 'Pusat Bantuan' => null]" class="mb-4" />
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="page-title">Pusat Bantuan</h1>
                <p class="muted mt-1 max-w-2xl">Jawaban cepat untuk pertanyaan seputar pemesanan, pembayaran, dan pengiriman. Belum ketemu solusinya? Kirim pertanyaan langsung ke tim kami.</p>
            </div>
            @if(!empty($settings['site_whatsapp']))
                <a href="https://wa.me/{{ $settings['site_whatsapp'] }}" target="_blank" rel="noopener" class="btn-ghost btn-sm shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#128c7e]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.174.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.347-.347.52-.52.174-.174.232-.298.347-.497.116-.198.058-.371-.03-.52-.087-.148-.663-1.597-.907-2.19-.243-.588-.489-.508-.67-.517h-.572c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.695.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.29.173-1.414-.074-.124-.272-.198-.57-.347z"/><path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652a11.9 11.9 0 005.71 1.454h.006c6.585 0 11.946-5.336 11.949-11.896 0-3.176-1.24-6.165-3.495-8.411zm-8.475 18.297a9.9 9.9 0 01-5.039-1.378l-.36-.214-3.76.981 1.005-3.678-.234-.372a9.898 9.898 0 01-1.512-5.264c0-5.46 4.456-9.905 9.92-9.905 2.65 0 5.14 1.033 7.01 2.906a9.83 9.83 0 012.9 6.99c-.003 5.459-4.458 9.904-9.929 9.904z"/></svg>
                    Hubungi via WhatsApp
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

        {{-- ── FAQ ─────────────────────────────────────────────── --}}
        <section class="lg:col-span-7 xl:col-span-8">
            <div class="card !p-0 overflow-hidden">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Pertanyaan yang Kerap Diajukan</h2>
                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">{{ count($faqs) }} topik paling sering ditanyakan pembaca BOOKSPACE.</p>
                </header>

                <div class="divide-y divide-[#f0eee6]">
                    @foreach($faqs as $i => $faq)
                        <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }" class="px-5 py-4">
                            <button type="button" @click="open = !open" :aria-expanded="open"
                                class="flex w-full items-start justify-between gap-4 text-left">
                                <span class="text-[14px] font-semibold leading-snug text-[#17181c]" :class="open ? 'text-[#0f2255]' : ''">
                                    {{ $faq['q'] }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-[#9a9484] transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-[#e05c1a]' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <p x-show="open" x-transition class="mt-2.5 max-w-2xl text-[13px] leading-relaxed text-[#6b6f78]" x-cloak>
                                {{ $faq['a'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="mt-4 text-[12px] text-[#9a9484]">
                Sedang menunggu pesanan? Cek status lengkapnya di halaman
                <a href="{{ route('user.orders') }}" class="font-semibold text-[#0f2255] underline decoration-[#dcd8cd] underline-offset-2">Pesanan Saya</a>.
            </p>
        </section>

        {{-- ── Chat rail ───────────────────────────────────────── --}}
        <div class="space-y-6 lg:col-span-5 xl:col-span-4">

            @if($openConversation)
                <div class="card border-[#cfe3d8] !p-0 overflow-hidden">
                    <div class="flex items-start gap-3 bg-[#f2f9f5] px-5 py-4">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#128c7e] text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-8 8H7l-4 3V12a8 8 0 018-8h2a8 8 0 018 8z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-bold text-[#0f5132]">Anda punya obrolan yang masih terbuka</p>
                            <p class="mt-0.5 truncate text-[12px] text-[#3f6b56]">{{ $openConversation->subject }}</p>
                            <a href="{{ route('help.conversations.show', $openConversation) }}" class="btn-sm mt-3 inline-flex items-center gap-1.5 rounded-lg bg-[#128c7e] px-3 py-1.5 text-[13px] font-semibold text-white transition hover:bg-[#0f7268]">
                                Lanjutkan Obrolan
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <section class="card">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Kirim Pertanyaan</h2>
                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">Admin membalas pada jam operasional 08.00&ndash;20.00 WIB.</p>
                </header>

                <form method="POST" action="{{ route('help.conversations.start') }}" class="space-y-4 p-5">
                    @csrf
                    <div>
                        <label class="field-label" for="help-subject">Subjek Pertanyaan <span class="text-[#b42318]">*</span></label>
                        <input type="text" id="help-subject" name="subject" value="{{ old('subject') }}" required maxlength="150"
                            placeholder="Contoh: Konfirmasi pembayaran #BKSP-0012" class="field">
                        @error('subject') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="help-message">Isi Pesan <span class="text-[#b42318]">*</span></label>
                        <textarea id="help-message" name="message" rows="4" required maxlength="2000" class="field"
                            placeholder="Tuliskan kendala, pertanyaan stok, atau kebutuhan pesanan Anda...">{{ old('message') }}</textarea>
                        @error('message') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full">Mulai Obrolan dengan Admin</button>
                </form>
            </section>

            <section class="card">
                <header class="flex items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Riwayat Obrolan</h2>
                    <span class="pill bg-[#f4f2ec] text-[#6b6f78]">{{ $conversations->total() }}</span>
                </header>

                @if($conversations->isEmpty())
                    <p class="px-5 py-8 text-center text-[13px] text-[#9a9484]">Belum ada obrolan sebelumnya.</p>
                @else
                    <div class="divide-y divide-[#f0eee6]">
                        @foreach($conversations as $conv)
                            <a href="{{ route('help.conversations.show', $conv) }}" class="flex items-start gap-3 px-5 py-3.5 transition-colors hover:bg-[#fafaf8]">
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $conv->status === 'open' ? 'bg-[#128c7e]' : 'bg-[#c9c4b6]' }}"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-baseline justify-between gap-3">
                                        <span class="truncate text-[13px] font-semibold text-[#17181c]">{{ $conv->subject }}</span>
                                        <span class="shrink-0 text-[11px] text-[#9a9484]">{{ $conv->last_message_at?->diffForHumans() }}</span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-[12px] text-[#6b6f78]">{{ $conv->latestMessage?->body ?: 'Belum ada pesan.' }}</span>
                                </span>
                                @if($conv->unread_count > 0)
                                    <span class="pill shrink-0 bg-[#e05c1a] text-white">{{ $conv->unread_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    @if($conversations->hasPages())
                        <div class="border-t border-[#f0eee6] px-5 py-3">{{ $conversations->links() }}</div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
