@extends('layouts.bookspace')

@section('title', 'Pusat Bantuan & Layanan Pembaca')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
    <div class="mb-10 text-center max-w-2xl mx-auto">
        <span class="inline-block bg-[#f4f2ec] text-[#e05c1a] text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-widest mb-2.5">
            Layanan &amp; Bantuan Pembaca
        </span>
        <h1 class="font-serif-display font-bold text-3xl md:text-4xl text-[#0f2255]">
            Ada yang Bisa Kami Bantu?
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
            Temukan jawaban atas pertanyaan umum seputar pemesanan, ketersediaan stok, pengiriman paket, atau hubungi langsung tim redaksi kami.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- ── LEFT: FAQ (7 cols) ─────────────────────────────── --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#e05c1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="font-serif-display font-bold text-xl text-[#0f2255]">
                    Pertanyaan yang Kerap Ditanyakan (FAQ)
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($faqs as $i => $faq)
                <div x-data="{ open: {{ $i === 0 ? 'true' : 'false' }} }" class="bg-white border border-[#e8e6df] rounded-2xl p-5 shadow-sm transition-all hover:border-[#0f2255]/30">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left text-sm font-serif-display font-bold text-gray-900 group">
                        <span class="group-hover:text-[#0f2255] transition-colors">{{ $faq['q'] }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-3" :class="open ? 'rotate-180 text-[#e05c1a]' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-[#f0eee6] pt-3 font-sans">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── RIGHT: LIVE CHAT & CONVERSATIONS (5 cols) ──────── --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Start Chat Card --}}
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-7 shadow-sm">
                <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-[#f0eee6]">
                    <div class="w-10 h-10 rounded-xl bg-[#f4f2ec] text-[#0f2255] flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-serif-display font-bold text-base text-[#0f2255]">Layanan Chat Langsung</h3>
                        <p class="text-xs text-gray-400">Terhubung langsung dengan tim admin BOOKSPACE</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('help.conversations.start') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Subjek / Topik Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="subject"
                            placeholder="Contoh: Konfirmasi pembayaran #BKSP-..."
                            required
                            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-3.5 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Pesan Anda <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="message"
                            rows="3"
                            placeholder="Tuliskan kendala, pertanyaan judul buku, atau kebutuhan pesanan Anda..."
                            required
                            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-3.5 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        ></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold text-xs sm:text-sm py-3 rounded-xl transition-all shadow-md">
                        Mulai Obrolan dengan Admin
                    </button>
                </form>
            </div>

            {{-- Previous Conversations --}}
            <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
                <h3 class="font-serif-display font-bold text-base text-[#0f2255] mb-3 pb-3 border-b border-[#f0eee6]">
                    Riwayat Obrolan Anda
                </h3>

                @if($conversations->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada obrolan sebelumnya.</p>
                @else
                    <div class="divide-y divide-[#f0eee6] max-h-60 overflow-y-auto pr-1">
                        @foreach($conversations as $conv)
                        <a href="{{ route('help.conversations.show', $conv) }}" class="block py-3 hover:bg-[#fafaf8] rounded-lg px-2 transition-colors">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-serif-display font-bold text-gray-900 truncate max-w-[160px]">{{ $conv->subject }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $conv->last_message_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 truncate">{{ $conv->latestMessage?->body }}</p>
                            <div class="mt-1.5 flex items-center justify-between">
                                <span class="text-[9px] uppercase tracking-wider font-bold {{ $conv->status === 'open' ? 'text-emerald-700' : 'text-gray-400' }}">
                                    ● {{ $conv->status === 'open' ? 'Aktif' : 'Selesai' }}
                                </span>
                                @if($conv->unread_count > 0)
                                    <span class="bg-[#e05c1a] text-white text-[9px] font-bold px-2 py-0.5 rounded-full">{{ $conv->unread_count }} baru</span>
                                @endif
                            </div>
                        </a>
                        @endforeach

                        @if($conversations->hasPages())
                            <div class="pt-3">{{ $conversations->links() }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- WhatsApp Direct Alternative --}}
            @if(isset($settings['site_whatsapp']) && $settings['site_whatsapp'])
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center">
                <p class="text-xs font-bold text-emerald-900 mb-1">Butuh Respon Segera?</p>
                <p class="text-[11px] text-emerald-700 mb-3">Hubungi tim customer care kami via WhatsApp resmi.</p>
                <a href="https://wa.me/{{ $settings['site_whatsapp'] }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition-all shadow-sm">
                    Chat WhatsApp Sekarang
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
