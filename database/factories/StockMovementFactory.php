<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'order_id' => null,
            'user_id' => null,
            'type' => 'masuk',
            'quantity' => 10,
            'stock_after' => 10,
            'reason' => 'Stok awal',
        ];
    }
}
