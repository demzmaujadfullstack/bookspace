<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'method' => 'transfer',
            'amount' => fake()->numberBetween(50, 300) * 1000,
            'status' => Payment::MENUNGGU,
            'proof_image' => null,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'PT BOOKSPACE INDONESIA',
            'note' => null,
            'verified_at' => null,
            'verified_by' => null,
        ];
    }
}
