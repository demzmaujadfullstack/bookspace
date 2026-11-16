<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'BOOKSPACE'],
            ['key' => 'site_tagline', 'value' => 'Toko Buku Online Terpercaya'],
            ['key' => 'site_email', 'value' => 'info@bookspace.id'],
            ['key' => 'site_phone', 'value' => '+62811234567'],
            ['key' => 'site_whatsapp', 'value' => '6281234567890'],
            ['key' => 'site_address', 'value' => 'Jl. Buku Raya No. 1, Jakarta Pusat, DKI Jakarta 10110'],
            ['key' => 'bank_name', 'value' => 'BCA'],
            ['key' => 'bank_account_number', 'value' => '1234567890'],
            ['key' => 'bank_account_name', 'value' => 'BOOKSPACE Indonesia'],
            ['key' => 'carousel_duration', 'value' => '5000'],
            ['key' => 'free_shipping_threshold', 'value' => '200000'],
            ['key' => 'footer_description', 'value' => 'BOOKSPACE adalah toko buku online terpercaya yang menyediakan koleksi buku terlengkap dengan harga terjangkau. Kami berkomitmen menghadirkan pengalaman berbelanja buku yang menyenangkan.'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/bookspace.id'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/bookspace_id'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/bookspace.id'],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(['key' => $data['key']], ['value' => $data['value']]);
        }
    }
}
