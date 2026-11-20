@extends('layouts.bookspace')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="shell-narrow py-6 md:py-8">
    <x-breadcrumb :items="['Beranda' => route('home'), 'Syarat & Ketentuan' => null]" class="mb-5" />

    <div class="mb-7">
        <h1 class="page-title">Syarat &amp; Ketentuan</h1>
        <p class="mt-2 font-mono text-[12px] text-[#9a9484]">Berlaku sejak {{ now()->translatedFormat('d F Y') }}</p>
        <p class="muted mt-3 max-w-2xl">
            Dengan membuat akun dan melakukan pemesanan di BOOKSPACE, Anda menyetujui aturan di bawah ini.
            Bacalah bagian status pesanan dan pembatalan dengan teliti karena keduanya menentukan apa yang
            dapat Anda minta bila sesuatu tidak berjalan sesuai harapan.
        </p>
    </div>

    <div class="space-y-4 text-[13px] leading-relaxed text-[#3f4451]">
        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">1. Akun dan Ketentuan Pembayaran</h2>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Satu alamat email dipakai untuk satu akun; gunakan data penerima yang benar agar paket tidak tertahan di kurir.</li>
                <li>Harga yang tampil sudah memperhitungkan diskon judul terkait dan belum termasuk ongkos kirim, kecuali total belanja Anda melewati ambang gratis ongkir yang berlaku di halaman keranjang.</li>
                <li>Transfer manual dan QRIS sama-sama memerlukan pencocokan oleh admin sebelum pesanan diproses.</li>
            </ul>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">2. Alur Status Pesanan</h2>
            <p class="mb-3">Pesanan bergerak berurutan melalui status berikut:</p>
            <ol class="space-y-2.5">
                @foreach ([
                    ['Menunggu Pembayaran', 'Anda sudah checkout dan belum mengunggah bukti transfer.'],
                    ['Menunggu Verifikasi', 'Bukti pembayaran terkirim, tim kami sedang mencocokkan mutasi.'],
                    ['Dibayar', 'Pembayaran disetujui dan stok buku resmi terpotong.'],
                    ['Diproses', 'Buku sedang dikemas di gudang.'],
                    ['Dikirim', 'Paket berada di tangan ekspedisi; nomor resi tampil di halaman pesanan Anda.'],
                    ['Selesai', 'Anda menekan tombol konfirmasi barang diterima, atau admin menutup pesanan.'],
                ] as $i => [$label, $desc])
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#0f2255] text-white text-[11px] font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                        <span><strong class="font-semibold text-[#17181c]">{{ $label }}</strong> — {{ $desc }}</span>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">3. Pembatalan dan Pengembalian</h2>
            <p>
                Pesanan masih dapat Anda batalkan sendiri selama statusnya berada pada tahap
                Menunggu Pembayaran atau Menunggu Verifikasi. Setelah pembayaran disetujui dan stok
                terpotong, pembatalan perlu dibicarakan melalui Pusat Bantuan agar buku yang sudah
                dikemas dapat dihentikan sebelum dikirim. Saat pesanan dibatalkan, jumlah stok buku
                kembali seperti semula secara otomatis.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">4. Tanggung Jawab Anda sebagai Pembeli</h2>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>Memeriksa kembali judul, jumlah eksemplar, dan alamat pada halaman checkout sebelum menekan tombol pesan.</li>
                <li>Mengunggah bukti transfer yang sesuai dengan nominal tagihan; bukti dengan nominal berbeda akan ditolak dan status pesanan berubah menjadi Dibatalkan.</li>
                <li>Menekan konfirmasi <strong>Pesanan Sudah Diterima</strong> hanya bila seluruh buku benar-benar sudah ada di tangan Anda, karena status Selesai menutup hak komplain pengiriman.</li>
            </ul>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">5. Ulasan dan Konten Pembaca</h2>
            <p>
                Ulasan yang Anda tulis menjadi bagian dari informasi katalog. Anda bebas mengubah atau
                menghapusnya kapan saja dari halaman detail buku. Kami dapat menghapus ulasan yang memuat
                ujaran kebencian, tautan penipuan, atau spoiler berat tanpa pemberitahuan lebih dulu.
            </p>
        </section>

        <section class="card card-pad">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">6. Batas Layanan</h2>
            <p>
                BOOKSPACE berupaya menjaga katalog tetap tersedia, tetapi kami tidak menjamin layanan bebas
                gangguan sepanjang waktu. Pemeliharaan berkala dapat membuat halaman tidak dapat diakses
                sementara. Ketentuan yang bertentangan dengan hukum yang berlaku di Indonesia tidak
                kami berlakukan.
            </p>
        </section>

        <div class="rounded-xl border border-[#e6e3db] bg-[#f4f2ec] p-5 text-center">
            <p class="mb-4 text-[13px] text-[#3f4451]">Masih ada bagian yang kurang jelas? Tanyakan langsung sebelum Anda memesan.</p>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="{{ route('help.index') }}" class="btn-primary btn-sm">Hubungi Pusat Bantuan</a>
                <a href="{{ route('pages.privacy') }}" class="btn-ghost btn-sm">Baca Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</div>
@endsection
