<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * Ulasan contoh hanya untuk pembaca yang pesannya benar-benar sampai,
 * supaya rating katalog tidak berasal dari data acak.
 */
class ReviewSeeder extends Seeder
{
    private const MAX_REVIEWS = 40;

    private const COMMENTS = [
        [5, 'Bahasa pengarangnya mengalir, bab pendeknya bikin sulit berhenti baca. Sekali duduk langsung tamat.'],
        [5, 'Kertasnya bagus dan jilidnya kuat, layak disimpan di rak. Pengiriman juga cepat, kondisi buku mulus.'],
        [4, 'Konsepnya menarik dan penjelasannya runtut. Sayang bagian tengah agak lambat, tapi tetap layak dibaca.'],
        [4, 'Saya baca dua kali dan baru paham maksud penulis di bagian penutup. Worth the re-read.'],
        [5, 'Contoh latihannya praktis, langsung bisa dipakai di pekerjaan. Bonus: indeksnya rapi.'],
        [3, 'Isi bukunya bagus, tapi terjemahannya terasa kaku di beberapa kalimat. Masih bisa dinikmati.'],
        [4, 'Cocok untuk pemula karena definisinya dijelaskan pelan-pelan. Untuk yang sudah paham terasa dasar.'],
        [5, 'Sudut pandang karakternya unik dan tidak mudah ditebak. Penokohannya yang paling saya suka.'],
        [4, 'Datanya lengkap dan bersumber jelas, enak dipakai referensi. Desain dalam bukunya bisa lebih lega.'],
        [2, 'Alurnya terlalu lambat di awal; saya hampir berhenti di bab tiga. Baru menarik setelahnya.'],
        [5, 'Harga lebih murah dibanding toko fisik, dapat diskon lagi. Bukunya orisinal dan segel rapi.'],
        [4, 'Ringkas dan to the point, cocok untuk baca santai sore. Sedikit mengulang di bab akhir.'],
        [3, 'Tema yang diangkat jarang dibahas penulis lain, sayang endingnya terburu-buru.'],
        [5, 'Buku ketiga yang saya pesan di sini dan kualitasnya konsisten. Admin cepat balas chat.'],
        [4, 'Ilustrasinya cantik dan cetakannya tajam. Anak saya jadi suka dibacakan sebelum tidur.'],
    ];

    public function run(): void
    {
        $candidates = OrderItem::query()
            ->whereIn('order_id', function ($query) {
                $query->select('id')
                    ->from('orders')
                    ->whereIn('status', [Order::DIKIRIM, Order::SELESAI]);
            })
            ->with('order')
            ->orderBy('id')
            ->get();

        $created = 0;

        foreach ($candidates as $item) {
            if ($created >= self::MAX_REVIEWS) {
                break;
            }

            $userId = $item->order?->user_id;

            if (! $userId || Review::where('user_id', $userId)->where('book_id', $item->book_id)->exists()) {
                continue;
            }

            [$rating, $comment] = self::COMMENTS[$item->id % count(self::COMMENTS)];

            $review = Review::create([
                'user_id' => $userId,
                'book_id' => $item->book_id,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            // Sebar tanggal ulasan agar daftar "terbaru" tidak tersusun seragam.
            $review->created_at = now()->subDays($item->id % 90)->subMinutes($item->id * 37 % 1440);
            $review->updated_at = $review->created_at;
            $review->save();

            $created++;
        }
    }
}
