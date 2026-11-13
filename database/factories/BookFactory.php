<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'title' => ucwords($title),
            'slug' => Str::slug($title),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'isbn' => fake()->unique()->isbn13(),
            'year' => fake()->numberBetween(2005, 2026),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->numberBetween(45, 180) * 1000,
            'discount_percent' => fake()->randomElement([0, 0, 0, 10, 15, 20]),
            'stock' => fake()->numberBetween(0, 30),
            'cover' => null,
            'is_featured' => fake()->boolean(25),
        ];
    }
}
