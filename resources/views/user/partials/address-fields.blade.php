@php
    $current = fn (string $key, $fallback = null) => old($key, $addr?->{$key} ?? $fallback);
    $scope = $addr?->id ?? 'new';
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="field-label" for="addr-label-{{ $scope }}">Label Alamat</label>
        <input type="text" id="addr-label-{{ $scope }}" name="label" value="{{ $current('label') }}"
            maxlength="60" placeholder="Rumah, Kantor, Titipan" class="field">
        @error('label') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="addr-recipient-{{ $scope }}">Nama Penerima <span class="text-[#b42318]">*</span></label>
        <input type="text" id="addr-recipient-{{ $scope }}" name="recipient_name" value="{{ $current('recipient_name') }}"
            required class="field">
        @error('recipient_name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="addr-phone-{{ $scope }}">Nomor WhatsApp / Telepon <span class="text-[#b42318]">*</span></label>
        <input type="tel" id="addr-phone-{{ $scope }}" name="phone" value="{{ $current('phone') }}"
            placeholder="081234567890" required class="field">
        @error('phone') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="addr-city-{{ $scope }}">Kota / Kabupaten <span class="text-[#b42318]">*</span></label>
        <input type="text" id="addr-city-{{ $scope }}" name="city" value="{{ $current('city') }}"
            placeholder="Kota Administrasi Jakarta Pusat" required class="field">
        @error('city') <p class="field-error">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="field-label" for="addr-street-{{ $scope }}">Alamat Lengkap <span class="text-[#b42318]">*</span></label>
    <textarea id="addr-street-{{ $scope }}" name="address" rows="3" required
        placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" class="field">{{ $current('address') }}</textarea>
    @error('address') <p class="field-error">{{ $message }}</p> @enderror
</div>

<div class="grid items-end gap-4 sm:grid-cols-2">
    <div>
        <label class="field-label" for="addr-postal-{{ $scope }}">Kode Pos</label>
        <input type="text" id="addr-postal-{{ $scope }}" name="postal_code" value="{{ $current('postal_code') }}"
            maxlength="10" placeholder="10310" class="field">
        @error('postal_code') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <label class="flex cursor-pointer items-center gap-2.5 sm:pb-2.5">
        <input type="checkbox" name="is_default" value="1" @checked($addr?->is_default ?? ($addr === null))
            class="rounded border-[#dcd8cd] text-[#0f2255] focus:ring-[#0f2255]">
        <span class="text-[13px] text-[#3f4451]">Jadikan alamat utama saat checkout</span>
    </label>
</div>

<div class="flex justify-end pt-1">
    <button type="submit" class="btn-primary btn-sm">
        {{ $addr ? 'Perbarui Alamat' : 'Simpan Alamat' }}
    </button>
</div>
