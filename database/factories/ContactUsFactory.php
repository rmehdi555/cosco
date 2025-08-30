<?php

namespace Database\Factories;

use App\Models\ContactUs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactUs>
 */
class ContactUsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactUs::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'body' => $this->faker->paragraph(3),
            'is_answered' => $this->faker->boolean(20), // 20% chance of being answered
        ];
    }

    /**
     * Indicate that the contact message is answered.
     */
    public function answered(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_answered' => true,
        ]);
    }

    /**
     * Indicate that the contact message is unanswered.
     */
    public function unanswered(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_answered' => false,
        ]);
    }
}
