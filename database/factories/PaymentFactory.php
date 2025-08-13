<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
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
        $order = Order::factory()->create();
        
        return [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => $this->faker->randomElement(PaymentMethod::cases()),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'amount' => $this->faker->randomFloat(2, 10000, 1000000),
            'merchant_id' => $this->faker->optional()->numerify('MERCH####'),
            'bank_transaction_id' => $this->faker->optional()->numerify('TXN########'),
            'bank_reference_id' => $this->faker->optional()->numerify('REF########'),
            'description' => $this->faker->optional()->sentence(),
            'callback_url' => $this->faker->optional()->url(),
            'gateway_response' => $this->faker->optional()->json([
                'status' => 'success',
                'transaction_id' => $this->faker->numerify('TXN########'),
                'reference_id' => $this->faker->numerify('REF########'),
                'amount' => $this->faker->numberBetween(10000, 1000000),
                'timestamp' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            ]),
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::COMPLETED,
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::PENDING,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::FAILED,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => PaymentMethod::ONLINE,
        ]);
    }

    /**
     * Indicate that the payment is cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => PaymentMethod::CASH,
        ]);
    }

    /**
     * Indicate that the payment is bank transfer.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => PaymentMethod::BANK_TRANSFER,
        ]);
    }
}
