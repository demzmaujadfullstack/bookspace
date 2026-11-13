<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'subtitle' => fake()->sentence(),
            'image' => 'banners/factory-'.fake()->unique()->numberBetween(1, 99999).'.svg',
            'cta_text' => 'Lihat Koleksi',
            'cta_link' => '/books',
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
