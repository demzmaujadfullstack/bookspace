@extends('layouts.admin')

@section('title', 'Help Center & Live Chat')
@section('page_title', 'Manajemen Obrolan Pengguna')

@section('content')
<div class="space-y-6">

    {{-- Filter Tabs --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.chat.index') }}" class="px-4 py-2 rounded-xl font-medium transition-colors {{ !request('status') ? 'bg-[#0f2557] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua Obrolan ({{ $openCount + $closedCount }})
            </a>
            <a href="{{ route('admin.chat.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-xl font-medium transition-colors {{ request('status') === 'open' ? 'bg-green-600 text-white' : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                Sedang Aktif ({{ $openCount }})
            </a>
            <a href="{{ route('admin.chat.index', ['status' => 'closed']) }}" class="px-4 py-2 rounded-xl font-medium transition-colors {{ request('status') === 'closed' ? 'bg-gray-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Selesai ({{ $closedCount }})
            </a>
        </div>
    </div>

    {{-- Chat list --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Pengguna</th>
                        <th class="pb-3">Topik Bantuan</th>
                        <th class="pb-3">Pesan Terakhir</th>
                        <th class="pb-3">Waktu</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($conversations as $conv)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($conv->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $conv->user->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $conv->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 font-semibold text-gray-800 max-w-[200px] truncate">
                            {{ $conv->subject }}
                        </td>
                        <td class="py-3.5 text-gray-500 max-w-[260px] truncate">
                            {{ $conv->latestMessage?->body ?? '-' }}
                        </td>
                        <td class="py-3.5 text-gray-400 text-[11px]">
                            {{ $conv->last_message_at?->diffForHumans() }}
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $conv->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $conv->status === 'open' ? 'Aktif' : 'Selesai' }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right">
                            <a href="{{ route('admin.chat.show', $conv) }}" class="text-xs bg-[#0f2557] hover:bg-orange-500 text-white font-medium px-3.5 py-1.5 rounded-lg transition-colors">
                                Buka Chat
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada obrolan dari pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $conversations->links('pagination::tailwind') }}
        </div>
    </div>

</div>
@endsection
