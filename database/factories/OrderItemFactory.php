<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(50, 150) * 1000;
        $quantity = fake()->numberBetween(1, 2);

        return [
            'order_id' => Order::factory(),
            'book_id' => Book::factory(),
            'book_title' => fake()->words(3, true),
            'price' => $price,
            'discount_percent' => 0,
            'quantity' => $quantity,
            'subtotal' => $price * $quantity,
        ];
    }
}
