<x-guest-layout>
    <div class="mb-6 text-center">
        <span class="text-[11px] font-bold text-[#e05c1a] uppercase tracking-widest block mb-1">Registrasi Pembaca</span>
        <h1 class="font-serif-display font-bold text-2xl text-[#0f2255]">Bergabung dengan BOOKSPACE</h1>
        <p class="text-xs text-gray-500 mt-1">Daftarkan akun Anda untuk kemudahan berbelanja &amp; ulasan buku.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Nomor WhatsApp / HP
            </label>
            <input
                id="phone"
                type="tel"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="Contoh: 081234567890"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Kata Sandi <span class="text-red-500">*</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Ulangi Kata Sandi <span class="text-red-500">*</span>
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold text-xs sm:text-sm py-3 rounded-xl transition-all shadow-md">
                Daftar Akun Baru
            </button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-[#f0eee6] text-center text-xs text-gray-500">
        Sudah memiliki akun terdaftar?
        <a href="{{ route('login') }}" class="font-bold text-[#e05c1a] hover:underline ml-1">Masuk sekarang</a>
    </div>
</x-guest-layout>
