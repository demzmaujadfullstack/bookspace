<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Koleksi Novel Terlaris 2024',
                'subtitle' => 'Temukan kisah-kisah terbaik dari penulis lokal dan internasional',
                'image' => 'banners/banner-1.jpg',
                'cta_text' => 'Lihat Koleksi',
                'cta_link' => '/books?category=novel',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Diskon Spesial Hingga 20%',
                'subtitle' => 'Dapatkan buku favorit Anda dengan harga terbaik, penawaran terbatas!',
                'image' => 'banners/banner-2.jpg',
                'cta_text' => 'Belanja Sekarang',
                'cta_link' => '/books?promo=1',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Buku Pengembangan Diri Pilihan',
                'subtitle' => 'Investasi terbaik adalah investasi pada diri sendiri',
                'image' => 'banners/banner-3.jpg',
                'cta_text' => 'Eksplorasi',
                'cta_link' => '/books?category=pengembangan-diri',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Koleksi Pendidikan Lengkap',
                'subtitle' => 'Buku pelajaran berkualitas untuk semua jenjang pendidikan',
                'image' => 'banners/banner-4.jpg',
                'cta_text' => 'Lihat Buku',
                'cta_link' => '/books?category=pendidikan',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Dunia Teknologi & Komputer',
                'subtitle' => 'Kuasai skill digital masa depan bersama buku-buku terpilih kami',
                'image' => 'banners/banner-5.jpg',
                'cta_text' => 'Temukan Sekarang',
                'cta_link' => '/books?category=teknologi-komputer',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $data) {
            Banner::create($data);
        }
    }
}
