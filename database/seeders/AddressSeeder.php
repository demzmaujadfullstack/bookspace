<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Alamat pengiriman contoh untuk akun pembaca demo.
     *
     * @return array<int, array<string, string|bool>>
     */
    private function templates(): array
    {
        return [
            [
                'label' => 'Rumah',
                'recipient_name' => 'Dimas Pratama',
                'phone' => '081298765432',
                'address' => 'Jl. Merdeka No. 45, RT 02/RW 04, Menteng',
                'city' => 'Kota Administrasi Jakarta Pusat',
                'postal_code' => '10310',
                'is_default' => true,
            ],
            [
                'label' => 'Kantor',
                'recipient_name' => 'Dimas Pratama',
                'phone' => '081122334455',
                'address' => 'Gedung Cyber Block B Lt. 7, Jl. HR Rasuna Said',
                'city' => 'Kota Administrasi Jakarta Selatan',
                'postal_code' => '12950',
                'is_default' => false,
            ],
            [
                'label' => 'Pondok',
                'recipient_name' => 'Sri Lestari',
                'phone' => '085711223344',
                'address' => 'Perum Griya Asri Blok C No. 12, Candibangin',
                'city' => 'Kabupaten Semarang',
                'postal_code' => '50771',
                'is_default' => false,
            ],
        ];
    }

    public function run(): void
    {
        $reader = User::where('email', 'user@bookspace.id')->first();

        if (! $reader) {
            return;
        }

        foreach ($this->templates() as $template) {
            $address = $template['address'];
            unset($template['address']);

            Address::updateOrCreate(
                ['user_id' => $reader->id, 'address' => $address],
                $template
            );
        }

        // Pastikan hanya satu alamat utama meski seeder dijalankan berulang kali.
        $default = Address::where('user_id', $reader->id)->where('is_default', true)->orderBy('id')->first();

        Address::where('user_id', $reader->id)
            ->when($default, fn ($query) => $query->where('id', '!=', $default->id))
            ->update(['is_default' => false]);
    }
}
