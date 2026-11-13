@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Akun Pengguna')

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteAction: '', userName: '' }">

    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, email, atau telepon..."
                    class="text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 w-full sm:w-64 focus:ring-2 focus:ring-orange-300"
                >

                <select name="role" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua Role</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Pelanggan (User)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>

                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-xl transition-colors">Cari</button>
            </div>
            <p class="text-xs text-gray-400">Total {{ $users->total() }} akun terdaftar</p>
        </form>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3">Nama & Email</th>
                        <th class="pb-3">Nomor Telepon</th>
                        <th class="pb-3 text-center">Role</th>
                        <th class="pb-3 text-center">Total Pesanan</th>
                        <th class="pb-3">Terdaftar Sejak</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 text-gray-600">{{ $user->phone ?: '-' }}</td>
                        <td class="py-3.5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3.5 text-center font-bold text-gray-900">{{ $user->orders_count }}</td>
                        <td class="py-3.5 text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-3.5 text-right space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Detail</a>
                            @if($user->id !== auth()->id())
                                <button
                                    type="button"
                                    @click="deleteAction = '{{ route('admin.users.destroy', $user) }}'; userName = '{{ addslashes($user->name) }}'; deleteModal = true"
                                    class="text-red-500 hover:text-red-700 font-semibold"
                                >
                                    Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="deleteModal = false">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Hapus Pengguna?</h3>
            <p class="text-xs text-gray-500 mb-1">Akun yang akan dihapus:</p>
            <p class="text-xs font-bold text-gray-800 mb-5" x-text="userName"></p>
            <form :action="deleteAction" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="text-xs border border-gray-200 text-gray-700 px-4 py-2 rounded-xl">Batal</button>
                <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-xl">Hapus</button>
            </form>
        </div>
    </div>

</div>
@endsection
