@extends('layouts.bookspace')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 md:py-12">
    <x-breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Kebijakan Privasi'],
    ]" />

    <div class="mb-8">
        <h1 class="font-serif-display font-bold text-3xl text-[#0f2255] leading-tight">Kebijakan Privasi</h1>
        <p class="text-xs text-gray-400 mt-2 font-mono">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
        <p class="text-sm text-gray-600 mt-4 leading-relaxed">
            Dokumen ini menjelaskan data apa saja yang BOOKSPACE kumpulkan saat Anda memakai katalog,
            keranjang, dan layanan pesanan kami, serta apa yang dapat Anda lakukan terhadap data tersebut.
            Kami berusaha menulisnya sesingkat mungkin tanpa menyembunyikan bagian yang penting.
        </p>
    </div>

    <div class="space-y-7 text-sm text-gray-700 leading-relaxed">
        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">1. Data yang Kami Simpan</h2>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong>Data akun:</strong> nama, alamat email, dan nomor telepon yang Anda isikan saat registrasi. Kata sandi disimpan sebagai hash, bukan teks asli.</li>
                <li><strong>Data transaksi:</strong> alamat pengiriman, daftar judul yang dipesan, metode pembayaran, dan status verifikasi. Alamat dibekukan pada saat checkout agar riwayat pesanan tidak berubah bila Anda mengedit data akun.</li>
                <li><strong>Data konten:</strong> ulasan dan rating yang Anda tulis, daftar favorit, serta isi obrolan di Pusat Bantuan.</li>
                <li><strong>Bukti pembayaran:</strong> berkas yang Anda unggah disimpan di luar folder publik dan hanya dapat dibuka oleh pemilik pesanan dan admin.</li>
            </ul>
        </section>

        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">2. Alasan Kami Membutuhkannya</h2>
            <p>
                Data tersebut dipakai untuk tiga hal saja: memproses dan mengirim pesanan, menunjukkan
                riwayat transaksi Anda, serta menjawab pertanyaan melalui layanan pelanggan. Kami tidak
                menjual data pembeli dan tidak menayangkannya kepada pihak ketiga untuk keperluan iklan.
            </p>
        </section>

        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">3. Siapa yang Bisa Melihat Data Anda</h2>
            <p>
                Admin BOOKSPACE dapat membuka rincian pesanan untuk mencocokkan pembayaran dan menyiapkan
                pengiriman. Nomor resi dan status pesanan hanya tampil pada akun Anda sendiri. Setiap
                perubahan status besar dicatat sebagai riwayat pergerakan stok supaya dapat ditelusuri
                kembali bila terjadi perbedaan.
            </p>
        </section>

        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">4. Menyimpan dan Menghapus Data</h2>
            <p>
                Anda dapat menghapus akun dari halaman
                <a href="{{ route('user.profile.edit') }}" class="text-[#e05c1a] font-semibold hover:underline">Profil</a>.
                Akun yang dihapus ditandai sebagai <em>soft delete</em>, sehingga transaksi yang sudah selesai
                tetap tercatat untuk keperluan pembukuan, tetapi data pribadi Anda tidak lagi dapat diakses
                melalui halaman mana pun. Riwayat obrolan dan ulasan ikut dihapus dari tampilan publik.
            </p>
        </section>

        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">5. Cookie dan Sesi</h2>
            <p>
                Kami memakai cookie sesi untuk mengenali Anda setelah masuk, menjaga isi keranjang tetap
                ada, dan melindungi formulir dari permintaan palsu (CSRF). Tidak ada pelacak pihak ketiga
                yang dipasang pada halaman katalog.
            </p>
        </section>

        <section class="bg-white border border-[#e8e6df] rounded-2xl p-6 shadow-sm">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">6. Hubungi Kami</h2>
            <p>
                Pertanyaan seputar privasi data dapat dikirim lewat
                <a href="{{ route('help.index') }}" class="text-[#e05c1a] font-semibold hover:underline">Pusat Bantuan</a>.
                Kami menargetkan balasan dalam satu hari kerja.
            </p>
        </section>
    </div>
</div>
@endsection
