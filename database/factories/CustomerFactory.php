<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->unique()->name(),
            'customer_address' => fake()->optional()->streetAddress(),
            'customer_email' => fake()->optional()->safeEmail(),
            'customer_phone' => fake()->optional()->phoneNumber()
        ];
    }
}
