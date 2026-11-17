@extends('layouts.bookspace')

@section('title', 'Profil & Alamat Pengiriman')

@section('content')
@php
    $openEditId = (int) old('address_id') ?: null;
    $showAddForm = $openEditId ? false : (bool) $errors->first('recipient_name');
@endphp

<div class="shell py-6 md:py-8">

    <div class="mb-6">
        <x-breadcrumb :items="['Beranda' => route('home'), 'Akun Saya' => route('user.dashboard'), 'Profil & Alamat' => null]" class="mb-4" />
        <h1 class="page-title">Profil &amp; Alamat Pengiriman</h1>
        <p class="muted mt-1">Perbarui data akun, ganti kata sandi, dan kelola alamat yang dipakai saat checkout.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
        <x-account-nav :user="$user" class="lg:col-span-4 xl:col-span-3" />

        <div class="space-y-6 lg:col-span-8 xl:col-span-9">

            {{-- ── Data akun ─────────────────────────────────── --}}
            <section class="card">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Data Akun</h2>
                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">Nama dan email ini yang tampil di faktur pesanan serta konfirmasi pengiriman.</p>
                </header>

                <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4 p-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="profile-name">Nama Lengkap <span class="text-[#b42318]">*</span></label>
                            <input type="text" id="profile-name" name="name" value="{{ old('name', $user->name) }}" required class="field">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label" for="profile-phone">Nomor WhatsApp / HP</label>
                            <input type="tel" id="profile-phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                placeholder="081234567890" class="field">
                            @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="field-label" for="profile-email">Alamat Email <span class="text-[#b42318]">*</span></label>
                        <input type="email" id="profile-email" name="email" value="{{ old('email', $user->email) }}" required class="field">
                        @error('email') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="mt-1.5 text-[12px] text-[#9a9484]">Email dipakai untuk notifikasi status pesanan dan pemulihan akun.</p>
                    </div>

                    <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-[#f0eee6] pt-4">
                        <p class="text-[12px] text-[#9a9484]">Terdaftar sejak {{ $user->created_at->translatedFormat('F Y') }}</p>
                        <button type="submit" class="btn-primary btn-sm">Simpan Perubahan</button>
                    </footer>
                </form>
            </section>

            {{-- ── Keamanan akun ─────────────────────────────── --}}
            <section class="card">
                <header class="border-b border-[#e6e3db] px-5 py-4">
                    <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Keamanan Akun</h2>
                    <p class="mt-0.5 text-[12px] text-[#6b6f78]">Gunakan kata sandi berbeda dari akun lain. Ganti berkala bila perlu.</p>
                </header>

                <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-4 p-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="field-label" for="current-password">Kata Sandi Saat Ini <span class="text-[#b42318]">*</span></label>
                        <input type="password" id="current-password" name="current_password" autocomplete="current-password"
                            class="field sm:max-w-sm">
                        @error('current_password') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="new-password">Kata Sandi Baru <span class="text-[#b42318]">*</span></label>
                            <input type="password" id="new-password" name="password" autocomplete="new-password" class="field">
                            @error('password') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label" for="new-password-confirmation">Ulangi Kata Sandi Baru <span class="text-[#b42318]">*</span></label>
                            <input type="password" id="new-password-confirmation" name="password_confirmation" autocomplete="new-password" class="field">
                        </div>
                    </div>

                    <footer class="flex items-center justify-end border-t border-[#f0eee6] pt-4">
                        <button type="submit" class="btn-ghost btn-sm">Ganti Kata Sandi</button>
                    </footer>
                </form>
            </section>

            {{-- ── Buku alamat ───────────────────────────────── --}}
            <section id="buku-alamat" class="card"
                x-data="{ editId: {{ $openEditId ?? 'null' }}, showAdd: {{ $showAddForm ? 'true' : 'false' }} }">
                <header class="flex flex-wrap items-start justify-between gap-3 border-b border-[#e6e3db] px-5 py-4">
                    <div>
                        <h2 class="font-serif-display text-[16px] font-bold text-[#0f2255]">Buku Alamat Pengiriman</h2>
                        <p class="mt-0.5 text-[12px] text-[#6b6f78]">Simpan alamat rumah atau kantor supaya checkout tinggal pilih, tidak perlu menulis ulang.</p>
                    </div>
                    <button type="button" @click="showAdd = !showAdd; editId = null" class="btn-outline btn-sm shrink-0">
                        <span x-show="!showAdd">+ Tambah Alamat</span>
                        <span x-show="showAdd" x-cloak>Tutup</span>
                    </button>
                </header>

                <div class="space-y-4 p-5">
                    @forelse($addresses as $savedAddress)
                        <article class="rounded-lg border p-4 {{ $savedAddress->is_default ? 'border-[#0f2255]/25 bg-[#f7f8fc]' : 'border-[#e6e3db] bg-[#fafaf8]' }}">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-[14px] font-bold text-[#17181c]">{{ $savedAddress->label ?: 'Alamat Tersimpan' }}</h3>
                                        @if($savedAddress->is_default)
                                            <span class="pill bg-[#0f2255] text-white">Alamat Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-1.5 text-[13px] font-semibold text-[#3f4451]">
                                        {{ $savedAddress->recipient_name }} &middot; {{ $savedAddress->phone }}
                                    </p>
                                    <p class="mt-1 max-w-xl text-[13px] leading-relaxed text-[#6b6f78]">
                                        {{ $savedAddress->address }}@if($savedAddress->postal_code), {{ $savedAddress->postal_code }}@endif
                                    </p>
                                    @if($savedAddress->city)
                                        <p class="text-[13px] text-[#6b6f78]">{{ $savedAddress->city }}</p>
                                    @endif
                                </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2">
                                    @unless($savedAddress->is_default)
                                        <form method="POST" action="{{ route('user.addresses.default', $savedAddress) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-ghost btn-sm">Jadikan Utama</button>
                                        </form>
                                    @endunless
                                    <button type="button"
                                        @click="editId = editId === {{ $savedAddress->id }} ? null : {{ $savedAddress->id }}; showAdd = false"
                                        class="btn-ghost btn-sm">Ubah</button>
                                    <form method="POST" action="{{ route('user.addresses.destroy', $savedAddress) }}"
                                        onsubmit="return confirm('Hapus alamat ini dari buku alamat Anda?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost btn-sm !border-transparent !bg-transparent !text-[#b42318] hover:!bg-[#fdecea]">Hapus</button>
                                    </form>
                                </div>
                            </div>

                            <div x-show="editId === {{ $savedAddress->id }}" x-cloak x-transition class="mt-4 space-y-4 border-t border-[#e6e3db] pt-4">
                                <form method="POST" action="{{ route('user.addresses.update', $savedAddress) }}" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="address_id" value="{{ $savedAddress->id }}">
                                    @include('user.partials.address-fields', ['addr' => $savedAddress])
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-lg border border-dashed border-[#dcd8cd] bg-[#fafaf8] px-4 py-6 text-center text-[13px] text-[#6b6f78]">
                            Belum ada alamat tersimpan. Tekan <span class="font-semibold text-[#0f2255]">+ Tambah Alamat</span> untuk menyimpan alamat pertama Anda.
                        </p>
                    @endforelse

                    <div x-show="showAdd" x-cloak x-transition>
                        <form method="POST" action="{{ route('user.addresses.store') }}"
                            class="space-y-4 rounded-lg border border-[#0f2255]/20 bg-[#f7f8fc] p-4">
                            @csrf
                            @include('user.partials.address-fields', ['addr' => null])
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
