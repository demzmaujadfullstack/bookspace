@extends('layouts.admin')

@section('title', 'Manajemen Carousel & Banner')
@section('page_title', 'Manajemen Carousel Hero Banner')

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteAction: '', bannerTitle: '' }">

    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="font-display font-bold text-base text-gray-900">Daftar Banner Landing Page</h2>
            <p class="text-xs text-gray-400 mt-0.5">Atur urutan dan banner aktif yang tampil otomatis pada slider beranda</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="bg-[#0f2557] hover:bg-orange-500 text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors shadow flex items-center gap-1.5 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Banner Baru
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 text-center w-16">Urutan</th>
                        <th class="pb-3">Judul & Subtitle</th>
                        <th class="pb-3">Tombol CTA & Link</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 text-center font-bold text-sm text-[#0f2557]">
                            {{ $banner->sort_order }}
                        </td>
                        <td class="py-3.5 max-w-[280px]">
                            <p class="font-bold text-gray-900 text-xs truncate">{{ $banner->title }}</p>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $banner->subtitle ?: '-' }}</p>
                        </td>
                        <td class="py-3.5 text-gray-600">
                            @if($banner->cta_text)
                                <span class="bg-orange-50 text-orange-700 font-semibold px-2 py-0.5 rounded text-[10px]">{{ $banner->cta_text }}</span>
                                <span class="text-[10px] text-gray-400 block font-mono mt-0.5">{{ $banner->cta_link }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-center">
                            <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-colors {{ $banner->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $banner->is_active ? 'Aktif' : 'Non-aktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-3.5 text-right space-x-2">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                            <button
                                type="button"
                                @click="deleteAction = '{{ route('admin.banners.destroy', $banner) }}'; bannerTitle = '{{ addslashes($banner->title) }}'; deleteModal = true"
                                class="text-red-500 hover:text-red-700 font-semibold"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada banner carousel.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($banners->hasPages())
                <div class="mt-4">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl" @click.away="deleteModal = false">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-display font-bold text-base text-gray-900 mb-1">Hapus Banner?</h3>
            <p class="text-xs text-gray-500 mb-1">Banner yang akan dihapus:</p>
            <p class="text-xs font-bold text-gray-800 mb-5" x-text="bannerTitle"></p>
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
