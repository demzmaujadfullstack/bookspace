@extends('layouts.admin')

@section('title', 'Chat: ' . $conversation->subject)
@section('page_title', 'Obrolan Pengguna')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.chat.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Kembali ke Daftar Obrolan</a>
        <form method="POST" action="{{ route('admin.chat.close', $conversation) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-xs border px-3 py-1.5 rounded-xl font-semibold transition-colors {{ $conversation->status === 'open' ? 'border-gray-300 text-gray-700 hover:bg-gray-100' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                {{ $conversation->status === 'open' ? 'Tandai Selesai (Tutup)' : 'Buka Kembali Obrolan' }}
            </button>
        </form>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- ── LEFT: CHAT BOX ───────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm flex flex-col h-[560px] overflow-hidden">
            {{-- Chat Header --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h2 class="font-display font-bold text-sm text-gray-900">{{ $conversation->subject }}</h2>
                    <p class="text-[10px] text-gray-400">Pengguna: {{ $conversation->user->name }} ({{ $conversation->user->email }})</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $conversation->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $conversation->status === 'open' ? 'Aktif' : 'Ditutup' }}
                </span>
            </div>

            {{-- Messages Area --}}
            <div id="chat-messages" class="flex-1 p-5 overflow-y-auto space-y-3 bg-gray-50/30">
                @foreach($conversation->messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-[10px] font-semibold text-gray-500">{{ $isMe ? 'Admin (Saya)' : $msg->sender->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <div class="max-w-[75%] rounded-2xl px-4 py-2 text-xs leading-relaxed shadow-sm {{ $isMe ? 'bg-[#0f2557] text-white rounded-tr-none' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-none' }}">
                            {{ $msg->body }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Input Form --}}
            <div class="p-3.5 bg-white border-t border-gray-100">
                <form id="admin-chat-form" method="POST" action="{{ route('admin.chat.messages.store', $conversation) }}" class="flex items-center gap-2">
                    @csrf
                    <input
                        type="text"
                        id="admin-input"
                        name="body"
                        placeholder="Ketik balasan Anda untuk pengguna..."
                        maxlength="2000"
                        required
                        autocomplete="off"
                        class="flex-1 text-xs border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-orange-300"
                    >
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors shadow">
                        Kirim Balasan
                    </button>
                </form>
            </div>
        </div>

        {{-- ── RIGHT: USER INFO ─────────────────────────────────── --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm text-xs">
                <h3 class="font-display font-bold text-sm text-gray-900 mb-3 pb-2 border-b border-gray-100">Profil Pelanggan</h3>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 truncate">{{ $conversation->user->name }}</p>
                        <p class="text-[11px] text-gray-400 truncate">{{ $conversation->user->email }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-gray-600 pt-2 border-t border-gray-100">
                    <p>Telepon: <strong class="text-gray-900">{{ $conversation->user->phone ?: '-' }}</strong></p>
                    <p>Terdaftar: <strong class="text-gray-900">{{ $conversation->user->created_at->format('d/m/Y') }}</strong></p>
                    <p>Total Pesanan: <strong class="text-gray-900">{{ $conversation->user->orders->count() }} transaksi</strong></p>
                </div>
            </div>

            {{-- Recent orders of this user --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm text-xs">
                <h3 class="font-display font-bold text-sm text-gray-900 mb-3 pb-2 border-b border-gray-100">Pesanan Pelanggan Ini</h3>

                @if($conversation->user->orders->isEmpty())
                    <p class="text-gray-400 italic">Belum ada pesanan.</p>
                @else
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($conversation->user->orders->take(4) as $ord)
                        <div class="p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-gray-900">#{{ $ord->code }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $ord->statusLabel() }}</span>
                            </div>
                            <a href="{{ route('admin.orders.show', $ord) }}" class="text-[11px] text-orange-600 hover:underline font-semibold">Lihat</a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('chat-messages');
    const form = document.getElementById('admin-chat-form');
    const input = document.getElementById('admin-input');
    const pollUrl = "{{ route('admin.chat.poll', $conversation) }}";
    const csrfToken = "{{ csrf_token() }}";

    function scrollToBottom() {
        container.scrollTop = container.scrollHeight;
    }
    scrollToBottom();

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
                    appendMessage(data.message.body, data.message.time, true, 'Admin (Saya)');
                    scrollToBottom();
                }
            } catch (err) {
                console.error(err);
            }
        });
    }

    function appendMessage(body, time, isMe, senderName) {
        const wrap = document.createElement('div');
        wrap.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'}`;
        wrap.innerHTML = `
            <div class="flex items-center gap-1.5 mb-1">
                <span class="text-[10px] font-semibold text-gray-500">${senderName}</span>
                <span class="text-[10px] text-gray-400">${time}</span>
            </div>
            <div class="max-w-[75%] rounded-2xl px-4 py-2 text-xs leading-relaxed shadow-sm ${isMe ? 'bg-[#0f2557] text-white rounded-tr-none' : 'bg-white border border-gray-200 text-gray-800 rounded-tl-none'}">
                ${body}
            </div>
        `;
        container.appendChild(wrap);
    }

    let lastCount = {{ $conversation->messages->count() }};
    setInterval(async () => {
        try {
            const res = await fetch(pollUrl, { credentials: 'same-origin' });
            if (res.ok) {
                const data = await res.json();
                if (data.messages && data.messages.length > lastCount) {
                    container.innerHTML = '';
                    data.messages.forEach(m => {
                        appendMessage(m.body, m.time, m.is_me, m.is_me ? 'Admin (Saya)' : m.sender_name);
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
