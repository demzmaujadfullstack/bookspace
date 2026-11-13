<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Novel', 'description' => 'Karya fiksi naratif panjang yang mengangkat berbagai tema kehidupan'],
            ['name' => 'Pengembangan Diri', 'description' => 'Buku-buku yang membantu meningkatkan kualitas diri dan produktivitas'],
            ['name' => 'Pendidikan', 'description' => 'Buku pelajaran dan referensi untuk semua jenjang pendidikan'],
            ['name' => 'Bisnis & Ekonomi', 'description' => 'Panduan bisnis, keuangan, dan ekonomi untuk para profesional'],
            ['name' => 'Teknologi & Komputer', 'description' => 'Buku pemrograman, teknologi informasi, dan dunia digital'],
            ['name' => 'Sejarah & Budaya', 'description' => 'Eksplorasi sejarah dan kekayaan budaya Nusantara dan dunia'],
            ['name' => 'Sains & Alam', 'description' => 'Ilmu pengetahuan alam, fisika, kimia, biologi, dan astronomi'],
            ['name' => 'Agama & Spiritualitas', 'description' => 'Buku keagamaan dan panduan spiritual dari berbagai perspektif'],
            ['name' => 'Anak & Remaja', 'description' => 'Bacaan edukatif dan menghibur untuk anak-anak dan remaja'],
            ['name' => 'Komik & Manga', 'description' => 'Koleksi komik lokal dan manga Jepang terpopuler'],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'],
                ]
            );
        }
    }
}
