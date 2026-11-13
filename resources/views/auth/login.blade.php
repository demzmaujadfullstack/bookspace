<x-guest-layout>
    <div class="mb-6 text-center">
        <span class="text-[11px] font-bold text-[#e05c1a] uppercase tracking-widest block mb-1">Masuk ke Akun</span>
        <h1 class="font-serif-display font-bold text-2xl text-[#0f2255]">Selamat Datang Kembali</h1>
        <p class="text-xs text-gray-500 mt-1">Lanjutkan perjalanan membaca dan kelola pesanan Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

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
                autofocus
                autocomplete="username"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Kata Sandi <span class="text-red-500">*</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-[#e05c1a] hover:underline font-medium" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="block w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-[#fafaf8] focus:bg-white focus:outline-none focus:border-[#0f2255] transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#0f2255] shadow-sm focus:ring-0" name="remember">
                <span class="ms-2 text-xs text-gray-600">Ingat perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-[#0f2255] hover:bg-[#1a3380] text-white font-semibold text-xs sm:text-sm py-3 rounded-xl transition-all shadow-md">
                Masuk ke Akun Pembaca
            </button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-[#f0eee6] text-center text-xs text-gray-500">
        Belum memiliki akun di BOOKSPACE?
        <a href="{{ route('register') }}" class="font-bold text-[#e05c1a] hover:underline ml-1">Daftar sekarang</a>
    </div>

    {{-- Demo Credentials Helper for USK Examiner / Tester --}}
    <div class="mt-5 p-3.5 bg-[#f4f2ec] border border-[#e8e6df] rounded-xl text-[11px] text-gray-600 space-y-1">
        <p class="font-bold text-[#0f2255]">Akun Pengujian Penguji USK:</p>
        <p>• Admin: <code class="text-gray-900 font-mono font-semibold">admin@bookspace.id</code> / <code class="text-gray-900 font-mono">password</code></p>
        <p>• User: <code class="text-gray-900 font-mono font-semibold">user@bookspace.id</code> / <code class="text-gray-900 font-mono">password</code></p>
    </div>
</x-guest-layout>
