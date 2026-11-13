@extends('layouts.bookspace')

@section('title', 'Pengaturan Profil Pembaca')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 md:py-12">
    <div class="mb-8 pb-5 border-b border-[#e8e6df]">
        <a href="{{ route('user.dashboard') }}" class="text-xs text-gray-500 hover:text-[#0f2255] transition-colors mb-2 inline-flex items-center gap-1">
            &larr; Kembali ke Dashboard Akun
        </a>
        <h1 class="font-serif-display font-bold text-2xl md:text-3xl text-[#0f2255] mt-1">
            Pengaturan Profil Pembaca
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Perbarui data identitas pribadi, kontak pengiriman, dan keamanan kata sandi akun Anda.
        </p>
    </div>

    <div class="bg-white border border-[#e8e6df] rounded-2xl p-6 sm:p-10 shadow-sm">
        <form method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            @method('PATCH')

            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-4 pb-3 border-b border-[#f0eee6]">
                Identitas &amp; Kontak Pembaca
            </h2>

            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                    >
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                    >
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp / HP Aktif
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                    >
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2 pb-3 border-b border-[#f0eee6]">
                Perbarui Kata Sandi (Opsional)
            </h2>
            <p class="text-xs text-gray-400 mb-4">Biarkan bagian ini kosong jika Anda tidak berencana mengganti kata sandi.</p>

            <div class="space-y-4 mb-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi Saat Ini
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                    >
                    @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        >
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
                        >
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-5 border-t border-[#f0eee6]">
                <a href="{{ route('user.dashboard') }}" class="border border-[#dcd8cd] text-gray-700 hover:border-gray-400 px-5 py-2.5 rounded-lg text-xs font-semibold transition-colors bg-[#fafaf8]">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs py-2.5 px-6 rounded-lg shadow-sm">
                    Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
