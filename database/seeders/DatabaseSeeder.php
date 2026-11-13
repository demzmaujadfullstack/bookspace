<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@bookspace.id'],
            [
                'name' => 'Admin BOOKSPACE',
                'email' => 'admin@bookspace.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        // Demo user
        User::updateOrCreate(
            ['email' => 'user@bookspace.id'],
            [
                'name' => 'Dimas Pratama',
                'email' => 'user@bookspace.id',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081298765432',
            ]
        );

        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            BookSeeder::class,
            BannerSeeder::class,
            BookCoverGeneratorSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
