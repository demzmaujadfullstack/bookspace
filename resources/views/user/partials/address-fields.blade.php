@php
    $current = fn (string $key, $fallback = null) => old($key, $addr?->{$key} ?? $fallback);
@endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
            Label Alamat <span class="text-gray-400 font-normal">(Rumah, Kantor...)</span>
        </label>
        <input type="text" name="label" value="{{ $current('label') }}" maxlength="60"
            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">
        @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
            Nama Penerima <span class="text-red-500">*</span>
        </label>
        <input type="text" name="recipient_name" value="{{ $current('recipient_name') }}" required
            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">
        @error('recipient_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
            Nomor WhatsApp / Telepon <span class="text-red-500">*</span>
        </label>
        <input type="tel" name="phone" value="{{ $current('phone') }}" placeholder="081234567890" required
            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">
        @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
            Kota / Kabupaten <span class="text-red-500">*</span>
        </label>
        <input type="text" name="city" value="{{ $current('city') }}" placeholder="Kota Administrasi Jakarta Pusat" required
            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">
        @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
        Alamat Lengkap <span class="text-red-500">*</span>
    </label>
    <textarea name="address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"
        class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">{{ $current('address') }}</textarea>
    @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid sm:grid-cols-2 gap-4 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
            Kode Pos <span class="text-gray-400 font-normal">(Opsional)</span>
        </label>
        <input type="text" name="postal_code" value="{{ $current('postal_code') }}" maxlength="10" placeholder="10310"
            class="w-full text-xs sm:text-sm border border-[#dcd8cd] rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:border-[#0f2255] transition-all">
        @error('postal_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-2.5 cursor-pointer sm:pb-2.5">
        <input type="checkbox" name="is_default" value="1" @checked($addr?->is_default ?? ($addr === null))
            class="rounded border-[#dcd8cd] text-[#0f2255] focus:ring-[#0f2255]">
        <span class="text-xs text-gray-600">Jadikan alamat utama</span>
    </label>
</div>

<div class="flex justify-end gap-3 pt-1">
    <button type="submit" class="btn-primary text-xs py-2.5 px-6 rounded-lg shadow-sm">
        {{ $addr ? 'Perbarui Alamat' : 'Simpan Alamat' }}
    </button>
</div>
