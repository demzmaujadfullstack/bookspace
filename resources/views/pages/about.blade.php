@extends('layouts.bookspace')

@section('title', 'Tentang BOOKSPACE')

@section('content')
<div class="shell-narrow py-6 md:py-8">
    <x-breadcrumb :items="['Beranda' => route('home'), 'Tentang Kami' => null]" class="mb-5" />

    <div class="mb-8">
        <span class="eyebrow text-[#e05c1a]">Tentang Kami</span>
        <h1 class="page-title mt-2 max-w-3xl">
            Perpustakaan Digital yang Tumbuh dari Kebiasaan Membaca
        </h1>
        <p class="muted mt-3 max-w-2xl">
            BOOKSPACE lahir dari keresahan sederhana: mencari buku bagus di kota kecil sering berarti
            menelepon satu per satu toko, lalu kecewa karena stoknya sudah habis. Kami membangun katalog
            yang bisa dipercaya, sehingga pembaca cukup fokus pada satu hal — memilih buku berikutnya.
        </p>
    </div>

    <div class="mb-9 grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-[#e6e3db] bg-[#e6e3db] md:grid-cols-4">
        @foreach([
            ['label' => 'Judul Terdaftar', 'value' => $titleCount],
            ['label' => 'Kategori Buku', 'value' => $categoryCount],
            ['label' => 'Pesanan Dikirim', 'value' => $shipmentCount],
            ['label' => 'Ulasan Pembaca', 'value' => $reviewCount],
        ] as $stat)
            <div class="bg-white px-5 py-4">
                <p class="font-serif-display text-[24px] font-bold leading-none text-[#0f2255]">{{ number_format($stat['value']) }}</p>
                <p class="eyebrow mt-2">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="space-y-8">
        <section>
            <h2 class="mb-3 border-b border-[#e6e3db] pb-2 font-serif-display text-[18px] font-bold text-[#0f2255]">
                Cara Kami Bekerja
            </h2>
            <div class="max-w-2xl space-y-3 text-[13px] leading-relaxed text-[#3f4451]">
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
            <h2 class="mb-3 border-b border-[#e6e3db] pb-2 font-serif-display text-[18px] font-bold text-[#0f2255]">
                Prinsip Kami
            </h2>
            <ul class="max-w-2xl list-none space-y-2.5 text-[13px] leading-relaxed text-[#3f4451]">
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

        <section class="rounded-xl border border-[#e6e3db] bg-[#f4f2ec] p-5">
            <h2 class="mb-2 font-serif-display text-[16px] font-bold text-[#0f2255]">Catatan Pengembang</h2>
            <p class="max-w-2xl text-[13px] leading-relaxed text-[#3f4451]">
                BOOKSPACE adalah proyek uji kompetensi yang dibangun dengan Laravel, MySQL, dan Tailwind CSS.
                Rancangan antarmukanya meniru toko buku daring skala besar, namun seluruh datanya bersifat
                demonstratif dan pembayaran yang diterima tidak diproses melalui gerbang pembayaran sungguhan.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('books.index') }}" class="btn-primary btn-sm">Jelajahi Katalog</a>
                <a href="{{ route('pages.terms') }}" class="btn-ghost btn-sm">Syarat &amp; Ketentuan</a>
                <a href="{{ route('pages.privacy') }}" class="btn-ghost btn-sm">Kebijakan Privasi</a>
            </div>
        </section>
    </div>
</div>
@endsection
