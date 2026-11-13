@extends('layouts.bookspace')

@section('title', 'Percakapan: ' . $conversation->subject)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    <div class="mb-5 flex items-center justify-between pb-4 border-b border-[#e8e6df]">
        <div>
            <a href="{{ route('help.index') }}" class="text-xs text-gray-500 hover:text-[#0f2255] transition-colors mb-1 inline-flex items-center gap-1">
                &larr; Kembali ke Pusat Bantuan
            </a>
            <h1 class="font-serif-display font-bold text-xl md:text-2xl text-[#0f2255] mt-0.5">
                {{ $conversation->subject }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $conversation->status === 'open' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                <span class="w-2 h-2 rounded-full {{ $conversation->status === 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                {{ $conversation->status === 'open' ? 'Layanan Terbuka' : 'Obrolan Selesai' }}
            </span>
        </div>
    </div>

    {{-- Chat Box Container --}}
    <div class="bg-white border border-[#e8e6df] rounded-2xl shadow-sm flex flex-col h-[540px] overflow-hidden">
        {{-- Chat messages container --}}
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 bg-[#fafaf8]">
            @foreach($conversation->messages as $msg)
                @php $isMe = $msg->sender_id === auth()->id(); @endphp
                <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}" data-id="{{ $msg->id }}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold text-gray-600">{{ $isMe ? 'Saya' : $msg->sender->name . ' (Admin)' }}</span>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                    <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm leading-relaxed shadow-sm {{ $isMe ? 'bg-[#0f2255] text-white rounded-tr-none' : 'bg-white border border-[#e8e6df] text-gray-900 rounded-tl-none font-sans' }}">
                        {{ $msg->body }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Chat input form --}}
        @if($conversation->status === 'open')
            <div class="p-4 bg-white border-t border-[#e8e6df]">
                <form id="chat-form" method="POST" action="{{ route('help.conversations.messages.store', $conversation) }}" class="flex items-center gap-3">
                    @csrf
                    <input
                        type="text"
                        id="message-input"
                        name="body"
                        placeholder="Ketik pertanyaan atau balasan Anda..."
                        maxlength="2000"
                        required
                        autocomplete="off"
                        class="flex-1 text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-3 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                    >
                    <button
                        type="submit"
                        id="send-btn"
                        class="bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold text-xs sm:text-sm px-5 py-3 rounded-xl transition-all shadow-md shrink-0 flex items-center gap-1.5"
                    >
                        <span>Kirim</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </button>
                </form>
            </div>
        @else
            <div class="p-4 bg-[#f4f2ec] text-center text-xs text-gray-600 border-t border-[#e8e6df]">
                Percakapan ini telah ditandai selesai. Anda dapat membuat topik pertanyaan baru di halaman Bantuan kapan saja.
            </div>
        @endif
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

    function scrollToBottom() {
        container.scrollTop = container.scrollHeight;
    }
    scrollToBottom();

    // Async Send
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            input.value = '';

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ body: text })
                });
                if (res.ok) {
                    const data = await res.json();
                    appendMessage(data.message.body, data.message.time, true, 'Saya');
                    scrollToBottom();
                }
            } catch (err) {
                console.error('Send error:', err);
            }
        });
    }

    function appendMessage(body, time, isMe, senderName) {
        const wrap = document.createElement('div');
        wrap.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'}`;

        const meta = document.createElement('div');
        meta.className = 'flex items-center gap-2 mb-1';
        const name = document.createElement('span');
        name.className = 'text-[10px] font-bold text-gray-600';
        name.textContent = senderName;
        const stamp = document.createElement('span');
        stamp.className = 'text-[10px] text-gray-400 font-mono';
        stamp.textContent = time;
        meta.append(name, stamp);

        const bubble = document.createElement('div');
        bubble.className = `max-w-[78%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm leading-relaxed shadow-sm ${isMe ? 'bg-[#0f2255] text-white rounded-tr-none' : 'bg-white border border-[#e8e6df] text-gray-900 rounded-tl-none font-sans'}`;
        bubble.textContent = body;

        wrap.append(meta, bubble);
        container.appendChild(wrap);
    }

    // Polling every 3.5s
    let lastCount = {{ $conversation->messages->count() }};
    setInterval(async () => {
        try {
            const res = await fetch(pollUrl, { credentials: 'same-origin' });
            if (res.ok) {
                const data = await res.json();
                if (data.messages && data.messages.length > lastCount) {
                    container.innerHTML = '';
                    data.messages.forEach(m => {
                        appendMessage(m.body, m.time, m.is_me, m.is_me ? 'Saya' : `${m.sender_name} (Admin)`);
                    });
                    lastCount = data.messages.length;
                    scrollToBottom();
                }
            }
        } catch (e) {
            // ignore
        }
    }, 3500);
});
</script>
@endpush
@endsection
