<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            // Novel
            [
                'category' => 'Novel',
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'isbn' => '9789793062792',
                'year' => 2005,
                'description' => 'Novel inspiratif tentang perjuangan anak-anak Belitung dalam meraih mimpi di tengah keterbatasan. Kisah persahabatan, semangat belajar, dan keajaiban hidup yang mengharukan.',
                'price' => 95000,
                'discount_percent' => 10,
                'stock' => 45,
                'is_featured' => true,
            ],
            [
                'category' => 'Novel',
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'publisher' => 'Lentera Dipantara',
                'isbn' => '9789794175842',
                'year' => 1980,
                'description' => 'Karya masterpiece Pramoedya yang mengisahkan perjuangan Minke di masa kolonial Belanda. Novel pertama dari Tetralogi Buru yang menggugah nurani tentang kemanusiaan.',
                'price' => 110000,
                'discount_percent' => 0,
                'stock' => 30,
                'is_featured' => true,
            ],
            [
                'category' => 'Novel',
                'title' => 'Ayah',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'isbn' => '9786022910602',
                'year' => 2015,
                'description' => 'Kisah cinta seorang ayah yang sangat menyentuh hati, penuh dengan keajaiban kecil yang membuat hidup menjadi bermakna.',
                'price' => 88000,
                'discount_percent' => 15,
                'stock' => 20,
                'is_featured' => false,
            ],
            [
                'category' => 'Novel',
                'title' => 'The Alchemist',
                'author' => 'Paulo Coelho',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9789792241372',
                'year' => 1988,
                'description' => 'Novel filosifis tentang seorang anak gembala dari Spanyol yang berkelana mencari harta karun. Sebuah kisah tentang keberanian mengikuti mimpi dan menemukan makna kehidupan.',
                'price' => 79000,
                'discount_percent' => 20,
                'stock' => 60,
                'is_featured' => true,
            ],
            [
                'category' => 'Novel',
                'title' => 'Dilan 1990',
                'author' => 'Pidi Baiq',
                'publisher' => 'Mizan Pustaka',
                'isbn' => '9786022911937',
                'year' => 2014,
                'description' => 'Kisah cinta remaja yang manis dan penuh kenangan di Bandung tahun 1990. Novel yang berhasil mencuri hati jutaan pembaca Indonesia.',
                'price' => 72000,
                'discount_percent' => 0,
                'stock' => 35,
                'is_featured' => false,
            ],
            // Pengembangan Diri
            [
                'category' => 'Pengembangan Diri',
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9786020638188',
                'year' => 2018,
                'description' => 'Panduan praktis membangun kebiasaan baik dan menghilangkan kebiasaan buruk. Buku ini mengajarkan cara membuat perubahan kecil yang memberikan hasil luar biasa.',
                'price' => 128000,
                'discount_percent' => 10,
                'stock' => 80,
                'is_featured' => true,
            ],
            [
                'category' => 'Pengembangan Diri',
                'title' => 'The Power of Now',
                'author' => 'Eckhart Tolle',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9786020349305',
                'year' => 1997,
                'description' => 'Panduan menuju pencerahan spiritual dengan hidup di masa kini. Buku yang mengubah perspektif jutaan orang tentang ketenangan batin.',
                'price' => 95000,
                'discount_percent' => 0,
                'stock' => 25,
                'is_featured' => false,
            ],
            [
                'category' => 'Pengembangan Diri',
                'title' => 'Rich Dad Poor Dad',
                'author' => 'Robert T. Kiyosaki',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9789792200577',
                'year' => 1997,
                'description' => 'Pelajaran tentang uang yang tidak diajarkan di sekolah. Panduan membangun kebebasan finansial melalui investasi dan kecerdasan finansial.',
                'price' => 109000,
                'discount_percent' => 5,
                'stock' => 50,
                'is_featured' => true,
            ],
            // Teknologi
            [
                'category' => 'Teknologi & Komputer',
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'publisher' => 'Informatika',
                'isbn' => '9780132350884',
                'year' => 2008,
                'description' => 'Panduan menulis kode yang bersih, mudah dibaca, dan mudah di-maintain. Wajib baca bagi setiap programmer profesional.',
                'price' => 185000,
                'discount_percent' => 0,
                'stock' => 15,
                'is_featured' => true,
            ],
            [
                'category' => 'Teknologi & Komputer',
                'title' => 'Laravel: Up & Running',
                'author' => 'Matt Stauffer',
                'publisher' => 'O\'Reilly Media',
                'isbn' => '9781492041214',
                'year' => 2019,
                'description' => 'Panduan lengkap membangun aplikasi web modern dengan Laravel framework. Cocok untuk pemula hingga developer berpengalaman.',
                'price' => 220000,
                'discount_percent' => 15,
                'stock' => 10,
                'is_featured' => false,
            ],
            // Pendidikan
            [
                'category' => 'Pendidikan',
                'title' => 'Matematika untuk SMA Kelas XII',
                'author' => 'Sartono Wirodikromo',
                'publisher' => 'Erlangga',
                'isbn' => '9789797535919',
                'year' => 2020,
                'description' => 'Buku pelajaran matematika lengkap dengan contoh soal dan pembahasan. Disusun sesuai kurikulum terbaru untuk persiapan ujian nasional.',
                'price' => 65000,
                'discount_percent' => 0,
                'stock' => 100,
                'is_featured' => false,
            ],
            [
                'category' => 'Pendidikan',
                'title' => 'Fisika Dasar',
                'author' => 'Halliday & Resnick',
                'publisher' => 'Erlangga',
                'isbn' => '9789797580292',
                'year' => 2010,
                'description' => 'Referensi fisika paling komprehensif untuk mahasiswa teknik dan sains. Dilengkapi dengan ribuan soal dan contoh aplikasi nyata.',
                'price' => 195000,
                'discount_percent' => 10,
                'stock' => 22,
                'is_featured' => false,
            ],
            // Bisnis
            [
                'category' => 'Bisnis & Ekonomi',
                'title' => 'Zero to One',
                'author' => 'Peter Thiel',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9780804139021',
                'year' => 2014,
                'description' => 'Panduan membangun startup dari nol. Peter Thiel berbagi rahasia di balik kesuksesan PayPal dan filosofi inovasi yang mengubah dunia.',
                'price' => 98000,
                'discount_percent' => 20,
                'stock' => 40,
                'is_featured' => true,
            ],
            [
                'category' => 'Bisnis & Ekonomi',
                'title' => 'Good to Great',
                'author' => 'Jim Collins',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9780066620992',
                'year' => 2001,
                'description' => 'Riset mendalam tentang apa yang membuat perusahaan biasa menjadi luar biasa. Pelajaran berharga dari perusahaan-perusahaan terbaik dunia.',
                'price' => 115000,
                'discount_percent' => 0,
                'stock' => 18,
                'is_featured' => false,
            ],
            // Sejarah
            [
                'category' => 'Sejarah & Budaya',
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'isbn' => '9786024247058',
                'year' => 2011,
                'description' => 'Perjalanan luar biasa spesies Homo sapiens dari zaman batu hingga era digital. Buku yang mengubah cara kita memandang sejarah manusia.',
                'price' => 148000,
                'discount_percent' => 10,
                'stock' => 55,
                'is_featured' => true,
            ],
            // Anak
            [
                'category' => 'Anak & Remaja',
                'title' => 'Harry Potter dan Batu Bertuah',
                'author' => 'J.K. Rowling',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9789792232653',
                'year' => 1997,
                'description' => 'Petualangan ajaib Harry Potter di Hogwarts School of Witchcraft and Wizardry. Seri fantasi yang telah memikat hati jutaan pembaca di seluruh dunia.',
                'price' => 125000,
                'discount_percent' => 0,
                'stock' => 70,
                'is_featured' => true,
            ],
            [
                'category' => 'Anak & Remaja',
                'title' => 'Si Kancil dan Teman-temannya',
                'author' => 'Tim Redaksi Erlangga',
                'publisher' => 'Erlangga',
                'isbn' => '9789797580019',
                'year' => 2022,
                'description' => 'Kumpulan cerita rakyat Indonesia yang kaya pesan moral untuk anak-anak. Kisah-kisah klasik yang mengajarkan keberanian, kejujuran, dan persahabatan.',
                'price' => 45000,
                'discount_percent' => 0,
                'stock' => 85,
                'is_featured' => false,
            ],
            // Agama
            [
                'category' => 'Agama & Spiritualitas',
                'title' => 'La Tahzan',
                'author' => 'Dr. Aidh Al-Qarni',
                'publisher' => 'Qisthi Press',
                'isbn' => '9789793226552',
                'year' => 2003,
                'description' => 'Jangan bersedih! Sebuah buku motivasi Islami yang telah membantu jutaan orang menemukan ketenangan hati dan optimisme dalam menjalani kehidupan.',
                'price' => 87000,
                'discount_percent' => 5,
                'stock' => 42,
                'is_featured' => false,
            ],
            // Komik
            [
                'category' => 'Komik & Manga',
                'title' => 'One Piece Vol. 1',
                'author' => 'Eiichiro Oda',
                'publisher' => 'Elex Media Komputindo',
                'isbn' => '9789792754469',
                'year' => 1997,
                'description' => 'Petualangan Monkey D. Luffy bersama kru Topi Jerami dalam pencarian harta karun legendaris One Piece. Manga paling populer sepanjang masa.',
                'price' => 32000,
                'discount_percent' => 0,
                'stock' => 120,
                'is_featured' => false,
            ],
            [
                'category' => 'Sains & Alam',
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'publisher' => 'Gramedia Pustaka Utama',
                'isbn' => '9780553380163',
                'year' => 1988,
                'description' => 'Perjalanan menakjubkan dari Big Bang hingga lubang hitam, dari waktu ke waktu. Stephen Hawking menjelaskan misteri alam semesta dengan cara yang memukau.',
                'price' => 108000,
                'discount_percent' => 0,
                'stock' => 3,
                'is_featured' => true,
            ],
        ];

        foreach ($books as $data) {
            $category = Category::where('name', $data['category'])->first();
            if (! $category) {
                continue;
            }

            $slug = Str::slug($data['title']);
            $counter = 1;
            $originalSlug = $slug;
            while (Book::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }

            Book::updateOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'category_id' => $category->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'author' => $data['author'],
                    'publisher' => $data['publisher'],
                    'isbn' => $data['isbn'],
                    'year' => $data['year'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'discount_percent' => $data['discount_percent'],
                    'stock' => $data['stock'],
                    'is_featured' => $data['is_featured'],
                ]
            );
        }
    }
}
