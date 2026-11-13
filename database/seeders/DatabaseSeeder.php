<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@bookspace.id'],
            [
                'name' => 'Admin BOOKSPACE',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
            ]
        );
        $admin->forceFill(['role' => 'admin'])->save();

        // Demo user
        $reader = User::updateOrCreate(
            ['email' => 'user@bookspace.id'],
            [
                'name' => 'Dimas Pratama',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
            ]
        );
        $reader->forceFill(['role' => 'user'])->save();

        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            BookSeeder::class,
            BannerSeeder::class,
            BookCoverGeneratorSeeder::class,
            OrderSeeder::class,
        ]);

        // Seeder ada yang menulis massal tanpa event model, jadi segarkan cache yang dibaca tampilan.
        Category::flushNavCache();
        Setting::flushCache();
    }
}
