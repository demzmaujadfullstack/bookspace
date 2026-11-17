@extends('layouts.bookspace')

@section('title', 'Obrolan: ' . $conversation->subject)

@section('content')
@php $isOpen = $conversation->status === \App\Models\Conversation::OPEN; @endphp

<div class="shell py-6 md:py-8">

    <div class="mb-5">
        <x-breadcrumb :items="['Beranda' => route('home'), 'Pusat Bantuan' => route('help.index'), 'Obrolan' => null]" class="mb-4" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">

        {{-- ── Daftar obrolan ──────────────────────────────────── --}}
        <aside class="lg:col-span-4 xl:col-span-3">
            <div class="card sticky top-[108px] !p-0 overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Obrolan Saya</h2>
                    <a href="{{ route('help.index') }}" class="btn-ghost btn-sm">+ Baru</a>
                </div>
                <div class="max-h-[420px] divide-y divide-[#f0eee6] overflow-y-auto">
                    @foreach($conversations as $conv)
                        <a href="{{ route('help.conversations.show', $conv) }}"
                            class="block px-5 py-3.5 transition-colors {{ $conv->id === $conversation->id ? 'bg-[#f7f8fc]' : 'hover:bg-[#fafaf8]' }}">
                            <span class="flex items-baseline justify-between gap-3">
                                <span class="truncate text-[13px] font-semibold {{ $conv->id === $conversation->id ? 'text-[#0f2255]' : 'text-[#17181c]' }}">{{ $conv->subject }}</span>
                                @if($conv->unread_count > 0)
                                    <span class="pill shrink-0 bg-[#e05c1a] text-white">{{ $conv->unread_count }}</span>
                                @endif
                            </span>
                            <span class="mt-0.5 flex items-center gap-1.5 text-[11px] text-[#9a9484]">
                                <span class="h-1.5 w-1.5 rounded-full {{ $conv->status === 'open' ? 'bg-[#128c7e]' : 'bg-[#c9c4b6]' }}"></span>
                                {{ $conv->status === 'open' ? 'Aktif' : 'Selesai' }}
                                &middot; {{ $conv->last_message_at?->diffForHumans() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- ── Ruang obrolan ───────────────────────────────────── --}}
        <section class="card flex min-h-[520px] flex-col overflow-hidden !p-0 lg:col-span-8 xl:col-span-9">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[14px] font-bold text-white">BA</span>
                    <div class="min-w-0">
                        <h1 class="truncate font-serif-display text-[16px] font-bold text-[#0f2255]">{{ $conversation->subject }}</h1>
                        <p class="text-[12px] text-[#6b6f78]">
                            Customer Care BOOKSPACE &middot;
                            @if($isOpen)
                                <span class="text-[#128c7e] font-semibold">balasan biasanya &lt; 1 jam</span>
                            @else
                                obrolan ditutup
                            @endif
                        </p>
                    </div>
                </div>
                <span class="pill {{ $isOpen ? 'bg-[#e8f5f1] text-[#0f5132]' : 'bg-[#f4f2ec] text-[#6b6f78]' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $isOpen ? 'animate-pulse bg-[#128c7e]' : 'bg-[#9a9484]' }}"></span>
                    {{ $isOpen ? 'Layanan Terbuka' : 'Obrolan Selesai' }}
                </span>
            </header>

            <div id="chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-[#fafaf8] px-4 py-5 sm:px-6" style="max-height: 60vh;">
                @foreach($conversation->messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex gap-2.5 {{ $isMe ? 'flex-row-reverse' : '' }}">
                        @unless($isMe)
                            <span class="mt-auto grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[11px] font-bold text-white">BA</span>
                        @endunless
                        <div class="max-w-[82%] sm:max-w-[68%]">
                            <div class="rounded-xl px-4 py-2.5 text-[13px] leading-relaxed shadow-sm
                                {{ $isMe ? 'rounded-tr-sm bg-[#0f2255] text-white' : 'rounded-tl-sm border border-[#e6e3db] bg-white text-[#17181c]' }}">
                                {{ $msg->body }}
                            </div>
                            <p class="mt-1 text-[11px] text-[#9a9484] {{ $isMe ? 'text-right' : '' }}">
                                {{ $isMe ? 'Anda' : $msg->sender->name }} &middot; {{ $msg->created_at->translatedFormat('d M, H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($isOpen)
                <form id="chat-form" method="POST" action="{{ route('help.conversations.messages.store', $conversation) }}"
                    class="flex items-center gap-2.5 border-t border-[#e6e3db] bg-white px-4 py-3.5 sm:px-6">
                    @csrf
                    <input type="text" id="message-input" name="body" required maxlength="2000" autocomplete="off"
                        placeholder="Ketik pertanyaan atau balasan Anda..." class="field flex-1">
                    <button type="submit" id="send-btn" class="btn-primary shrink-0">
                        <span class="hidden sm:inline">Kirim</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.2 5.6a1 1 0 011.35-1.26l15.1 7.6a1 1 0 010 1.79l-15.1 7.6A1 1 0 013.2 15.4L6 12m0 0h6"/></svg>
                    </button>
                </form>
            @else
                <div class="border-t border-[#e6e3db] bg-[#f4f2ec] px-5 py-4 text-center text-[13px] text-[#6b6f78]">
                    Obrolan ini sudah ditandai selesai.
                    <a href="{{ route('help.index') }}" class="font-semibold text-[#0f2255] underline underline-offset-2">Buka topik pertanyaan baru</a>.
                </div>
            @endif
        </section>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message-input');
    const pollUrl = "{{ route('help.conversations.poll', $conversation) }}";
    const csrfToken = "{{ csrf_token() }}";
    const isOpen = @json($isOpen);

    const scrollToBottom = () => { container.scrollTop = container.scrollHeight; };
    scrollToBottom();

    function renderMessage({ body, time, is_me: isMe, sender_name: senderName }) {
        const row = document.createElement('div');
        row.className = `flex gap-2.5 ${isMe ? 'flex-row-reverse' : ''}`;

        const column = document.createElement('div');
        column.className = 'max-w-[82%] sm:max-w-[68%]';

        const bubble = document.createElement('div');
        bubble.className = isMe
            ? 'rounded-xl rounded-tr-sm bg-[#0f2255] px-4 py-2.5 text-[13px] leading-relaxed text-white shadow-sm'
            : 'rounded-xl rounded-tl-sm border border-[#e6e3db] bg-white px-4 py-2.5 text-[13px] leading-relaxed text-[#17181c] shadow-sm';
        bubble.textContent = body;

        const meta = document.createElement('p');
        meta.className = `mt-1 text-[11px] text-[#9a9484] ${isMe ? 'text-right' : ''}`;
        meta.textContent = `${isMe ? 'Anda' : `${senderName} `} · ${time}`;

        column.append(bubble, meta);

        if (!isMe) {
            const avatar = document.createElement('span');
            avatar.className = 'mt-auto grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#0f2255] text-[11px] font-bold text-white';
            avatar.textContent = 'BA';
            row.append(avatar);
        }
        row.append(column);
        container.append(row);

        return row;
    }

    let lastId = {{ $conversation->messages->max('id') ?? 0 }};

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            input.value = '';
            const pending = renderMessage({ body: text, time: 'baru saja', is_me: true });
            scrollToBottom();

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ body: text }),
                });
                if (res.ok) {
                    const data = await res.json();
                    lastId = Math.max(lastId, data.message.id);
                } else {
                    throw new Error('failed');
                }
            } catch (error) {
                pending.remove();
                input.value = text;
            }
        });
    }

    if (!isOpen) return;

    setInterval(async () => {
        try {
            const res = await fetch(pollUrl, { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            const fresh = data.messages.filter((m) => m.id > lastId);
            if (!fresh.length) return;

            fresh.forEach(renderMessage);
            lastId = Math.max(...fresh.map((m) => m.id));
            scrollToBottom();
        } catch (error) {
            // jaringan sementara gagal, coba lagi pada tick berikutnya
        }
    }, 3500);
});
</script>
@endpush
@endsection
