@extends('layouts.bookspace')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="shell-narrow py-6 md:py-8">
    <x-breadcrumb :items="['Beranda' => route('home'), 'Kebijakan Privasi' => null]" class="mb-5" />

    <div class="mb-7">
        <h1 class="page-title">Kebijakan Privasi</h1>
        <p class="mt-2 font-mono text-[12px] text-[#9a9484]">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
        <p class="muted mt-3 max-w-2xl">
            Dokumen ini menjelaskan data apa saja yang BOOKSPACE kumpulkan saat Anda memakai katalog,
            keranjang, dan layanan pesanan kami, serta apa yang dapat Anda lakukan terhadap data tersebut.
            Kami berusaha menulisnya sesingkat mungkin tanpa menyembunyikan bagian yang penting.
        </p>
    </div>

    <div class="space-y-4 text-[13px] leading-relaxed text-[#3f4451]">
        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">1. Data yang Kami Simpan</h2>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong>Data akun:</strong> nama, alamat email, dan nomor telepon yang Anda isikan saat registrasi. Kata sandi disimpan sebagai hash, bukan teks asli.</li>
                <li><strong>Data transaksi:</strong> alamat pengiriman, daftar judul yang dipesan, metode pembayaran, dan status verifikasi. Alamat dibekukan pada saat checkout agar riwayat pesanan tidak berubah bila Anda mengedit data akun.</li>
                <li><strong>Data konten:</strong> ulasan dan rating yang Anda tulis, daftar favorit, serta isi obrolan di Pusat Bantuan.</li>
                <li><strong>Bukti pembayaran:</strong> berkas yang Anda unggah disimpan di luar folder publik dan hanya dapat dibuka oleh pemilik pesanan dan admin.</li>
            </ul>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">2. Alasan Kami Membutuhkannya</h2>
            <p>
                Data tersebut dipakai untuk tiga hal saja: memproses dan mengirim pesanan, menunjukkan
                riwayat transaksi Anda, serta menjawab pertanyaan melalui layanan pelanggan. Kami tidak
                menjual data pembeli dan tidak menayangkannya kepada pihak ketiga untuk keperluan iklan.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">3. Siapa yang Bisa Melihat Data Anda</h2>
            <p>
                Admin BOOKSPACE dapat membuka rincian pesanan untuk mencocokkan pembayaran dan menyiapkan
                pengiriman. Nomor resi dan status pesanan hanya tampil pada akun Anda sendiri. Setiap
                perubahan status besar dicatat sebagai riwayat pergerakan stok supaya dapat ditelusuri
                kembali bila terjadi perbedaan.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">4. Menyimpan dan Menghapus Data</h2>
            <p>
                Anda dapat menghapus akun dari halaman
                <a href="{{ route('user.profile.edit') }}" class="text-[#e05c1a] font-semibold hover:underline">Profil</a>.
                Akun yang dihapus ditandai sebagai <em>soft delete</em>, sehingga transaksi yang sudah selesai
                tetap tercatat untuk keperluan pembukuan, tetapi data pribadi Anda tidak lagi dapat diakses
                melalui halaman mana pun. Riwayat obrolan dan ulasan ikut dihapus dari tampilan publik.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">5. Cookie dan Sesi</h2>
            <p>
                Kami memakai cookie sesi untuk mengenali Anda setelah masuk, menjaga isi keranjang tetap
                ada, dan melindungi formulir dari permintaan palsu (CSRF). Tidak ada pelacak pihak ketiga
                yang dipasang pada halaman katalog.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">6. Hubungi Kami</h2>
            <p>
                Pertanyaan seputar privasi data dapat dikirim lewat
                <a href="{{ route('help.index') }}" class="text-[#e05c1a] font-semibold hover:underline">Pusat Bantuan</a>.
                Kami menargetkan balasan dalam satu hari kerja.
            </p>
        </section>
    </div>
</div>
@endsection
