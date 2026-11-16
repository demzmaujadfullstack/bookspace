@extends('layouts.bookspace')

@section('title', 'Tentang BOOKSPACE')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    <x-breadcrumbs :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Tentang Kami'],
    ]" />

    <div class="text-center mb-10">
        <span class="inline-block bg-[#f4f2ec] text-[#e05c1a] text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-widest mb-3">
            Tentang Kami
        </span>
        <h1 class="font-serif-display font-bold text-3xl md:text-4xl text-[#0f2255] leading-tight">
            Perpustakaan Digital yang Tumbuh dari Kebiasaan Membaca
        </h1>
        <p class="text-sm text-gray-500 mt-3 leading-relaxed max-w-2xl mx-auto">
            BOOKSPACE lahir dari keresahan sederhana: mencari buku bagus di kota kecil sering berarti
            menelepon satu per satu toko, lalu kecewa karena stoknya sudah habis. Kami membangun katalog
            yang bisa dipercaya, sehingga pembaca cukup fokus pada satu hal — memilih buku berikutnya.
        </p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 text-center shadow-sm">
            <p class="font-serif-display font-bold text-2xl text-[#0f2255]">{{ number_format($titleCount) }}</p>
            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mt-1">Judul Terdaftar</p>
        </div>
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 text-center shadow-sm">
            <p class="font-serif-display font-bold text-2xl text-[#0f2255]">{{ number_format($categoryCount) }}</p>
            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mt-1">Kategori Buku</p>
        </div>
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 text-center shadow-sm">
            <p class="font-serif-display font-bold text-2xl text-[#0f2255]">{{ number_format($shipmentCount) }}</p>
            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mt-1">Pesanan Dikirim</p>
        </div>
        <div class="bg-white border border-[#e8e6df] rounded-2xl p-5 text-center shadow-sm">
            <p class="font-serif-display font-bold text-2xl text-[#0f2255]">{{ number_format($reviewCount) }}</p>
            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-bold mt-1">Ulasan Pembaca</p>
        </div>
    </div>

    <div class="space-y-10">
        <section>
            <h2 class="font-serif-display font-bold text-xl text-[#0f2255] mb-3 pb-2 border-b border-[#e8e6df]">
                Cara Kami Bekerja
            </h2>
            <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                <p>
                    Setiap judul yang tampil di katalog memiliki data yang kami periksa sendiri: penerbit,
                    tahun terbit, ISBN, dan stok riil di rak. Ketika admin menyetujui pembayaran, sistem
                    memotong stok dalam satu transaksi database — jadi jumlah yang Anda lihat tidak akan
                    tiba-tiba minus saat checkout.
                </p>
                <p>
                    Setelah paket meninggalkan gudang, kami menuliskan nomor resi pada pesanan tersebut.
                    Anda dapat memantaunya di halaman <a href="{{ route('user.orders') }}" class="text-[#e05c1a] font-semibold hover:underline">Pesanan Saya</a>
                    dan menekan tombol konfirmasi begitu buku sampai di tangan Anda.
                </p>
            </div>
        </section>

        <section>
            <h2 class="font-serif-display font-bold text-xl text-[#0f2255] mb-3 pb-2 border-b border-[#e8e6df]">
                Prinsip Kami
            </h2>
            <ul class="text-sm text-gray-700 leading-relaxed space-y-2.5 list-none">
                <li class="flex gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#e05c1a] mt-2 shrink-0"></span>
                    <span><strong>Harga transparan.</strong> Diskon ditampilkan sejak kartu buku, tidak disimpan untuk kejutan di akhir pembayaran.</span>
                </li>
                <li class="flex gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#e05c1a] mt-2 shrink-0"></span>
                    <span><strong>Ulasan yang bisa dikoreksi.</strong> Pembaca dapat mengubah atau menghapus ratings dan ulasannya sendiri kapan saja, dan tim kami menindaklanjuti laporan ulasan yang tidak relevan.</span>
                </li>
                <li class="flex gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#e05c1a] mt-2 shrink-0"></span>
                    <span><strong>Tanya dulu, beli kemudian.</strong> Layanan <a href="{{ route('help.index') }}" class="text-[#e05c1a] font-semibold hover:underline">Pusat Bantuan</a> membuka obrolan langsung dengan tim kami, bukan formulir yang tidak pernah dibalas.</span>
                </li>
            </ul>
        </section>

        <section class="bg-[#f4f2ec] border border-[#e8e6df] rounded-2xl p-6">
            <h2 class="font-serif-display font-bold text-base text-[#0f2255] mb-2">Catatan Pengembang</h2>
            <p class="text-xs text-gray-600 leading-relaxed">
                BOOKSPACE adalah proyek uji kompetensi yang dibangun dengan Laravel, MySQL, dan Tailwind CSS.
                Rancangan antarmukanya meniru toko buku daring skala besar, namun seluruh datanya bersifat
                demonstratif dan pembayaran yang diterima tidak diproses melalui gerbang pembayaran sungguhan.
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                <a href="{{ route('books.index') }}" class="inline-block bg-[#0f2255] text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-[#e05c1a] transition-colors">Jelajahi Katalog</a>
                <a href="{{ route('pages.terms') }}" class="inline-block border border-[#dcd8cd] text-[#0f2255] text-xs font-semibold px-4 py-2 rounded-lg hover:border-[#0f2255] transition-colors">Syarat &amp; Ketentuan</a>
                <a href="{{ route('pages.privacy') }}" class="inline-block border border-[#dcd8cd] text-[#0f2255] text-xs font-semibold px-4 py-2 rounded-lg hover:border-[#0f2255] transition-colors">Kebijakan Privasi</a>
            </div>
        </section>
    </div>
</div>
@endsection
