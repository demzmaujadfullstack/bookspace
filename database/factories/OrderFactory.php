<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => 'INV-'.fake()->unique()->numerify('######'),
            'status' => Order::MENUNGGU_PEMBAYARAN,
            'recipient_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'total' => fake()->numberBetween(50, 300) * 1000,
            'note' => null,
            'paid_at' => null,
        ];
    }
}
